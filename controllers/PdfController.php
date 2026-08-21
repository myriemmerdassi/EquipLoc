<?php
/**
 * Contrôleur Génération PDF (Contrat, Facture, Reçu)
 * Imprimable et téléchargeable au format PDF (Exigence du sujet)
 */

require_once __DIR__ . '/../models/Location.php';
require_once __DIR__ . '/../config/helpers.php';

class PdfController {
    private Location $locationModel;

    public function __construct() {
        if (!isLoggedIn()) {
            redirect('/index.php?action=login');
        }
        $this->locationModel = new Location();
    }

    /**
     * Génère le Contrat de Location
     */
    public function contract(): void {
        $id = (int)($_GET['id'] ?? 0);
        $location = $this->locationModel->findById($id);

        $this->checkAccessPermission($location);

        $docType = 'CONTRAT DE LOCATION';
        require_once __DIR__ . '/../views/pdf/contract.php';
    }

    /**
     * Génère la Facture
     */
    public function invoice(): void {
        $id = (int)($_GET['id'] ?? 0);
        $location = $this->locationModel->findById($id);

        $this->checkAccessPermission($location);

        $docType = 'FACTURE DE LOCATION';
        require_once __DIR__ . '/../views/pdf/invoice.php';
    }

    /**
     * Génère le Reçu de Règlement / Retour
     */
    public function receipt(): void {
        $id = (int)($_GET['id'] ?? 0);
        $location = $this->locationModel->findById($id);

        $this->checkAccessPermission($location);

        $docType = 'REÇU DE PAIEMENT & RESTITUTION';
        require_once __DIR__ . '/../views/pdf/receipt.php';
    }

    /**
     * Vérification de sécurité : Le client ne peut voir que SES contrats/factures
     */
    private function checkAccessPermission(?array $location): void {
        if (!$location) {
            setFlash('danger', 'Document introuvable.');
            redirect('/index.php');
        }

        $user = currentUser();
        if ($user['role'] === ROLE_CLIENT && (int)$location['id_client'] !== (int)$user['id_utilisateur']) {
            setFlash('danger', 'Accès non autorisé à ce document.');
            redirect('/index.php');
        }
    }
}
