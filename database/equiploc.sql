-- ============================================================
-- SCRIPT DE CRÉATION DE LA BASE DE DONNÉES : equiploc
-- Plateforme de Gestion de Location d'Équipements
-- Architecture : MySQL / MariaDB (PHP 8 + PDO)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `equiploc` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `equiploc`;

-- Disable FK checks temporarily for safe table setup
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `location`;
DROP TABLE IF EXISTS `equipement`;
DROP TABLE IF EXISTS `categorie`;
DROP TABLE IF EXISTS `utilisateur`;

SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- TABLE 1 : utilisateur
-- ------------------------------------------------------------
CREATE TABLE `utilisateur` (
  `id_utilisateur` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(100) NOT NULL,
  `prenom` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `mot_de_passe` VARCHAR(255) NOT NULL,
  `telephone` VARCHAR(20) DEFAULT NULL,
  `role` ENUM('responsable_inventaire', 'agent_location', 'client') NOT NULL DEFAULT 'client',
  `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE 2 : categorie
-- ------------------------------------------------------------
CREATE TABLE `categorie` (
  `id_categorie` INT AUTO_INCREMENT PRIMARY KEY,
  `nom_categorie` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE 3 : equipement
-- ------------------------------------------------------------
CREATE TABLE `equipement` (
  `id_equipement` INT AUTO_INCREMENT PRIMARY KEY,
  `nom_equipement` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `prix_par_jour` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock` INT NOT NULL DEFAULT 0,
  `seuil_alerte` INT NOT NULL DEFAULT 5,
  `etat` ENUM('Disponible', 'En location', 'En maintenance', 'Endommagé') NOT NULL DEFAULT 'Disponible',
  `image` VARCHAR(255) DEFAULT 'default_equipement.png',
  `id_categorie` INT NOT NULL,
  CONSTRAINT `fk_equipement_categorie` FOREIGN KEY (`id_categorie`) 
    REFERENCES `categorie` (`id_categorie`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE 4 : location
-- ------------------------------------------------------------
CREATE TABLE `location` (
  `id_location` INT AUTO_INCREMENT PRIMARY KEY,
  `id_client` INT NOT NULL,
  `id_agent` INT DEFAULT NULL,
  `id_equipement` INT NOT NULL,
  `quantite` INT NOT NULL DEFAULT 1,
  `date_debut` DATE NOT NULL,
  `date_fin` DATE NOT NULL,
  `duree_jours` INT NOT NULL DEFAULT 1,
  `montant_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `frais_supplementaires` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `motif_frais` TEXT DEFAULT NULL,
  `statut` ENUM('En attente', 'Validée', 'En cours', 'Terminée', 'Annulée') NOT NULL DEFAULT 'En attente',
  `etat_retour` ENUM('Disponible', 'En maintenance', 'Endommagé') DEFAULT NULL,
  `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_location_client` FOREIGN KEY (`id_client`) 
    REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_location_agent` FOREIGN KEY (`id_agent`) 
    REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_location_equipement` FOREIGN KEY (`id_equipement`) 
    REFERENCES `equipement` (`id_equipement`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DONNÉES INITIALES DE DÉMONSTRATION (TESTING SEED DATA)
-- Mots de passe hashés avec password_hash('password123', PASSWORD_BCRYPT)
-- ============================================================

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `telephone`, `role`) VALUES
(1, 'Ben Ali', 'Mohamed', 'responsable@equiploc.tn', '$2y$10$J6o5B0emGsbR1TequWu3FelqYGt8ZmvljZLYN1USsAQeu2iGqlGJ.', '98123456', 'responsable_inventaire'),
(2, 'Trabelsi', 'Sarra', 'agent@equiploc.tn', '$2y$10$J6o5B0emGsbR1TequWu3FelqYGt8ZmvljZLYN1USsAQeu2iGqlGJ.', '22987654', 'agent_location'),
(3, 'Gharbi', 'Ahmed', 'client@gmail.com', '$2y$10$J6o5B0emGsbR1TequWu3FelqYGt8ZmvljZLYN1USsAQeu2iGqlGJ.', '55112233', 'client');

INSERT INTO `categorie` (`id_categorie`, `nom_categorie`, `description`) VALUES
(1, 'Audiovisuel', 'Caméras, projecteurs, micros, trépieds et équipements de prise de vue'),
(2, 'Informatique', 'Ordinateurs portables, serveurs, écrans et accessoires réseau'),
(3, 'Jardinage & Outils', 'Matériel de motoculture, taille-haies et outillage électroportatif'),
(4, 'Événementiel & Sonorisation', 'Enceintes de puissance, tables de mixage, jeux de lumières');

INSERT INTO `equipement` (`id_equipement`, `nom_equipement`, `description`, `prix_par_jour`, `stock`, `seuil_alerte`, `etat`, `image`, `id_categorie`) VALUES
(1, 'Sony Alpha 7 IV', 'Caméra hybride plein format 33 MP, enregistrement 4K 60p.', 120.00, 15, 5, 'Disponible', 'camera_sony_a7iv.jpg', 1),
(2, 'MacBook Pro M2 Max', '16 pouces, 32 Go RAM, 1 To SSD. Idéal pour montage vidéo.', 90.00, 8, 3, 'Disponible', 'macbook_pro_m2.jpg', 2),
(3, 'Projecteur Epson EB-2250U', 'Vidéoprojecteur Full HD 5000 Lumens HDMI/VGA.', 45.00, 4, 5, 'Disponible', 'projecteur_epson.jpg', 1),
(4, 'Drone DJI Mavic 3 Pro', 'Drone triple caméra Hasselblad 4K, autonomie 43 min.', 150.00, 6, 2, 'Disponible', 'drone_dji_mavic3.jpg', 1),
(5, 'Pack Sonorisation JBL PartyBox 1000', 'Enceinte Bluetooth 1100W avec jeux de lumière intégrés.', 80.00, 10, 4, 'Disponible', 'jbl_partybox.jpg', 4);

INSERT INTO `location` (`id_location`, `id_client`, `id_agent`, `id_equipement`, `quantite`, `date_debut`, `date_fin`, `duree_jours`, `montant_total`, `frais_supplementaires`, `motif_frais`, `statut`, `etat_retour`) VALUES
(1, 3, 2, 1, 1, '2026-08-10', '2026-08-15', 5, 600.00, 0.00, NULL, 'Validée', NULL);
