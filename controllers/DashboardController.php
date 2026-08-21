<?php
/**
 * Contrôleur du Tableau de Bord (Dashboard BackOffice)
 * Statistiques, alertes de stock et vue synthétique pour Responsable & Agent
 */

require_once __DIR__ . '/../models/Equipement.php';
require_once __DIR__ . '/../models/Location.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/helpers.php';

class DashboardController {
    private Equipement $equipementModel;
    private Location $locationModel;
    private User $userModel;

    public function __construct() {
        requireRole(ROLE_RESPONSABLE, ROLE_AGENT);
        $this->equipementModel = new Equipement();
        $this->locationModel = new Location();
        $this->userModel = new User();
    }

    public function index(): void {
        $equipements   = $this->equipementModel->getAll();
        $alertesStock  = $this->equipementModel->getLowStockAlerts();
        $locations     = $this->locationModel->getAll();
        $totalUsers    = $this->userModel->count();

        // Calculs statistiques
        $totalEquipements = count($equipements);
        $totalLocations   = count($locations);
        $locationsAttente = count(array_filter($locations, fn($l) => $l['statut'] === 'En attente'));
        $locationsEnCours = count(array_filter($locations, fn($l) => in_array($l['statut'], ['Validée', 'En cours'])));

        // Revenu total estimé
        $chiffreAffaires = array_reduce($locations, function($sum, $l) {
            return $sum + ($l['statut'] !== 'Annulée' ? ($l['montant_total'] + $l['frais_supplementaires']) : 0);
        }, 0.0);

        require_once __DIR__ . '/../views/backoffice/dashboard.php';
    }
}
