<?php
/**
 * Modèle Équipement
 * Interaction PDO avec la table `equipement` et jointure sur `categorie`
 */

require_once __DIR__ . '/../config/database.php';

class Equipement {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Récupère tous les équipements avec la jointure de leur catégorie
     */
    public function getAll(): array {
        $sql = "SELECT e.*, c.nom_categorie 
                FROM equipement e 
                JOIN categorie c ON e.id_categorie = c.id_categorie 
                ORDER BY e.id_equipement DESC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Recherche d'un équipement par ID avec sa catégorie
     */
    public function findById(int $id): ?array {
        $sql = "SELECT e.*, c.nom_categorie 
                FROM equipement e 
                JOIN categorie c ON e.id_categorie = c.id_categorie 
                WHERE e.id_equipement = :id 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Recherche multicritère d'équipements (Exigence du sujet)
     */
    public function search(array $filters): array {
        $sql = "SELECT e.*, c.nom_categorie 
                FROM equipement e 
                JOIN categorie c ON e.id_categorie = c.id_categorie 
                WHERE 1=1";
        $params = [];

        if (!empty($filters['query'])) {
            $sql .= " AND (e.nom_equipement LIKE :query OR e.description LIKE :query)";
            $params['query'] = '%' . $filters['query'] . '%';
        }

        if (!empty($filters['id_categorie'])) {
            $sql .= " AND e.id_categorie = :id_categorie";
            $params['id_categorie'] = (int)$filters['id_categorie'];
        }

        if (!empty($filters['prix_min'])) {
            $sql .= " AND e.prix_par_jour >= :prix_min";
            $params['prix_min'] = (float)$filters['prix_min'];
        }

        if (!empty($filters['prix_max'])) {
            $sql .= " AND e.prix_par_jour <= :prix_max";
            $params['prix_max'] = (float)$filters['prix_max'];
        }

        if (!empty($filters['etat'])) {
            $sql .= " AND e.etat = :etat";
            $params['etat'] = $filters['etat'];
        }

        if (isset($filters['seul_disponible']) && $filters['seul_disponible']) {
            $sql .= " AND e.stock > 0 AND e.etat = 'Disponible'";
        }

        $sql .= " ORDER BY e.id_equipement DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Équipements en alerte de stock (stock <= seuil_alerte)
     */
    public function getLowStockAlerts(): array {
        $sql = "SELECT e.*, c.nom_categorie 
                FROM equipement e 
                JOIN categorie c ON e.id_categorie = c.id_categorie 
                WHERE e.stock <= e.seuil_alerte 
                ORDER BY e.stock ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function create(array $data): bool {
        $sql = "INSERT INTO equipement (nom_equipement, description, prix_par_jour, stock, seuil_alerte, etat, image, id_categorie) 
                VALUES (:nom, :desc, :prix, :stock, :seuil, :etat, :image, :id_categorie)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nom'          => $data['nom_equipement'],
            'desc'         => $data['description'] ?? null,
            'prix'         => $data['prix_par_jour'],
            'stock'        => $data['stock'],
            'seuil'        => $data['seuil_alerte'],
            'etat'         => $data['etat'] ?? 'Disponible',
            'image'        => $data['image'] ?? 'default_equipement.png',
            'id_categorie' => $data['id_categorie']
        ]);
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE equipement 
                SET nom_equipement = :nom, description = :desc, prix_par_jour = :prix, 
                    stock = :stock, seuil_alerte = :seuil, etat = :etat, image = :image, id_categorie = :id_categorie 
                WHERE id_equipement = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'           => $id,
            'nom'          => $data['nom_equipement'],
            'desc'         => $data['description'] ?? null,
            'prix'         => $data['prix_par_jour'],
            'stock'        => $data['stock'],
            'seuil'        => $data['seuil_alerte'],
            'etat'         => $data['etat'],
            'image'        => $data['image'],
            'id_categorie' => $data['id_categorie']
        ]);
    }

    /**
     * Mise à jour spécifique du stock et de l'état (ex: après un retour ou une location)
     */
    public function updateStockAndState(int $id, int $stockDelta, ?string $etat = null): bool {
        $equipement = $this->findById($id);
        if (!$equipement) return false;

        $newStock = max(0, $equipement['stock'] + $stockDelta);
        $newEtat = $etat ?? ($newStock === 0 ? 'En location' : $equipement['etat']);

        $sql = "UPDATE equipement SET stock = :stock, etat = :etat WHERE id_equipement = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'stock' => $newStock,
            'etat' => $newEtat
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM equipement WHERE id_equipement = :id");
        return $stmt->execute(['id' => $id]);
    }
}
