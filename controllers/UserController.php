<?php
/**
 * Contrôleur Utilisateur (Gestion des Comptes BackOffice)
 * Réservé au Responsable Inventaire
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/helpers.php';

class UserController {
    private User $userModel;

    public function __construct() {
        requireRole(ROLE_RESPONSABLE);
        $this->userModel = new User();
    }

    public function index(): void {
        $users = $this->userModel->getAll();
        require_once __DIR__ . '/../views/backoffice/users/index.php';
    }

    public function create(): void {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom'          => sanitize($_POST['nom'] ?? ''),
                'prenom'       => sanitize($_POST['prenom'] ?? ''),
                'email'        => sanitize($_POST['email'] ?? ''),
                'mot_de_passe' => $_POST['mot_de_passe'] ?? '',
                'telephone'    => sanitize($_POST['telephone'] ?? ''),
                'role'         => sanitize($_POST['role'] ?? ROLE_CLIENT)
            ];

            if (empty($data['nom']) || empty($data['prenom']) || empty($data['email'])) {
                $error = "Champs obligatoires manquants.";
            } elseif ($this->userModel->findByEmail($data['email'])) {
                $error = "Cet email est déjà enregistré.";
            } else {
                if ($this->userModel->create($data)) {
                    setFlash('success', 'Utilisateur créé avec succès.');
                    redirect('/index.php?action=users_admin');
                } else {
                    $error = "Erreur lors de la création de l'utilisateur.";
                }
            }
        }
        require_once __DIR__ . '/../views/backoffice/users/form.php';
    }

    public function edit(): void {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->userModel->findById($id);

        if (!$user) {
            setFlash('danger', 'Utilisateur introuvable.');
            redirect('/index.php?action=users_admin');
        }

        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom'          => sanitize($_POST['nom'] ?? ''),
                'prenom'       => sanitize($_POST['prenom'] ?? ''),
                'email'        => sanitize($_POST['email'] ?? ''),
                'mot_de_passe' => $_POST['mot_de_passe'] ?? '',
                'telephone'    => sanitize($_POST['telephone'] ?? ''),
                'role'         => sanitize($_POST['role'] ?? $user['role'])
            ];

            if ($this->userModel->update($id, $data)) {
                setFlash('success', 'Utilisateur mis à jour avec succès.');
                redirect('/index.php?action=users_admin');
            } else {
                $error = "Erreur lors de la mise à jour.";
            }
        }
        require_once __DIR__ . '/../views/backoffice/users/form.php';
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        // Empêcher l'auto-suppression
        if ($id === $_SESSION['user']['id_utilisateur']) {
            setFlash('danger', 'Vous ne pouvez pas supprimer votre propre compte connecté !');
        } else {
            if ($this->userModel->delete($id)) {
                setFlash('success', 'Utilisateur supprimé.');
            } else {
                setFlash('danger', 'Impossible de supprimer cet utilisateur (il a des locations enregistrées).');
            }
        }
        redirect('/index.php?action=users_admin');
    }
}
