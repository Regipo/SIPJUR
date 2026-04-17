<?php
// ============================================
// Model: Surat (Pengajuan Surat)
// Mengelola data pengajuan surat mahasiswa
// ============================================

class Surat {
    private $conn;
    private $table = 'pengajuan_surat';

    public $id;
    public $nomor_surat;
    public $user_id;
    public $jenis_surat_id;
    public $keterangan;
    public $file_lampiran;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Membuat pengajuan surat baru
     */
    public function create() {
        $query = "INSERT INTO {$this->table} (user_id, jenis_surat_id, keterangan, file_lampiran, status)
                  VALUES (:user_id, :jenis_surat_id, :keterangan, :file_lampiran, 'diajukan')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':jenis_surat_id', $this->jenis_surat_id);
        $stmt->bindParam(':keterangan', $this->keterangan);
        $stmt->bindParam(':file_lampiran', $this->file_lampiran);

        if ($stmt->execute()) {
            $pengajuan_id = $this->conn->lastInsertId();
            $this->logAktivitas($pengajuan_id, $this->user_id, 'Pengajuan Dibuat', 'Mahasiswa mengajukan surat baru');
            return $pengajuan_id;
        }
        return false;
    }

    /**
     * Mendapatkan semua pengajuan milik mahasiswa tertentu
     */
    public function getByUserId($user_id) {
        $query = "SELECT p.*, js.nama as jenis_surat_nama, js.kategori
                  FROM {$this->table} p
                  JOIN jenis_surat js ON p.jenis_surat_id = js.id
                  WHERE p.user_id = :user_id
                  ORDER BY p.tanggal_pengajuan DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Mendapatkan semua pengajuan (untuk admin)
     */
    public function getAll($status = null) {
        $query = "SELECT p.*, js.nama as jenis_surat_nama, js.kategori, u.nama as nama_mahasiswa, u.nim_nip, u.program_studi
                  FROM {$this->table} p
                  JOIN jenis_surat js ON p.jenis_surat_id = js.id
                  JOIN users u ON p.user_id = u.id";
        if ($status) {
            $query .= " WHERE p.status = :status";
        }
        $query .= " ORDER BY p.tanggal_pengajuan DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Mendapatkan pengajuan yang perlu divalidasi Kajur
     */
    public function getPendingKajur() {
        $query = "SELECT p.*, js.nama as jenis_surat_nama, js.kategori, u.nama as nama_mahasiswa, u.nim_nip, u.program_studi
                  FROM {$this->table} p
                  JOIN jenis_surat js ON p.jenis_surat_id = js.id
                  JOIN users u ON p.user_id = u.id
                  WHERE p.status = 'diteruskan'
                  ORDER BY p.tanggal_diteruskan ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Mendapatkan detail pengajuan berdasarkan ID
     */
    public function getById($id) {
        $query = "SELECT p.*, js.nama as jenis_surat_nama, js.kategori, js.persyaratan,
                  u.nama as nama_mahasiswa, u.nim_nip, u.email, u.program_studi, u.no_hp
                  FROM {$this->table} p
                  JOIN jenis_surat js ON p.jenis_surat_id = js.id
                  JOIN users u ON p.user_id = u.id
                  WHERE p.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Admin meneruskan surat ke Kajur
     */
    public function teruskanKeKajur($id, $admin_id, $catatan = '') {
        $query = "UPDATE {$this->table} SET status = 'diteruskan', catatan_admin = :catatan, tanggal_diteruskan = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':catatan', $catatan);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $this->logAktivitas($id, $admin_id, 'Diteruskan ke Kajur', $catatan);
            return true;
        }
        return false;
    }

    /**
     * Kajur menyetujui surat
     */
    public function setujui($id, $kajur_id, $catatan = '') {
        $query = "UPDATE {$this->table} SET status = 'disetujui', catatan_kajur = :catatan, tanggal_validasi = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':catatan', $catatan);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $this->logAktivitas($id, $kajur_id, 'Disetujui Kajur', $catatan);
            return true;
        }
        return false;
    }

    /**
     * Kajur menolak surat
     */
    public function tolak($id, $kajur_id, $catatan = '') {
        $query = "UPDATE {$this->table} SET status = 'ditolak', catatan_kajur = :catatan, tanggal_validasi = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':catatan', $catatan);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $this->logAktivitas($id, $kajur_id, 'Ditolak Kajur', $catatan);
            return true;
        }
        return false;
    }

    /**
     * Admin menyelesaikan surat (upload surat final + ttd digital)
     */
    public function selesaikan($id, $admin_id, $nomor_surat, $file_surat = '') {
        $query = "UPDATE {$this->table} SET status = 'selesai', nomor_surat = :nomor_surat, file_surat_selesai = :file_surat, tanggal_selesai = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nomor_surat', $nomor_surat);
        $stmt->bindParam(':file_surat', $file_surat);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $this->logAktivitas($id, $admin_id, 'Surat Selesai', 'Surat telah ditandatangani dan selesai diproses');
            return true;
        }
        return false;
    }

    /**
     * Menghitung statistik pengajuan
     */
    public function getStatistik($user_id = null) {
        $where = $user_id ? "WHERE p.user_id = :user_id" : "";
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'diajukan' THEN 1 ELSE 0 END) as diajukan,
                    SUM(CASE WHEN status = 'diteruskan' THEN 1 ELSE 0 END) as diteruskan,
                    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
                    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai
                  FROM {$this->table} p $where";
        $stmt = $this->conn->prepare($query);
        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        }
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Log aktivitas perubahan status surat
     */
    private function logAktivitas($pengajuan_id, $user_id, $aksi, $keterangan = '') {
        $query = "INSERT INTO log_aktivitas (pengajuan_id, user_id, aksi, keterangan) VALUES (:pengajuan_id, :user_id, :aksi, :keterangan)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pengajuan_id', $pengajuan_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':aksi', $aksi);
        $stmt->bindParam(':keterangan', $keterangan);
        $stmt->execute();
    }

    /**
     * Mendapatkan log aktivitas surat tertentu
     */
    public function getLogAktivitas($pengajuan_id) {
        $query = "SELECT la.*, u.nama as nama_user, u.role
                  FROM log_aktivitas la
                  JOIN users u ON la.user_id = u.id
                  WHERE la.pengajuan_id = :pengajuan_id
                  ORDER BY la.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pengajuan_id', $pengajuan_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
