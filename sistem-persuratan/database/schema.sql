-- ============================================
-- DATABASE: SISTEM PERSURATAN JURUSAN (SIPJUR)
-- Sistem Informasi Persuratan Jurusan
-- ============================================

CREATE DATABASE IF NOT EXISTS sipjur_db;
USE sipjur_db;

-- ============================================
-- TABEL: users
-- Menyimpan data pengguna (Mahasiswa, Admin, Kajur)
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim_nip VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    password VARCHAR(255) NOT NULL,
    role ENUM('mahasiswa', 'admin', 'kajur') NOT NULL DEFAULT 'mahasiswa',
    program_studi VARCHAR(100),
    no_hp VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABEL: jenis_surat
-- Master data jenis-jenis surat yang tersedia
-- ============================================
CREATE TABLE jenis_surat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    kategori ENUM('akademik', 'kemahasiswaan', 'umum_keuangan', 'perpustakaan') NOT NULL,
    deskripsi TEXT,
    persyaratan TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABEL: pengajuan_surat
-- Data pengajuan surat dari mahasiswa
-- ============================================
CREATE TABLE pengajuan_surat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(50),
    user_id INT NOT NULL,
    jenis_surat_id INT NOT NULL,
    keterangan TEXT,
    file_lampiran VARCHAR(255),
    status ENUM('diajukan', 'diteruskan', 'disetujui', 'ditolak', 'selesai') DEFAULT 'diajukan',
    catatan_admin TEXT,
    catatan_kajur TEXT,
    tanggal_pengajuan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tanggal_diteruskan TIMESTAMP NULL,
    tanggal_validasi TIMESTAMP NULL,
    tanggal_selesai TIMESTAMP NULL,
    file_surat_selesai VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (jenis_surat_id) REFERENCES jenis_surat(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABEL: log_aktivitas
-- Mencatat setiap perubahan status surat
-- ============================================
CREATE TABLE log_aktivitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pengajuan_id INT NOT NULL,
    user_id INT NOT NULL,
    aksi VARCHAR(100) NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pengajuan_id) REFERENCES pengajuan_surat(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- DATA AWAL: Jenis Surat
-- ============================================
INSERT INTO jenis_surat (nama, kategori, deskripsi, persyaratan) VALUES
-- Bidang Akademik
('Surat Aktif Kuliah', 'akademik', 'Surat keterangan bahwa mahasiswa masih aktif berkuliah', 'KRS semester berjalan, Bukti pembayaran UKT'),
('Transkrip Akademik', 'akademik', 'Pengajuan cetak transkrip nilai akademik', 'KHS terakhir'),
('Surat Keterangan Lulus', 'akademik', 'Surat keterangan telah lulus dari program studi', 'Bukti lulus yudisium'),
('Surat Ijin Penelitian', 'akademik', 'Surat ijin untuk melakukan penelitian tugas akhir', 'Proposal penelitian, Surat persetujuan pembimbing'),
('Surat Pengantar Cuti Akademik', 'akademik', 'Surat pengantar pengajuan cuti akademik', 'Surat pernyataan, Bukti pembayaran UKT'),
('Surat Pengantar Pindah Studi', 'akademik', 'Surat pengantar untuk pindah program studi', 'Transkrip nilai, Surat pernyataan'),
('Legalisir', 'akademik', 'Legalisir dokumen akademik', 'Dokumen asli yang akan dilegalisir'),
('Yudisium', 'akademik', 'Pengajuan yudisium kelulusan', 'Transkrip, Bukti bebas tanggungan'),

-- Bidang Kemahasiswaan
('Surat Keterangan Tidak Menerima Beasiswa', 'kemahasiswaan', 'Surat keterangan bahwa mahasiswa tidak sedang menerima beasiswa', 'KTM, Surat pernyataan'),
('Surat Rekomendasi Beasiswa', 'kemahasiswaan', 'Surat rekomendasi untuk pengajuan beasiswa', 'Transkrip nilai, CV, Proposal beasiswa'),

-- Bidang Umum dan Keuangan
('Surat Rekomendasi Pembukaan Virtual Akun UKT', 'umum_keuangan', 'Surat rekomendasi pembukaan virtual akun untuk pembayaran UKT', 'KTM, Bukti registrasi'),
('Surat Rekomendasi Pembukaan Virtual Akun IPI', 'umum_keuangan', 'Surat rekomendasi pembukaan virtual akun IPI', 'KTM, Bukti registrasi'),

-- Bidang Perpustakaan
('Surat Keterangan Bebas Pinjam', 'perpustakaan', 'Surat keterangan bebas pinjaman perpustakaan', 'Kartu anggota perpustakaan');

-- ============================================
-- DATA AWAL: User Default
-- Password default: password123 (hashed dengan password_hash)
-- ============================================
INSERT INTO users (nim_nip, nama, email, password, role, program_studi) VALUES
('admin001', 'Administrator Jurusan', 'admin@jurusan.ac.id', '$2y$10$W4FMIVycKjAXfagCZA44Ye8o6NEvYbIKXMzxsby35FwoIh5S/A4X.', 'admin', NULL),
('198501012010011001', 'Dr. Ahmad Fauzi, M.T.', 'kajur@jurusan.ac.id', '$2y$10$W4FMIVycKjAXfagCZA44Ye8o6NEvYbIKXMzxsby35FwoIh5S/A4X.', 'kajur', 'Teknik Informatika'),
('2430105030005', 'Okta Febrianto', 'okta@mhs.ac.id', '$2y$10$W4FMIVycKjAXfagCZA44Ye8o6NEvYbIKXMzxsby35FwoIh5S/A4X.', 'mahasiswa', 'Teknik Informatika');
