<?php
/**
 * Modèle Catégorie
 * Interaction PDO avec la table `categorie`
 */

require_once __DIR__ . '/../config/database.php';

class Categorie {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT c.*, COUNT(e.id_equipement) AS nb_equipements 
                                  FROM categorie c 
                                  LEFT JOIN equipement e ON c.id_categorie = e.id_categorie 
                                  GROUP BY c.id_categorie 
                                  ORDER BY c.nom_categorie ASC");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM categorie WHERE id_categorie = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO categorie (nom_categorie, description) VALUES (:nom, :desc)");
        return $stmt->execute([
            'nom' => $data['nom_categorie'],
            'desc' => $data['description'] ?? null
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE categorie SET nom_categorie = :nom, description = :desc WHERE id_categorie = :id");
        return $stmt->execute([
            'id' => $id,
            'nom' => $data['nom_categorie'],
            'desc' => $data['description'] ?? null
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM categorie WHERE id_categorie = :id");
        return $stmt->execute(['id' => $id]);
    }
}
