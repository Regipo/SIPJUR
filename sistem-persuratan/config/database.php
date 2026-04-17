<?php
// ============================================
// Konfigurasi Database menggunakan PDO
// ============================================

class Database {
    private $host = 'localhost';
    private $db_name = 'sipjur_db';
    private $username = 'root';
    private $password = '';
    private $conn;

    /**
     * Mendapatkan koneksi database menggunakan PDO
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Koneksi Database Gagal: " . $e->getMessage());
        }
        return $this->conn;
    }
}
