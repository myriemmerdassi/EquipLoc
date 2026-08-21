<?php
/**
 * Contrôleur Location
 * Réservation Client, Gestion Comptoir Agent, Validation Retours & Frais
 */

require_once __DIR__ . '/../models/Location.php';
require_once __DIR__ . '/../models/Equipement.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/helpers.php';

class LocationController {
    private Location $locationModel;
    private Equipement $equipementModel;
    private User $userModel;

    public function __construct() {
        $this->locationModel = new Location();
        $this->equipementModel = new Equipement();
        $this->userModel = new User();
    }

    /**
     * Espace Personnel Client : Liste de ses locations
     */
    public function myRentals(): void {
        requireRole(ROLE_CLIENT);
        $idClient = $_SESSION['user']['id_utilisateur'];
        $locations = $this->locationModel->getAll($idClient);
        
        require_once __DIR__ . '/../views/frontoffice/my_rentals.php';
    }

    /**
     * Demande de réservation Client (depuis le catalogue)
     */
    public function reserve(): void {
        requireRole(ROLE_CLIENT);

        $idEquipement = (int)($_GET['id_equipement'] ?? $_POST['id_equipement'] ?? 0);
        $equipement   = $this->equipementModel->findById($idEquipement);

        if (!$equipement) {
            setFlash('danger', 'Équipement non trouvé.');
            redirect('/index.php?action=catalogue');
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin   = $_POST['date_fin'] ?? '';
            $quantite  = (int)($_POST['quantite'] ?? 1);

            $today = date('Y-m-d');

            // Validations PHP & JS
            if (empty($dateDebut) || empty($dateFin)) {
                $error = "Veuillez sélectionner les dates de début et de fin.";
            } elseif ($dateDebut < $today) {
                $error = "La date de début ne peut pas être dans le passé.";
            } elseif ($dateFin < $dateDebut) {
                $error = "La date de fin doit être égale ou postérieure à la date de début.";
            } elseif ($quantite <= 0) {
                $error = "La quantité doit être supérieure à zéro.";
            } elseif ($quantite > $equipement['stock']) {
                $error = "Quantité indisponible en stock (Stock disponible : {$equipement['stock']}).";
            } else {
                $success = $this->locationModel->create([
                    'id_client'     => $_SESSION['user']['id_utilisateur'],
                    'id_equipement' => $idEquipement,
                    'quantite'      => $quantite,
                    'date_debut'    => $dateDebut,
                    'date_fin'      => $dateFin,
                    'statut'        => 'En attente'
                ]);

                if ($success) {
                    setFlash('success', 'Votre demande de location a été envoyée avec succès ! Un agent la validera très rapidement.');
                    redirect('/index.php?action=mes_locations');
                } else {
                    $error = "Une erreur est survenue lors de la création de la réservation.";
                }
            }
        }

        require_once __DIR__ . '/../views/frontoffice/reserve.php';
    }

    /**
     * Liste Globale des Locations (BackOffice Agent / Responsable)
     */
    public function indexAdmin(): void {
        requireRole(ROLE_RESPONSABLE, ROLE_AGENT);

        $locations = $this->locationModel->getAll();
        require_once __DIR__ . '/../views/backoffice/locations/index.php';
    }

    /**
     * Agent de Location : Création directe d'une location au comptoir
     */
    public function createCounterRental(): void {
        requireRole(ROLE_RESPONSABLE, ROLE_AGENT);

        $clients     = $this->userModel->getAll(ROLE_CLIENT);
        $equipements = $this->equipementModel->getAll();
        $error       = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id_client'     => (int)($_POST['id_client'] ?? 0),
                'id_agent'      => $_SESSION['user']['id_utilisateur'],
                'id_equipement' => (int)($_POST['id_equipement'] ?? 0),
                'quantite'      => (int)($_POST['quantite'] ?? 1),
                'date_debut'    => $_POST['date_debut'] ?? '',
                'date_fin'      => $_POST['date_fin'] ?? '',
                'statut'        => 'Validée'
            ];

            if ($data['id_client'] <= 0 || $data['id_equipement'] <= 0) {
                $error = "Veuillez sélectionner un client et un équipement.";
            } elseif ($data['date_fin'] < $data['date_debut']) {
                $error = "Dates incohérentes (date fin < date début).";
            } else {
                if ($this->locationModel->create($data)) {
                    setFlash('success', 'Location au comptoir créée et validée avec succès.');
                    redirect('/index.php?action=locations_admin');
                } else {
                    $error = "Erreur lors de la création (Vérifiez le stock disponible).";
                }
            }
        }

        require_once __DIR__ . '/../views/backoffice/locations/create_counter.php';
    }

    /**
     * Validation du statut par l'Agent (Validation / Annulation)
     */
    public function updateStatus(): void {
        requireRole(ROLE_RESPONSABLE, ROLE_AGENT);

        $id = (int)($_GET['id'] ?? 0);
        $statut = sanitize($_GET['statut'] ?? '');

        if (in_array($statut, ['Validée', 'En cours', 'Annulée'])) {
            $agentId = $_SESSION['user']['id_utilisateur'];
            if ($this->locationModel->updateStatus($id, $statut, $agentId)) {
                setFlash('success', "Le statut de la location n'a été mis à jour : {$statut}.");
            } else {
                setFlash('danger', "Erreur lors du changement de statut.");
            }
        }

        redirect('/index.php?action=locations_admin');
    }

    /**
     * Enregistrement du Retour Matériel & Diagnostic (Responsable + Agent)
     * Inspection : cassé, rayé, fonctionnel -> état final + frais additionnels
     */
    public function registerReturn(): void {
        requireRole(ROLE_RESPONSABLE, ROLE_AGENT);

        $id = (int)($_GET['id'] ?? $_POST['id_location'] ?? 0);
        $location = $this->locationModel->findById($id);

        if (!$location) {
            setFlash('danger', 'Location introuvable.');
            redirect('/index.php?action=locations_admin');
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $etatRetour = sanitize($_POST['etat_retour'] ?? 'Disponible');
            $fraisSupp  = (float)($_POST['frais_supplementaires'] ?? 0);
            $motifFrais = sanitize($_POST['motif_frais'] ?? '');
            $agentId    = $_SESSION['user']['id_utilisateur'];

            if ($this->locationModel->registerReturn($id, $etatRetour, $fraisSupp, $motifFrais, $agentId)) {
                setFlash('success', 'Retour matériel enregistré avec succès ! Le stock a été mis à jour.');
                redirect('/index.php?action=locations_admin');
            } else {
                $error = "Erreur lors de la validation du retour.";
            }
        }

        require_once __DIR__ . '/../views/backoffice/locations/return_form.php';
    }
}
