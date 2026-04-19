-- ============================================================
--  DATABASE: db_monitoring_teknisi
--  Project  : TechMonitor - Sistem Monitoring Kinerja Teknisi
--  Company  : PT. Nusantara CCTV
--  Created  : 2026
-- ============================================================

CREATE DATABASE IF NOT EXISTS `db_monitoring_teknisi`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE `db_monitoring_teknisi`;

-- ─────────────────────────────────────────────
--  TABEL: users
--  Untuk login admin & teknisi
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
    `nama`       VARCHAR(100) NOT NULL,
    `username`   VARCHAR(50)  NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `role`       ENUM('admin','teknisi') NOT NULL DEFAULT 'teknisi',
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────
--  TABEL: teknisi
--  Data detail profil teknisi
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `teknisi` (
    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
    `user_id`    INT(11)      NOT NULL,
    `nama`       VARCHAR(100) NOT NULL,
    `no_hp`      VARCHAR(20)  DEFAULT NULL,
    `alamat`     TEXT         DEFAULT NULL,
    `spesialisasi` VARCHAR(100) DEFAULT NULL,
    `status`     ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────
--  TABEL: pelanggan
--  Data pelanggan / klien
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `pelanggan` (
    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
    `nama`       VARCHAR(100) NOT NULL,
    `no_hp`      VARCHAR(20)  DEFAULT NULL,
    `alamat`     TEXT         DEFAULT NULL,
    `email`      VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────
--  TABEL: tugas
--  Tugas / pekerjaan yang diberikan ke teknisi
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `tugas` (
    `id`           INT(11)      NOT NULL AUTO_INCREMENT,
    `teknisi_id`   INT(11)      NOT NULL,
    `pelanggan_id` INT(11)      NOT NULL,
    `judul`        VARCHAR(200) NOT NULL,
    `deskripsi`    TEXT         DEFAULT NULL,
    `tanggal`      DATE         NOT NULL,
    `status`       ENUM('pending','proses','selesai','batal') NOT NULL DEFAULT 'pending',
    `prioritas`    ENUM('rendah','sedang','tinggi') NOT NULL DEFAULT 'sedang',
    `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`teknisi_id`)   REFERENCES `teknisi`(`id`)   ON DELETE CASCADE,
    FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────
--  TABEL: laporan
--  Laporan hasil pekerjaan dari teknisi
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `laporan` (
    `id`          INT(11)   NOT NULL AUTO_INCREMENT,
    `tugas_id`    INT(11)   NOT NULL,
    `teknisi_id`  INT(11)   NOT NULL,
    `catatan`     TEXT      DEFAULT NULL,
    `foto`        VARCHAR(255) DEFAULT NULL,
    `tanggal_selesai` DATE  DEFAULT NULL,
    `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`tugas_id`)   REFERENCES `tugas`(`id`)    ON DELETE CASCADE,
    FOREIGN KEY (`teknisi_id`) REFERENCES `teknisi`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────
--  DATA AWAL: users
--  Password semua akun: admin123
--  Hash bcrypt dari: password_hash('admin123', PASSWORD_DEFAULT)
-- ─────────────────────────────────────────────
INSERT INTO `users` (`nama`, `username`, `password`, `role`) VALUES
('Administrator',   'admin',    '$2y$10$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lW', 'admin'),
('Budi Santoso',    'budi',     '$2y$10$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lW', 'teknisi'),
('Andi Prasetyo',   'andi',     '$2y$10$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lW', 'teknisi'),
('Rizky Firmansyah','rizky',    '$2y$10$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lW', 'teknisi');

-- ─────────────────────────────────────────────
--  DATA AWAL: teknisi
-- ─────────────────────────────────────────────
INSERT INTO `teknisi` (`user_id`, `nama`, `no_hp`, `alamat`, `spesialisasi`, `status`) VALUES
(2, 'Budi Santoso',     '081234567890', 'Jl. Merdeka No. 1, Jakarta',   'Instalasi CCTV',          'aktif'),
(3, 'Andi Prasetyo',    '082345678901', 'Jl. Sudirman No. 5, Tangerang','Maintenance & Perbaikan',  'aktif'),
(4, 'Rizky Firmansyah', '083456789012', 'Jl. Gatot Subroto No. 9, Depok','Jaringan & Konfigurasi', 'aktif');

-- ─────────────────────────────────────────────
--  DATA AWAL: pelanggan
-- ─────────────────────────────────────────────
INSERT INTO `pelanggan` (`nama`, `no_hp`, `alamat`, `email`) VALUES
('PT. Maju Bersama',    '021-1234567', 'Jl. Industri No. 10, Jakarta',    'info@majubersama.com'),
('Toko Elektronik Jaya','021-2345678', 'Jl. Pasar No. 3, Bekasi',         'jaya@tokojaya.com'),
('Rumah Sakit Sehat',   '021-3456789', 'Jl. Kesehatan No. 7, Bogor',      'admin@rssehat.com'),
('Mall Nusantara',      '021-4567890', 'Jl. Raya Serpong No. 15, Tangerang','cs@mallnusantara.com');

-- ─────────────────────────────────────────────
--  DATA AWAL: tugas
-- ─────────────────────────────────────────────
INSERT INTO `tugas` (`teknisi_id`, `pelanggan_id`, `judul`, `deskripsi`, `tanggal`, `status`, `prioritas`) VALUES
(1, 1, 'Instalasi CCTV Gedung A',   'Pasang 8 kamera CCTV di area gedung A lantai 1 dan 2', '2026-04-12', 'pending',  'tinggi'),
(2, 2, 'Perbaikan DVR Rusak',       'DVR tidak bisa merekam, perlu pengecekan dan perbaikan', '2026-04-11', 'proses',   'tinggi'),
(3, 3, 'Konfigurasi Remote Access', 'Setting akses CCTV dari jarak jauh via aplikasi',       '2026-04-13', 'pending',  'sedang'),
(1, 4, 'Maintenance Bulanan',       'Cek kondisi semua kamera dan DVR, bersihkan lensa',     '2026-04-10', 'selesai',  'rendah'),
(2, 1, 'Tambah Kamera Area Parkir', 'Instalasi 4 kamera baru di area parkir basement',       '2026-04-14', 'pending',  'sedang');

-- ─────────────────────────────────────────────
--  DATA AWAL: laporan
-- ─────────────────────────────────────────────
INSERT INTO `laporan` (`tugas_id`, `teknisi_id`, `catatan`, `tanggal_selesai`) VALUES
(4, 1, 'Semua kamera dalam kondisi baik, lensa sudah dibersihkan. DVR berfungsi normal. Tidak ada kerusakan ditemukan.', '2026-04-10');
