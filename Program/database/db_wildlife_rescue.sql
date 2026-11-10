-- Database: db_wildlife_rescue
CREATE DATABASE IF NOT EXISTS db_wildlife_rescue;
USE db_wildlife_rescue;

DROP TABLE IF EXISTS health_logs;
DROP TABLE IF EXISTS animals;
DROP TABLE IF EXISTS enclosures;
DROP TABLE IF EXISTS veterinarians;

-- 1. Tabel enclosures
CREATE TABLE enclosures (
    id_enclosure INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    habitat_type VARCHAR(50) NOT NULL,
    capacity INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel animals
CREATE TABLE animals (
    id_animal INT AUTO_INCREMENT PRIMARY KEY,
    id_enclosure INT NOT NULL,
    species_name VARCHAR(100) NOT NULL,
    nickname VARCHAR(50) NOT NULL,
    rescue_date DATE NOT NULL,
    status ENUM('Karantina', 'Rehabilitasi', 'Siap Rilis', 'Telah Dirilis') NOT NULL DEFAULT 'Karantina',
    CONSTRAINT fk_animal_enclosure 
        FOREIGN KEY (id_enclosure) REFERENCES enclosures(id_enclosure) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel veterinarians (Dokter Hewan)
CREATE TABLE veterinarians (
    id_vet INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    gender ENUM('Laki-laki', 'Perempuan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabel health_logs
CREATE TABLE health_logs (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_animal INT NOT NULL,
    id_vet INT NOT NULL,
    checkup_date DATETIME NOT NULL,
    diagnosis TEXT NOT NULL,
    recovery_date DATETIME DEFAULT NULL,
    status ENUM('Dalam Perawatan', 'Sembuh') NOT NULL DEFAULT 'Dalam Perawatan',
    CONSTRAINT fk_health_animal 
        FOREIGN KEY (id_animal) REFERENCES animals(id_animal) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_health_vet 
        FOREIGN KEY (id_vet) REFERENCES veterinarians(id_vet) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Dummy Awal
INSERT INTO enclosures (id_enclosure, name, habitat_type, capacity) VALUES
(1, 'Aviary A', 'Udara', 5),
(2, 'Kandang Primata 1', 'Terestrial', 4),
(3, 'Kolam Karantina 1', 'Akuatik', 3),
(4, 'Kandang Reptil', 'Amfibi', 2);

INSERT INTO animals (id_animal, id_enclosure, species_name, nickname, rescue_date, status) VALUES
(1, 2, 'Orangutan', 'Hope', '2026-01-15', 'Rehabilitasi'),
(2, 1, 'Elang Jawa', 'Garuda', '2026-02-10', 'Siap Rilis'),
(3, 3, 'Berang-berang', 'Charmy', '2026-03-05', 'Karantina'),
(4, 2, 'Owa Jawa', 'Miko', '2026-02-28', 'Rehabilitasi'),
(5, 4, 'Komodo', 'Bimo', '2026-01-20', 'Telah Dirilis');

INSERT INTO veterinarians (id_vet, name, gender) VALUES
(1, 'drh. Sarah Wijaya', 'Perempuan'),
(2, 'drh. Budi Pratama', 'Laki-laki'),
(3, 'drh. Farhan Ansori', 'Laki-laki');

INSERT INTO health_logs (id_log, id_animal, id_vet, checkup_date, diagnosis, recovery_date, status) VALUES
(1, 1, 1, '2026-01-16 09:30:00', 'Malnutrisi ringan', '2026-01-25 10:00:00', 'Sembuh'),
(2, 2, 2, '2026-02-11 11:15:00', 'Luka robek sayap kanan', '2026-02-22 14:30:00', 'Sembuh'),
(3, 3, 1, '2026-03-06 14:00:00', 'Dehidrasi & kutu air', NULL, 'Dalam Perawatan'),
(4, 1, 1, '2026-02-20 10:00:00', 'Pemeriksaan rutin', '2026-02-20 11:30:00', 'Sembuh'),
(5, 4, 3, '2026-03-01 13:45:00', 'Flu dan lesu', NULL, 'Dalam Perawatan');
