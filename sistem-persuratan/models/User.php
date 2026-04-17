<?php
// ============================================
// Model: User
// Mengelola data pengguna (CRUD + Autentikasi)
// ============================================

class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $nim_nip;
    public $nama;
    public $email;
    public $password;
    public $role;
    public $program_studi;
    public $no_hp;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Login: mencari user berdasarkan NIM/NIP
     */
    public function login($nim_nip) {
        $query = "SELECT * FROM {$this->table} WHERE nim_nip = :nim_nip LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nim_nip', $nim_nip);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Registrasi user baru (mahasiswa)
     */
    public function register() {
        $query = "INSERT INTO {$this->table} (nim_nip, nama, email, password, role, program_studi, no_hp)
                  VALUES (:nim_nip, :nama, :email, :password, 'mahasiswa', :program_studi, :no_hp)";
        $stmt = $this->conn->prepare($query);

        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(':nim_nip', $this->nim_nip);
        $stmt->bindParam(':nama', $this->nama);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':program_studi', $this->program_studi);
        $stmt->bindParam(':no_hp', $this->no_hp);

        return $stmt->execute();
    }

    /**
     * Mendapatkan data user berdasarkan ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Mendapatkan semua mahasiswa
     */
    public function getAllMahasiswa() {
        $query = "SELECT * FROM {$this->table} WHERE role = 'mahasiswa' ORDER BY nama ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Cek apakah NIM/NIP sudah terdaftar
     */
    public function isExists($nim_nip) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE nim_nip = :nim_nip";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nim_nip', $nim_nip);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }
}
