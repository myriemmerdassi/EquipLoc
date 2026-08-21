<?php
/**
 * Contrôleur d'Authentification
 * Connexion, Inscription et Déconnexion
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/helpers.php';

class AuthController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Formulaire de connexion & Traitement POST
     */
    public function login(): void {
        if (isLoggedIn()) {
            redirect('/index.php');
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = strtolower(trim(sanitize($_POST['email'] ?? '')));
            $password = $_POST['mot_de_passe'] ?? '';

            // Validation JS/PHP
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Veuillez saisir une adresse email valide.";
            } elseif (empty($password)) {
                $error = "Veuillez saisir votre mot de passe.";
            } else {
                $user = $this->userModel->findByEmail($email);
                if (!$user) {
                    $error = "Aucune adresse email '" . htmlspecialchars($email) . "' trouvée dans la base de données.";
                } elseif (!password_verify($password, $user['mot_de_passe'])) {
                    $error = "Mot de passe incorrect pour l'utilisateur '" . htmlspecialchars($email) . "'.";
                } else {
                    // Démarrage session utilisateur
                    $_SESSION['user'] = [
                        'id_utilisateur' => $user['id_utilisateur'],
                        'nom'            => $user['nom'],
                        'prenom'         => $user['prenom'],
                        'email'          => $user['email'],
                        'role'           => $user['role'],
                        'telephone'      => $user['telephone']
                    ];
                    setFlash('success', 'Bienvenue ' . htmlspecialchars($user['prenom']) . ' !');
                    
                    // Redirection selon le rôle
                    if (in_array($user['role'], [ROLE_RESPONSABLE, ROLE_AGENT])) {
                        redirect('/index.php?action=dashboard');
                    } else {
                        redirect('/index.php');
                    }
                }
            }
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Formulaire d'inscription (Espace Client) & Traitement POST
     */
    public function register(): void {
        if (isLoggedIn()) {
            redirect('/index.php');
        }

        $errors = [];
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom'          => sanitize($_POST['nom'] ?? ''),
                'prenom'       => sanitize($_POST['prenom'] ?? ''),
                'email'        => sanitize($_POST['email'] ?? ''),
                'mot_de_passe' => $_POST['mot_de_passe'] ?? '',
                'confirm_mdp'  => $_POST['confirm_mdp'] ?? '',
                'telephone'    => sanitize($_POST['telephone'] ?? ''),
                'role'         => ROLE_CLIENT
            ];

            // Validations serveur
            if (empty($data['nom'])) $errors[] = "Le nom est obligatoire.";
            if (empty($data['prenom'])) $errors[] = "Le prénom est obligatoire.";
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Adresse email invalide.";
            } elseif ($this->userModel->findByEmail($data['email'])) {
                $errors[] = "Cet email est déjà utilisé par un autre compte.";
            }
            if (strlen($data['mot_de_passe']) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
            }
            if ($data['mot_de_passe'] !== $data['confirm_mdp']) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            }

            if (empty($errors)) {
                if ($this->userModel->create($data)) {
                    setFlash('success', 'Votre compte a été créé avec succès ! Connectez-vous.');
                    redirect('/index.php?action=login');
                } else {
                    $errors[] = "Une erreur est survenue lors de la création du compte.";
                }
            }
        }

        require_once __DIR__ . '/../views/auth/register.php';
    }

    /**
     * Déconnexion
     */
    public function logout(): void {
        unset($_SESSION['user']);
        session_destroy();
        session_start();
        setFlash('info', 'Vous avez été déconnecté.');
        redirect('/index.php?action=login');
    }
}
