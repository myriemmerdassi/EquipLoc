<?php
/**
 * Gestionnaire de Connexion PDO (Pattern Singleton)
 * Conforme aux contraintes PHP 8 + PDO
 */

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    /**
     * Retourne l'instance unique de la connexion PDO
     * @return PDO
     */
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()));
            }
        }
        return self::$instance;
    }
}
