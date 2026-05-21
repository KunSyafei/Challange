-- Database: student_monitoring

CREATE DATABASE IF NOT EXISTS student_monitoring;
USE student_monitoring;

-- Tabel users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel notulensi_rabuan
CREATE TABLE notulensi_rabuan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(200) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    drive_link VARCHAR(500),
    tanggal_rapat DATE NOT NULL,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- Tabel mentoring
CREATE TABLE mentoring (
    id INT PRIMARY KEY AUTO_INCREMENT,
    materi VARCHAR(200) NOT NULL,
    pengisi_materi VARCHAR(100) NOT NULL,
    bahan_ajar TEXT,
    kebutuhan TEXT,
    tanggal DATE NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Tabel timeline_kegiatan
CREATE TABLE timeline_kegiatan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kegiatan VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    tanggal_mulai DATETIME NOT NULL,
    tanggal_selesai DATETIME NOT NULL,
    lokasi VARCHAR(200),
    status ENUM('planned', 'ongoing', 'completed', 'cancelled') DEFAULT 'planned',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel pra_operasional
CREATE TABLE pra_operasional (
    id INT PRIMARY KEY AUTO_INCREMENT,
    perencanaan TEXT,
    perbekalan_regu TEXT,
    peralatan_pribadi TEXT,
    peralatan_regu TEXT,
    perbekalan TEXT,
    tanggal DATE NOT NULL,
    created_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Tabel siswa
CREATE TABLE siswa (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nis VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    kelas VARCHAR(20),
    jenis_kelamin ENUM('L', 'P'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel kehadiran
CREATE TABLE kehadiran (
    id INT PRIMARY KEY AUTO_INCREMENT,
    siswa_id INT,
    jenis_kegiatan ENUM('rabuan', 'mentoring', 'bina_jasmani') NOT NULL,
    tanggal DATE NOT NULL,
    status ENUM('hadir', 'izin', 'sakit', 'alpha') DEFAULT 'hadir',
    keterangan TEXT,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id),
    UNIQUE KEY unique_kehadiran (siswa_id, jenis_kegiatan, tanggal)
);

-- Tabel bina_jasmani
CREATE TABLE bina_jasmani (
    id INT PRIMARY KEY AUTO_INCREMENT,
    siswa_id INT,
    latihan_fisik VARCHAR(100) NOT NULL,
    nilai DECIMAL(5,2),
    standarisasi_nilai DECIMAL(5,2),
    tanggal DATE NOT NULL,
    catatan TEXT,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id)
);

-- Tabel operasional_kegiatan
CREATE TABLE operasional_kegiatan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(200) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    drive_link VARCHAR(500),
    tanggal_kegiatan DATE NOT NULL,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- Tabel pasca_operasional
CREATE TABLE pasca_operasional (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_peralatan VARCHAR(100) NOT NULL,
    kondisi ENUM('layak', 'tidak_layak', 'perbaikan') NOT NULL,
    checklist BOOLEAN DEFAULT FALSE,
    keterangan TEXT,
    tanggal_cek DATE NOT NULL,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Insert sample data
INSERT INTO users (username, password, role, full_name) VALUES 
('admin', MD5('admin123'), 'admin', 'Administrator'),
('superadmin', MD5('super123'), 'super_admin', 'Super Administrator');

INSERT INTO siswa (nis, nama, kelas, jenis_kelamin) VALUES
('2024001', 'Ahmad Fauzi', 'XII RPL 1', 'L'),
('2024002', 'Siti Nurhaliza', 'XII RPL 1', 'P'),
('2024003', 'Budi Santoso', 'XII RPL 2', 'L');