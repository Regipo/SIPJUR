<?php
// ============================================
// Controller: SuratController
// Menangani semua operasi terkait surat
// ============================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Surat.php';
require_once __DIR__ . '/../models/JenisSurat.php';
require_once __DIR__ . '/../models/User.php';

class SuratController {
    private $db;
    private $surat;
    private $jenisSurat;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->surat = new Surat($this->db);
        $this->jenisSurat = new JenisSurat($this->db);
    }

    // =====================
    // MAHASISWA FUNCTIONS
    // =====================

    /**
     * Dashboard Mahasiswa
     */
    public function mahasiswaDashboard() {
        $this->checkAuth('mahasiswa');
        $jenisSuratGrouped = $this->jenisSurat->getAllGrouped();
        $pengajuanSurat = $this->surat->getByUserId($_SESSION['user_id']);
        $statistik = $this->surat->getStatistik($_SESSION['user_id']);
        require_once __DIR__ . '/../views/mahasiswa/dashboard.php';
    }

    /**
     * Form pengajuan surat baru
     */
    public function ajukanSurat() {
        $this->checkAuth('mahasiswa');
        $jenis_id = $_GET['jenis'] ?? null;
        $jenisSuratDetail = $jenis_id ? $this->jenisSurat->getById($jenis_id) : null;
        $allJenisSurat = $this->jenisSurat->getAll();
        require_once __DIR__ . '/../views/mahasiswa/ajukan_surat.php';
    }

    /**
     * Proses pengajuan surat
     */
    public function prosesAjukan() {
        $this->checkAuth('mahasiswa');
        $errors = [];

        $jenis_surat_id = $_POST['jenis_surat_id'] ?? '';
        $keterangan = trim($_POST['keterangan'] ?? '');

        if (empty($jenis_surat_id)) $errors[] = 'Jenis surat wajib dipilih';

        // Handle file upload
        $file_lampiran = '';
        if (isset($_FILES['file_lampiran']) && $_FILES['file_lampiran']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
            $ext = strtolower(pathinfo($_FILES['file_lampiran']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                $errors[] = 'Format file tidak didukung (pdf, jpg, png, doc, docx)';
            } elseif ($_FILES['file_lampiran']['size'] > 5 * 1024 * 1024) {
                $errors[] = 'Ukuran file maksimal 5MB';
            } else {
                $filename = time() . '_' . uniqid() . '.' . $ext;
                $upload_path = __DIR__ . '/../public/uploads/' . $filename;
                if (move_uploaded_file($_FILES['file_lampiran']['tmp_name'], $upload_path)) {
                    $file_lampiran = $filename;
                } else {
                    $errors[] = 'Gagal mengupload file';
                }
            }
        }

        if (empty($errors)) {
            $this->surat->user_id = $_SESSION['user_id'];
            $this->surat->jenis_surat_id = $jenis_surat_id;
            $this->surat->keterangan = $keterangan;
            $this->surat->file_lampiran = $file_lampiran;

            if ($this->surat->create()) {
                $_SESSION['success'] = 'Pengajuan surat berhasil dikirim!';
                header('Location: index.php?page=mahasiswa_dashboard');
                exit;
            } else {
                $errors[] = 'Gagal mengajukan surat';
            }
        }

        $allJenisSurat = $this->jenisSurat->getAll();
        require_once __DIR__ . '/../views/mahasiswa/ajukan_surat.php';
    }

    /**
     * Detail pengajuan surat (mahasiswa)
     */
    public function detailSurat() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?page=mahasiswa_dashboard');
            exit;
        }
        $detail = $this->surat->getById($id);
        $logAktivitas = $this->surat->getLogAktivitas($id);

        // Tentukan view berdasarkan role
        $role = $_SESSION['role'];
        require_once __DIR__ . "/../views/{$role}/detail_surat.php";
    }

    // =====================
    // ADMIN FUNCTIONS
    // =====================

    /**
     * Dashboard Admin
     */
    public function adminDashboard() {
        $this->checkAuth('admin');
        $semuaPengajuan = $this->surat->getAll();
        $statistik = $this->surat->getStatistik();
        $pengajuanBaru = $this->surat->getAll('diajukan');
        $pengajuanDisetujui = $this->surat->getAll('disetujui');
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    /**
     * Admin meneruskan surat ke Kajur
     */
    public function teruskanSurat() {
        $this->checkAuth('admin');
        $id = $_POST['id'] ?? null;
        $catatan = trim($_POST['catatan_admin'] ?? '');

        if ($id) {
            $this->surat->teruskanKeKajur($id, $_SESSION['user_id'], $catatan);
            $_SESSION['success'] = 'Surat berhasil diteruskan ke Kajur!';
        }
        header('Location: index.php?page=admin_dashboard');
        exit;
    }

    /**
     * Admin menyelesaikan surat
     */
    public function selesaikanSurat() {
        $this->checkAuth('admin');
        $id = $_POST['id'] ?? null;
        $nomor_surat = trim($_POST['nomor_surat'] ?? '');

        // Handle upload surat final
        $file_surat = '';
        if (isset($_FILES['file_surat_selesai']) && $_FILES['file_surat_selesai']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['file_surat_selesai']['name'], PATHINFO_EXTENSION));
            $filename = 'surat_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = __DIR__ . '/../public/uploads/' . $filename;
            if (move_uploaded_file($_FILES['file_surat_selesai']['tmp_name'], $upload_path)) {
                $file_surat = $filename;
            }
        }

        if ($id && $nomor_surat) {
            $this->surat->selesaikan($id, $_SESSION['user_id'], $nomor_surat, $file_surat);
            $_SESSION['success'] = 'Surat berhasil diselesaikan!';
        }
        header('Location: index.php?page=admin_dashboard');
        exit;
    }

    // =====================
    // KAJUR FUNCTIONS
    // =====================

    /**
     * Dashboard Kajur
     */
    public function kajurDashboard() {
        $this->checkAuth('kajur');
        $pendingValidasi = $this->surat->getPendingKajur();
        $statistik = $this->surat->getStatistik();
        $semuaPengajuan = $this->surat->getAll();
        require_once __DIR__ . '/../views/kajur/dashboard.php';
    }

    /**
     * Kajur menyetujui surat
     */
    public function setujuiSurat() {
        $this->checkAuth('kajur');
        $id = $_POST['id'] ?? null;
        $catatan = trim($_POST['catatan_kajur'] ?? '');

        if ($id) {
            $this->surat->setujui($id, $_SESSION['user_id'], $catatan);
            $_SESSION['success'] = 'Surat berhasil disetujui!';
        }
        header('Location: index.php?page=kajur_dashboard');
        exit;
    }

    /**
     * Kajur menolak surat
     */
    public function tolakSurat() {
        $this->checkAuth('kajur');
        $id = $_POST['id'] ?? null;
        $catatan = trim($_POST['catatan_kajur'] ?? '');

        if ($id) {
            $this->surat->tolak($id, $_SESSION['user_id'], $catatan);
            $_SESSION['success'] = 'Surat telah ditolak.';
        }
        header('Location: index.php?page=kajur_dashboard');
        exit;
    }

    // =====================
    // HELPER
    // =====================

    /**
     * Cek autentikasi dan role
     */
    private function checkAuth($requiredRole) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        if ($_SESSION['role'] !== $requiredRole) {
            header('Location: index.php?page=login');
            exit;
        }
    }
}
