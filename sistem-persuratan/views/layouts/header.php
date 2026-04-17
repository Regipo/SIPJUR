<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPJUR - Sistem Informasi Persuratan Jurusan</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="app-container">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div style="font-size:2rem;">🎓</div>
            <h3>SIPJUR</h3>
            <small style="color:rgba(255,255,255,0.6); font-size:0.75rem;">Sistem Informasi Persuratan</small>
        </div>
        <nav class="sidebar-menu">
            <?php if ($_SESSION['role'] === 'mahasiswa'): ?>
                <a href="index.php?page=mahasiswa_dashboard" class="<?= ($page ?? '') === 'mahasiswa_dashboard' ? 'active' : '' ?>">
                    <span class="icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard
                </a>
                <div class="menu-category">Pengajuan Bidang Akademik</div>
                <?php
                $db_temp = (new Database())->getConnection();
                $js_temp = new JenisSurat($db_temp);
                $grouped = $js_temp->getAllGrouped();
                $kategori_labels = [
                    'akademik' => 'Pengajuan Bidang Akademik',
                    'kemahasiswaan' => 'Pengajuan Bidang Kemahasiswaan',
                    'umum_keuangan' => 'Pengajuan Bidang Umum & Keuangan',
                    'perpustakaan' => 'Pengajuan Bidang Perpustakaan'
                ];
                $first = true;
                foreach ($grouped as $kat => $items):
                    if (!$first): ?>
                        <div class="menu-category"><?= $kategori_labels[$kat] ?? ucfirst($kat) ?></div>
                    <?php endif;
                    $first = false;
                    foreach ($items as $js): ?>
                        <a href="index.php?page=ajukan_surat&jenis=<?= $js['id'] ?>">
                            <span class="icon"><i class="fas fa-file-alt"></i></span> <?= htmlspecialchars($js['nama']) ?>
                        </a>
                    <?php endforeach;
                endforeach; ?>

            <?php elseif ($_SESSION['role'] === 'admin'): ?>
                <a href="index.php?page=admin_dashboard" class="active">
                    <span class="icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard
                </a>
                <div class="menu-category">Kelola Surat</div>
                <a href="index.php?page=admin_dashboard#baru">
                    <span class="icon"><i class="fas fa-inbox"></i></span> Pengajuan Baru
                </a>
                <a href="index.php?page=admin_dashboard#disetujui">
                    <span class="icon"><i class="fas fa-check-circle"></i></span> Siap Diproses
                </a>
                <a href="index.php?page=admin_dashboard#semua">
                    <span class="icon"><i class="fas fa-list"></i></span> Semua Pengajuan
                </a>

            <?php elseif ($_SESSION['role'] === 'kajur'): ?>
                <a href="index.php?page=kajur_dashboard" class="active">
                    <span class="icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard
                </a>
                <div class="menu-category">Validasi Surat</div>
                <a href="index.php?page=kajur_dashboard#pending">
                    <span class="icon"><i class="fas fa-clock"></i></span> Menunggu Validasi
                </a>
                <a href="index.php?page=kajur_dashboard#semua">
                    <span class="icon"><i class="fas fa-list"></i></span> Semua Surat
                </a>
            <?php endif; ?>
        </nav>
        <div class="copyright" style="color:rgba(255,255,255,0.4); position:absolute; bottom:0; width:100%; font-size:0.7rem; padding:10px;">
            Version 1.0 &copy; 2025 SIPJUR
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- TOPBAR -->
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:15px;">
                <button id="sidebarToggle" style="display:none; background:none; border:none; font-size:1.3rem; cursor:pointer; color:#555;">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="page-title"><?= $pageTitle ?? 'Dashboard' ?></span>
            </div>
            <div class="user-info">
                <span style="font-size:0.85rem; color:#666;">Sistem Informasi Persuratan Jurusan</span>
                <span style="margin:0 5px; color:#ddd;">|</span>
                <div class="avatar"><?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1)) ?></div>
                <span style="font-size:0.85rem; font-weight:600;"><?= htmlspecialchars($_SESSION['nama'] ?? '') ?></span>
                <?php if ($_SESSION['role'] === 'mahasiswa'): ?>
                    <span style="font-size:0.8rem; color:#888;">- NIM <?= htmlspecialchars($_SESSION['nim_nip'] ?? '') ?></span>
                <?php endif; ?>
                <a href="index.php?page=logout" style="color:var(--danger); margin-left:10px; font-size:0.85rem;" title="Logout">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </div>
        </header>

        <div class="content-area alert-container">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-auto-hide"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-auto-hide"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
