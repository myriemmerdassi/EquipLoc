<?php
/**
 * Modèle Location
 * Gère les réservations, calculs de prix, statut, et retours d'équipements.
 * Utilise des jointures SQL complexes (JOIN utilisateur, equipement, categorie)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Equipement.php';

class Location {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Récupère toutes les locations avec de multiples JOINs SQL
     */
    public function getAll(?int $idClient = null): array {
        $sql = "SELECT l.*, 
                       e.nom_equipement, e.prix_par_jour, e.image,
                       cat.nom_categorie,
                       client.nom AS client_nom, client.prenom AS client_prenom, client.email AS client_email, client.telephone AS client_telephone,
                       agent.nom AS agent_nom, agent.prenom AS agent_prenom
                FROM location l
                JOIN equipement e ON l.id_equipement = e.id_equipement
                JOIN categorie cat ON e.id_categorie = cat.id_categorie
                JOIN utilisateur client ON l.id_client = client.id_utilisateur
                LEFT JOIN utilisateur agent ON l.id_agent = agent.id_utilisateur";
        
        $params = [];
        if ($idClient !== null) {
            $sql .= " WHERE l.id_client = :id_client";
            $params['id_client'] = $idClient;
        }

        $sql .= " ORDER BY l.id_location DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Récupère une location par son ID avec toutes ses jointures
     */
    public function findById(int $id): ?array {
        $sql = "SELECT l.*, 
                       e.nom_equipement, e.prix_par_jour, e.image, e.id_equipement, e.stock AS equipement_stock,
                       cat.nom_categorie,
                       client.nom AS client_nom, client.prenom AS client_prenom, client.email AS client_email, client.telephone AS client_telephone,
                       agent.nom AS agent_nom, agent.prenom AS agent_prenom
                FROM location l
                JOIN equipement e ON l.id_equipement = e.id_equipement
                JOIN categorie cat ON e.id_categorie = cat.id_categorie
                JOIN utilisateur client ON l.id_client = client.id_utilisateur
                LEFT JOIN utilisateur agent ON l.id_agent = agent.id_utilisateur
                WHERE l.id_location = :id
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Création d'une nouvelle location
     */
    public function create(array $data): bool {
        // Vérification de la durée en jours
        $dStart = new DateTime($data['date_debut']);
        $dEnd   = new DateTime($data['date_fin']);
        $interval = $dStart->diff($dEnd);
        $dureeJours = max(1, $interval->days);

        // Calcul du montant total de base
        $equipementModel = new Equipement();
        $equipement = $equipementModel->findById((int)$data['id_equipement']);
        if (!$equipement || $equipement['stock'] < (int)$data['quantite']) {
            return false;
        }

        $quantite = (int)($data['quantite'] ?? 1);
        $montantTotal = $dureeJours * (float)$equipement['prix_par_jour'] * $quantite;

        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO location (id_client, id_agent, id_equipement, quantite, date_debut, date_fin, duree_jours, montant_total, statut) 
                    VALUES (:id_client, :id_agent, :id_equipement, :quantite, :date_debut, :date_fin, :duree_jours, :montant_total, :statut)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'id_client'      => $data['id_client'],
                'id_agent'       => $data['id_agent'] ?? null,
                'id_equipement'  => $data['id_equipement'],
                'quantite'       => $quantite,
                'date_debut'     => $data['date_debut'],
                'date_fin'       => $data['date_fin'],
                'duree_jours'    => $dureeJours,
                'montant_total'  => $montantTotal,
                'statut'         => $data['statut'] ?? 'En attente'
            ]);

            // Décrémenter le stock si la location est immédiatement validée
            if (($data['statut'] ?? '') === 'Validée' || ($data['statut'] ?? '') === 'En cours') {
                $equipementModel->updateStockAndState($equipement['id_equipement'], -$quantite);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Changer le statut d'une location (Validée, En cours, Annulée, etc.)
     */
    public function updateStatus(int $idLocation, string $statut, ?int $idAgent = null): bool {
        $location = $this->findById($idLocation);
        if (!$location) return false;

        $sql = "UPDATE location SET statut = :statut";
        $params = ['statut' => $statut, 'id' => $idLocation];

        if ($idAgent !== null) {
            $sql .= ", id_agent = :id_agent";
            $params['id_agent'] = $idAgent;
        }

        $sql .= " WHERE id_location = :id";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($params);

        // Si la location passe à Validée/En cours, décrémenter le stock
        if ($result && in_array($statut, ['Validée', 'En cours']) && !in_array($location['statut'], ['Validée', 'En cours'])) {
            $equipementModel = new Equipement();
            $equipementModel->updateStockAndState((int)$location['id_equipement'], -(int)$location['quantite']);
        }

        return $result;
    }

    /**
     * Traiter le retour d'un matériel (Par l'agent de location ou le responsable)
     * Ajout de frais supplémentaires et enregistrement de l'état du matériel rendu
     */
    public function registerReturn(int $idLocation, string $etatRetour, float $fraisSupp = 0.00, ?string $motifFrais = null, ?int $idAgent = null): bool {
        $location = $this->findById($idLocation);
        if (!$location) return false;

        $this->db->beginTransaction();

        try {
            $sql = "UPDATE location 
                    SET statut = 'Terminée', 
                        etat_retour = :etat_retour, 
                        frais_supplementaires = :frais, 
                        motif_frais = :motif,
                        id_agent = COALESCE(:id_agent, id_agent) 
                    WHERE id_location = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'id'          => $idLocation,
                'etat_retour' => $etatRetour,
                'frais'       => $fraisSupp,
                'motif'       => $motifFrais,
                'id_agent'    => $idAgent
            ]);

            // Mise à jour de l'état et ré-incrémentation du stock de l'équipement
            $equipementModel = new Equipement();
            
            // Si l'équipement est fonctionnel ('Disponible'), le stock augmente.
            // S'il est endommagé ou en maintenance, on ajuste son état dans la table equipement.
            $equipementModel->updateStockAndState(
                (int)$location['id_equipement'], 
                ($etatRetour === 'Disponible' ? (int)$location['quantite'] : 0), 
                $etatRetour
            );

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM location WHERE id_location = :id");
        return $stmt->execute(['id' => $id]);
    }
}
