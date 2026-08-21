<?php
/**
 * Contrôleur des Catégories
 * Réservé au Responsable Inventaire & Agent
 */

require_once __DIR__ . '/../models/Categorie.php';
require_once __DIR__ . '/../config/helpers.php';

class CategorieController {
    private Categorie $categorieModel;

    public function __construct() {
        requireRole(ROLE_RESPONSABLE, ROLE_AGENT);
        $this->categorieModel = new Categorie();
    }

    public function index(): void {
        $categories = $this->categorieModel->getAll();
        require_once __DIR__ . '/../views/backoffice/categories/index.php';
    }

    public function create(): void {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = sanitize($_POST['nom_categorie'] ?? '');
            $desc = sanitize($_POST['description'] ?? '');

            if (empty($nom)) {
                $error = "Le nom de la catégorie est obligatoire.";
            } else {
                if ($this->categorieModel->create(['nom_categorie' => $nom, 'description' => $desc])) {
                    setFlash('success', 'Catégorie ajoutée avec succès.');
                    redirect('/index.php?action=categories');
                } else {
                    $error = "Erreur lors de la création (le nom existe peut-être déjà).";
                }
            }
        }
        require_once __DIR__ . '/../views/backoffice/categories/form.php';
    }

    public function edit(): void {
        $id = (int)($_GET['id'] ?? 0);
        $categorie = $this->categorieModel->findById($id);

        if (!$categorie) {
            setFlash('danger', 'Catégorie introuvable.');
            redirect('/index.php?action=categories');
        }

        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = sanitize($_POST['nom_categorie'] ?? '');
            $desc = sanitize($_POST['description'] ?? '');

            if (empty($nom)) {
                $error = "Le nom de la catégorie est obligatoire.";
            } else {
                if ($this->categorieModel->update($id, ['nom_categorie' => $nom, 'description' => $desc])) {
                    setFlash('success', 'Catégorie modifiée avec succès.');
                    redirect('/index.php?action=categories');
                } else {
                    $error = "Erreur lors de la modification.";
                }
            }
        }
        require_once __DIR__ . '/../views/backoffice/categories/form.php';
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($this->categorieModel->delete($id)) {
            setFlash('success', 'Catégorie supprimée avec succès.');
        } else {
            setFlash('danger', 'Impossible de supprimer cette catégorie (elle contient probablement des équipements).');
        }
        redirect('/index.php?action=categories');
    }
}
