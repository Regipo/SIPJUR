<?php
// ============================================
// Controller: AuthController
// Menangani autentikasi (login, register, logout)
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    /**
     * Menampilkan halaman login
     */
    public function loginForm() {
        if (isset($_SESSION['user_id'])) {
            $this->redirectByRole($_SESSION['role']);
            return;
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Proses login
     */
    public function login() {
        $errors = [];

        // Validasi input
        $nim_nip = trim($_POST['nim_nip'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($nim_nip)) $errors[] = 'NIM/NIP wajib diisi';
        if (empty($password)) $errors[] = 'Password wajib diisi';

        if (empty($errors)) {
            $userData = $this->user->login($nim_nip);

            if ($userData && password_verify($password, $userData['password'])) {
                // Set session
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['nim_nip'] = $userData['nim_nip'];
                $_SESSION['nama'] = $userData['nama'];
                $_SESSION['role'] = $userData['role'];

                $this->redirectByRole($userData['role']);
                return;
            } else {
                $errors[] = 'NIM/NIP atau Password salah!';
            }
        }

        // Kembali ke form login dengan error
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Menampilkan halaman register
     */
    public function registerForm() {
        require_once __DIR__ . '/../views/auth/register.php';
    }

    /**
     * Proses registrasi
     */
    public function register() {
        $errors = [];

        $nim = trim($_POST['nim_nip'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $no_hp = trim($_POST['no_hp'] ?? '');

        // Validasi server-side
        if (empty($nim)) $errors[] = 'NIM wajib diisi';
        if (empty($nama)) $errors[] = 'Nama wajib diisi';
        if (empty($password)) $errors[] = 'Password wajib diisi';
        if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter';
        if ($password !== $confirm) $errors[] = 'Konfirmasi password tidak cocok';
        if ($this->user->isExists($nim)) $errors[] = 'NIM sudah terdaftar';

        if (empty($errors)) {
            $this->user->nim_nip = $nim;
            $this->user->nama = $nama;
            $this->user->email = $email;
            $this->user->password = $password;
            $this->user->no_hp = $no_hp;

            if ($this->user->register()) {
                $_SESSION['success'] = 'Registrasi berhasil! Silakan login.';
                header('Location: index.php?page=login');
                exit;
            } else {
                $errors[] = 'Registrasi gagal, silakan coba lagi';
            }
        }

        require_once __DIR__ . '/../views/auth/register.php';
    }

    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }

    /**
     * Redirect berdasarkan role user
     */
    private function redirectByRole($role) {
        switch ($role) {
            case 'admin':
                header('Location: index.php?page=admin_dashboard');
                break;
            case 'kajur':
                header('Location: index.php?page=kajur_dashboard');
                break;
            default:
                header('Location: index.php?page=mahasiswa_dashboard');
                break;
        }
        exit;
    }
}
