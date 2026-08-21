<?php
/**
 * Configuration globale de l'application EquipLoc
 * PHP 8 Natif - Architecture MVC
 */

// Configuration de la Base de Données
define('DB_HOST', 'localhost');
define('DB_NAME', 'equiploc');
define('DB_USER', 'root');
define('DB_PASS', ''); // Par défaut vide sur WAMP

// Configuration des URL et Chemins
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

if (str_ends_with($scriptDir, '/public')) {
    $basePath = $scriptDir;
} elseif ($scriptDir === '/' || $scriptDir === '.' || empty($scriptDir)) {
    $basePath = '/public';
} else {
    $basePath = rtrim($scriptDir, '/') . '/public';
}

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

define('BASE_URL', rtrim($protocol . "://" . $host . $basePath, '/'));

define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// Rôles utilisateurs
define('ROLE_RESPONSABLE', 'responsable_inventaire');
define('ROLE_AGENT', 'agent_location');
define('ROLE_CLIENT', 'client');

// Démarrage sécurisé de la session si non démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fuseau horaire
date_default_timezone_set('Africa/Tunis');
