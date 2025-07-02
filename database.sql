
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','asisten') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE praktikum (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100),
  deskripsi TEXT
);

CREATE TABLE pendaftaran_praktikum (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT,
  id_praktikum INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unik (id_user, id_praktikum)
);

CREATE TABLE `modul` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_praktikum` int NOT NULL,
  `judul` varchar(100) NOT NULL,
  `file_materi` varchar(255),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_praktikum`) REFERENCES praktikum(`id`) ON DELETE CASCADE
);

CREATE TABLE `laporan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `id_modul` int NOT NULL,
  `file_laporan` varchar(255),
  `nilai` int DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `uploaded_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_user`) REFERENCES users(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_modul`) REFERENCES modul(`id`) ON DELETE CASCADE
);

