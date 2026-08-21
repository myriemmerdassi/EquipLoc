<?php
/**
 * Contrôleur Équipement
 * Gestion du Stock, CRUD, Seuil d'Alerte, Image Upload, et Recherche Multicritère
 */

require_once __DIR__ . '/../models/Equipement.php';
require_once __DIR__ . '/../models/Categorie.php';
require_once __DIR__ . '/../config/helpers.php';

class EquipementController {
    private Equipement $equipementModel;
    private Categorie $categorieModel;

    public function __construct() {
        $this->equipementModel = new Equipement();
        $this->categorieModel = new Categorie();
    }

    /**
     * Catalogue Public & Recherche Multicritère (FrontOffice Client + BackOffice)
     */
    public function catalogue(): void {
        $filters = [
            'query'        => sanitize($_GET['q'] ?? ''),
            'id_categorie' => (int)($_GET['categorie'] ?? 0),
            'prix_min'     => (float)($_GET['prix_min'] ?? 0),
            'prix_max'     => (float)($_GET['prix_max'] ?? 0),
            'etat'         => sanitize($_GET['etat'] ?? '')
        ];

        $equipements = $this->equipementModel->search($filters);
        $categories  = $this->categorieModel->getAll();

        require_once __DIR__ . '/../views/frontoffice/catalogue.php';
    }

    /**
     * Fiche Détails d'un équipement
     */
    public function show(): void {
        $id = (int)($_GET['id'] ?? 0);
        $equipement = $this->equipementModel->findById($id);

        if (!$equipement) {
            setFlash('danger', 'Équipement non trouvé.');
            redirect('/index.php?action=catalogue');
        }

        require_once __DIR__ . '/../views/frontoffice/details.php';
    }

    /**
     * Liste du BackOffice (Gestion des Stocks & Alertes)
     */
    public function backofficeIndex(): void {
        requireRole(ROLE_RESPONSABLE, ROLE_AGENT);

        $equipements = $this->equipementModel->getAll();
        $alertesStock = $this->equipementModel->getLowStockAlerts();

        require_once __DIR__ . '/../views/backoffice/equipements/index.php';
    }

    /**
     * Création d'un équipement (Responsable Inventaire)
     */
    public function create(): void {
        requireRole(ROLE_RESPONSABLE);

        $categories = $this->categorieModel->getAll();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom_equipement' => sanitize($_POST['nom_equipement'] ?? ''),
                'description'    => sanitize($_POST['description'] ?? ''),
                'prix_par_jour'  => (float)($_POST['prix_par_jour'] ?? 0),
                'stock'          => (int)($_POST['stock'] ?? 0),
                'seuil_alerte'   => (int)($_POST['seuil_alerte'] ?? 5),
                'etat'           => sanitize($_POST['etat'] ?? 'Disponible'),
                'id_categorie'   => (int)($_POST['id_categorie'] ?? 0),
                'image'          => 'default_equipement.png'
            ];

            // Validations PHP & JS
            if (empty($data['nom_equipement'])) {
                $error = "Le nom de l'équipement est requis.";
            } elseif ($data['prix_par_jour'] <= 0) {
                $error = "Le prix par jour doit être un nombre positif.";
            } elseif ($data['stock'] < 0) {
                $error = "Le stock ne peut pas être négatif.";
            } elseif ($data['id_categorie'] <= 0) {
                $error = "Veuillez sélectionner une catégorie.";
            } else {
                // Traitement Upload Image
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = $this->handleImageUpload($_FILES['image']);
                    if (str_starts_with($uploadResult, 'error:')) {
                        $error = substr($uploadResult, 6);
                    } else {
                        $data['image'] = $uploadResult;
                    }
                }

                if (!$error) {
                    if ($this->equipementModel->create($data)) {
                        setFlash('success', 'Équipement ajouté à l\'inventaire avec succès.');
                        redirect('/index.php?action=equipements_admin');
                    } else {
                        $error = "Erreur lors de l'enregistrement en base de données.";
                    }
                }
            }
        }

        require_once __DIR__ . '/../views/backoffice/equipements/form.php';
    }

    /**
     * Modification d'un équipement
     */
    public function edit(): void {
        requireRole(ROLE_RESPONSABLE);

        $id = (int)($_GET['id'] ?? 0);
        $equipement = $this->equipementModel->findById($id);

        if (!$equipement) {
            setFlash('danger', 'Équipement introuvable.');
            redirect('/index.php?action=equipements_admin');
        }

        $categories = $this->categorieModel->getAll();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom_equipement' => sanitize($_POST['nom_equipement'] ?? ''),
                'description'    => sanitize($_POST['description'] ?? ''),
                'prix_par_jour'  => (float)($_POST['prix_par_jour'] ?? 0),
                'stock'          => (int)($_POST['stock'] ?? 0),
                'seuil_alerte'   => (int)($_POST['seuil_alerte'] ?? 5),
                'etat'           => sanitize($_POST['etat'] ?? 'Disponible'),
                'id_categorie'   => (int)($_POST['id_categorie'] ?? 0),
                'image'          => $equipement['image']
            ];

            if (empty($data['nom_equipement']) || $data['prix_par_jour'] <= 0 || $data['stock'] < 0) {
                $error = "Informations invalides (prix positif, stock >= 0 requis).";
            } else {
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = $this->handleImageUpload($_FILES['image']);
                    if (str_starts_with($uploadResult, 'error:')) {
                        $error = substr($uploadResult, 6);
                    } else {
                        $data['image'] = $uploadResult;
                    }
                }

                if (!$error) {
                    if ($this->equipementModel->update($id, $data)) {
                        setFlash('success', 'Équipement mis à jour avec succès.');
                        redirect('/index.php?action=equipements_admin');
                    } else {
                        $error = "Erreur lors de la mise à jour.";
                    }
                }
            }
        }

        require_once __DIR__ . '/../views/backoffice/equipements/form.php';
    }

    /**
     * Suppression
     */
    public function delete(): void {
        requireRole(ROLE_RESPONSABLE);
        $id = (int)($_GET['id'] ?? 0);
        
        if ($this->equipementModel->delete($id)) {
            setFlash('success', 'Équipement supprimé.');
        } else {
            setFlash('danger', 'Impossible de supprimer cet équipement (des locations y sont rattachées).');
        }
        redirect('/index.php?action=equipements_admin');
    }

    /**
     * Gestionnaire d'Upload d'images sécurisé
     */
    private function handleImageUpload(array $file): string {
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExts, true)) {
            return "error:Format d'image non autorisé (JPG, PNG, WEBP acceptés).";
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
            return "error:L'image dépasse la taille maximale autorisée (5 Mo).";
        }

        if (!is_dir(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0777, true);
        }

        $filename = 'eq_' . uniqid() . '.' . $ext;
        $destination = UPLOAD_PATH . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }

        return "error:Échec de l'enregistrement du fichier téléchargé.";
    }
}
