<?php
/**
 * Modèle Utilisateur
 * Interaction PDO avec la table `utilisateur`
 */

require_once __DIR__ . '/../config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE LOWER(email) = LOWER(:email) LIMIT 1");
        $stmt->execute(['email' => trim($email)]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getAll(?string $role = null): array {
        if ($role) {
            $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE role = :role ORDER BY date_creation DESC");
            $stmt->execute(['role' => $role]);
        } else {
            $stmt = $this->db->query("SELECT * FROM utilisateur ORDER BY date_creation DESC");
        }
        return $stmt->fetchAll();
    }

    public function create(array $data): bool {
        $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, telephone, role) 
                VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone, :role)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_BCRYPT),
            'telephone' => $data['telephone'] ?? null,
            'role' => $data['role'] ?? 'client'
        ]);
    }

    public function update(int $id, array $data): bool {
        $fields = "nom = :nom, prenom = :prenom, email = :email, telephone = :telephone, role = :role";
        $params = [
            'id' => $id,
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'] ?? null,
            'role' => $data['role']
        ];

        if (!empty($data['mot_de_passe'])) {
            $fields .= ", mot_de_passe = :mot_de_passe";
            $params['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT);
        }

        $sql = "UPDATE utilisateur SET {$fields} WHERE id_utilisateur = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM utilisateur WHERE id_utilisateur = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function count(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn();
    }
}
