<?php
/**
 * Fonctions Helpers Utilitaires (Sécurité, Sessions, Redirections)
 */

require_once __DIR__ . '/config.php';

/**
 * Nettoyage des entrées utilisateurs (XSS protection)
 */
function sanitize(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirection HTTP avec URL relative ou absolue
 */
function redirect(string $path): void {
    if (!str_starts_with($path, 'http://') && !str_starts_with($path, 'https://')) {
        $path = BASE_URL . '/' . ltrim($path, '/');
    }
    header("Location: " . $path);
    exit;
}

/**
 * Définit un message flash dans la session
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // success, danger, warning, info
        'message' => $message
    ];
}

/**
 * Affiche et supprime le message flash en cours
 */
function displayFlash(): string {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return '<div class="alert alert-' . $flash['type'] . ' alert-dismissible fade show" role="alert">
                    ' . htmlspecialchars($flash['message']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    }
    return '';
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id_utilisateur']);
}

/**
 * Récupère l'utilisateur connecté en session
 */
function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * Vérifie le rôle de l'utilisateur connecté
 */
function hasRole(string ...$roles): bool {
    if (!isLoggedIn()) return false;
    return in_array($_SESSION['user']['role'], $roles, true);
}

/**
 * Exige un rôle ou redirige avec message d'erreur
 */
function requireRole(string ...$roles): void {
    if (!isLoggedIn()) {
        setFlash('warning', 'Veuillez vous connecter pour accéder à cette page.');
        redirect('/index.php?action=login');
    }
    if (!hasRole(...$roles)) {
        setFlash('danger', 'Accès non autorisé pour votre profil.');
        redirect('/index.php');
    }
}
