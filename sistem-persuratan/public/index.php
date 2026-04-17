<?php
// ============================================
// SIPJUR - Sistem Informasi Persuratan Jurusan
// Front Controller (Router)
// ============================================

session_start();

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/SuratController.php';

$page = $_GET['page'] ?? 'login';
$auth = new AuthController();
$surat = new SuratController();

// ============================================
// Routing
// ============================================
switch ($page) {
    // --- Auth ---
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->login();
        } else {
            $auth->loginForm();
        }
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->register();
        } else {
            $auth->registerForm();
        }
        break;

    case 'logout':
        $auth->logout();
        break;

    // --- Mahasiswa ---
    case 'mahasiswa_dashboard':
        $surat->mahasiswaDashboard();
        break;

    case 'ajukan_surat':
        $surat->ajukanSurat();
        break;

    case 'proses_ajukan':
        $surat->prosesAjukan();
        break;

    case 'detail_surat':
        $surat->detailSurat();
        break;

    // --- Admin ---
    case 'admin_dashboard':
        $surat->adminDashboard();
        break;

    case 'teruskan_surat':
        $surat->teruskanSurat();
        break;

    case 'selesaikan_surat':
        $surat->selesaikanSurat();
        break;

    // --- Kajur ---
    case 'kajur_dashboard':
        $surat->kajurDashboard();
        break;

    case 'setujui_surat':
        $surat->setujuiSurat();
        break;

    case 'tolak_surat':
        $surat->tolakSurat();
        break;

    default:
        $auth->loginForm();
        break;
}
