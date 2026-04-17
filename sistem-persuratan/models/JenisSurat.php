<?php
// ============================================
// Model: JenisSurat
// Mengelola master data jenis surat
// ============================================

class JenisSurat {
    private $conn;
    private $table = 'jenis_surat';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Mendapatkan semua jenis surat yang aktif
     */
    public function getAll() {
        $query = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY kategori, nama";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Mendapatkan jenis surat berdasarkan kategori
     */
    public function getByKategori($kategori) {
        $query = "SELECT * FROM {$this->table} WHERE kategori = :kategori AND is_active = 1 ORDER BY nama";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':kategori', $kategori);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Mendapatkan detail jenis surat berdasarkan ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Mendapatkan semua jenis surat dikelompokkan per kategori
     */
    public function getAllGrouped() {
        $all = $this->getAll();
        $grouped = [];
        foreach ($all as $surat) {
            $grouped[$surat['kategori']][] = $surat;
        }
        return $grouped;
    }
}
