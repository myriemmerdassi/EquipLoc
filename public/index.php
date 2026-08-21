<?php
/**
 * Front Controller & Routeur de l'application EquipLoc
 * PHP 8 - Architecture MVC Natifs
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

// Auto-chargement simple des contrôleurs
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/EquipementController.php';
require_once __DIR__ . '/../controllers/CategorieController.php';
require_once __DIR__ . '/../controllers/LocationController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/PdfController.php';

// Récupération de l'action demandée
$action = sanitize($_GET['action'] ?? 'catalogue');

// Routage dynamique
switch ($action) {
    // --- FrontOffice Public & Auth ---
    case 'catalogue':
        (new EquipementController())->catalogue();
        break;
    case 'details':
        (new EquipementController())->show();
        break;
    case 'login':
        (new AuthController())->login();
        break;
    case 'register':
        (new AuthController())->register();
        break;
    case 'logout':
        (new AuthController())->logout();
        break;

    // --- Espace Client ---
    case 'reserve':
        (new LocationController())->reserve();
        break;
    case 'mes_locations':
        (new LocationController())->myRentals();
        break;

    // --- BackOffice Admin / Agent / Responsable ---
    case 'dashboard':
        (new DashboardController())->index();
        break;

    // Gestion Équipements
    case 'equipements_admin':
        (new EquipementController())->backofficeIndex();
        break;
    case 'equipement_create':
        (new EquipementController())->create();
        break;
    case 'equipement_edit':
        (new EquipementController())->edit();
        break;
    case 'equipement_delete':
        (new EquipementController())->delete();
        break;

    // Gestion Catégories
    case 'categories':
        (new CategorieController())->index();
        break;
    case 'categorie_create':
        (new CategorieController())->create();
        break;
    case 'categorie_edit':
        (new CategorieController())->edit();
        break;
    case 'categorie_delete':
        (new CategorieController())->delete();
        break;

    // Gestion Locations & Comptoir
    case 'locations_admin':
        (new LocationController())->indexAdmin();
        break;
    case 'location_comptoir':
        (new LocationController())->createCounterRental();
        break;
    case 'location_status':
        (new LocationController())->updateStatus();
        break;
    case 'location_retour':
        (new LocationController())->registerReturn();
        break;

    // Gestion Utilisateurs
    case 'users_admin':
        (new UserController())->index();
        break;
    case 'user_create':
        (new UserController())->create();
        break;
    case 'user_edit':
        (new UserController())->edit();
        break;
    case 'user_delete':
        (new UserController())->delete();
        break;

    // Génération PDF Documents
    case 'pdf_contrat':
        (new PdfController())->contract();
        break;
    case 'pdf_facture':
        (new PdfController())->invoice();
        break;
    case 'pdf_recu':
        (new PdfController())->receipt();
        break;

    default:
        (new EquipementController())->catalogue();
        break;
}
