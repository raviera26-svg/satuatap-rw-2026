CREATE DATABASE IF NOT EXISTS satu_atap_rw CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE satu_atap_rw;

-- Tabel users (RW dan RT)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('rw', 'rt') NOT NULL,
    nomor_rt TINYINT NULL COMMENT 'NULL jika role RW, 1-8 jika role RT',
    nomor_wa VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    foto VARCHAR(255) NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel warga
CREATE TABLE IF NOT EXISTS warga (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(16) NOT NULL UNIQUE,
    no_kk VARCHAR(16) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    tempat_lahir VARCHAR(50) NOT NULL,
    tanggal_lahir DATE NOT NULL,

    umur TINYINT UNSIGNED DEFAULT NULL,

    jenis_kelamin ENUM('L', 'P') NOT NULL,
    alamat TEXT NOT NULL,
    rt TINYINT UNSIGNED NOT NULL,
    rw TINYINT UNSIGNED NOT NULL DEFAULT 8,

    status_domisili ENUM(
        'Warga Tetap',
        'Warga Sementara',
        'Pendatang'
    ) DEFAULT 'Warga Tetap',

    status_perkawinan ENUM(
        'Belum Kawin',
        'Kawin',
        'Cerai Hidup',
        'Cerai Mati'
    ) DEFAULT 'Belum Kawin',

    pekerjaan VARCHAR(100) NULL,
    no_telepon VARCHAR(20) NULL,

    kategori_usia ENUM(
        'Baruta',
        'Balita',
        'Anak-Anak',
        'Remaja',
        'WUS',
        'Dewasa',
        'Lansia'
    ) DEFAULT 'Dewasa',

    perlu_update TINYINT(1) DEFAULT 0,
    input_oleh INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (input_oleh) REFERENCES users(id) ON DELETE SET NULL
);
-- Tabel monitoring bulanan
CREATE TABLE IF NOT EXISTS monitoring_bulanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bulan TINYINT NOT NULL,
    tahun YEAR NOT NULL,
    rt TINYINT UNSIGNED NOT NULL,
    total_lansia INT DEFAULT 0,
    total_wus INT DEFAULT 0,
    total_remaja INT DEFAULT 0,
    total_balita INT DEFAULT 0,
    total_baruta INT DEFAULT 0,
    total_anak INT DEFAULT 0,
    total_dewasa INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_bulan_rt (bulan, tahun, rt)
);

-- Insert user RW default
INSERT INTO users (
    username,
    password,
    nama_lengkap,
    role,
    nomor_rt,
    nomor_wa,
    email,
    status
)
VALUES (
    'admin_rw',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Ahmad Priono',
    'rw',
    NULL,
    '+6281234567890',
    'rw08@example.com',
    'aktif'
);
-- Password default: password

-- Insert 8 akun RT
INSERT INTO users (username, password, nama_lengkap, role, nomor_rt, status) VALUES
('admin_rt01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bpk. ADI ', 'rt', 01, 'aktif'),
('admin_rt02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ibu Siti Aminah', 'rt', 2, 'aktif'),
('admin_rt03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bpk. Bambang Pamungkas', 'rt', 3, 'aktif'),
('admin_rt04', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bpk. Dr. Kurniawan', 'rt', 4, 'aktif'),
('admin_rt05', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ibu Risma Handayani', 'rt', 5, 'aktif'),
('admin_rt06', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bpk. Pak Suherman', 'rt', 6, 'aktif'),
('admin_rt07', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bpk. Dedi Supriadi', 'rt', 7, 'aktif'),
('admin_rt08', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bpk. Hendra Wijaya', 'rt', 8, 'aktif');

-- Insert sample warga
INSERT INTO warga (nik, no_kk, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, rt, rw, status_domisili, status_perkawinan, pekerjaan, no_telepon) VALUES
('3271041205850001', '3271041205850000', 'Agus Setiawan', 'Jakarta', '1985-05-12', 'L', 'Jl. Mawar No. 1', 1, 8, 'Warga Tetap', 'Kawin', 'Karyawan Swasta', '081234567001'),
('3271042208920003', '3271042208920000', 'Siti Maryam', 'Bandung', '1992-08-22', 'P', 'Jl. Melati No. 3', 2, 8, 'Warga Tetap', 'Kawin', 'Ibu Rumah Tangga', '081234567002'),
('3271041112450002', '3271041112450000', 'Bambang Kusuma', 'Surabaya', '1945-11-12', 'L', 'Jl. Anggrek No. 5', 1, 8, 'Warga Tetap', 'Kawin', 'Pensiunan', '081234567003'),
('3271040506120007', '3271040506120000', 'Dimas Rizky', 'Jakarta', '2012-06-05', 'L', 'Jl. Dahlia No. 7', 3, 8, 'Warga Tetap', 'Belum Kawin', 'Pelajar', '081234567004'),
('3271045510060004', '3271045510060000', 'Laras Atmaja', 'Bogor', '2006-10-15', 'P', 'Jl. Kenanga No. 9', 5, 8, 'Warga Tetap', 'Belum Kawin', 'Pelajar', '081234567005'),
('3271041503880006', '3271041503880000', 'Rina Wulandari', 'Jakarta', '1988-03-15', 'P', 'Jl. Tulip No. 11', 2, 8, 'Warga Tetap', 'Kawin', 'Guru', '081234567006'),
('3271042807950008', '3271042807950000', 'Hendra Gunawan', 'Bekasi', '1995-07-28', 'L', 'Jl. Mawar No. 15', 4, 8, 'Warga Tetap', 'Belum Kawin', 'Wiraswasta', '081234567007'),
('3271041901020009', '3271041901020000', 'Bayi Cantika', 'Jakarta', '2023-01-19', 'P', 'Jl. Melati No. 20', 3, 8, 'Warga Tetap', 'Belum Kawin', '-', '081234567008');
