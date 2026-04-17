<?php $pageTitle = 'Dashboard Mahasiswa'; require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- STATISTIK -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-primary"><i class="fas fa-file-alt"></i></div>
        <div class="stat-info">
            <h3><?= $statistik['total'] ?? 0 ?></h3>
            <p>Total Pengajuan</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-warning"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <h3><?= ($statistik['diajukan'] ?? 0) + ($statistik['diteruskan'] ?? 0) ?></h3>
            <p>Dalam Proses</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-success"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <h3><?= $statistik['selesai'] ?? 0 ?></h3>
            <p>Selesai</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-danger"><i class="fas fa-times-circle"></i></div>
        <div class="stat-info">
            <h3><?= $statistik['ditolak'] ?? 0 ?></h3>
            <p>Ditolak</p>
        </div>
    </div>
</div>

<!-- TABEL RIWAYAT PENGAJUAN -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-history"></i> Riwayat Pengajuan Surat</h3>
        <a href="index.php?page=ajukan_surat" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Ajukan Surat Baru
        </a>
    </div>
    <div class="card-body">
        <?php if (!empty($pengajuanSurat)): ?>
            <div style="margin-bottom:15px;">
                <input type="text" id="searchTable" class="form-control" placeholder="Cari surat..." style="max-width:300px;">
            </div>
            <div class="table-responsive">
                <table id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jenis Surat</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pengajuanSurat as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p['tanggal_pengajuan'])) ?></td>
                            <td><?= htmlspecialchars($p['jenis_surat_nama']) ?></td>
                            <td><?= ucfirst(str_replace('_', ' ', $p['kategori'])) ?></td>
                            <td><span class="badge badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                            <td><?= htmlspecialchars(substr($p['keterangan'] ?? '-', 0, 50)) ?></td>
                            <td>
                                <a href="index.php?page=detail_surat&id=<?= $p['id'] ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <?php if ($p['status'] === 'selesai' && $p['file_surat_selesai']): ?>
                                    <a href="uploads/<?= $p['file_surat_selesai'] ?>" class="btn btn-success btn-sm" download>
                                        <i class="fas fa-download"></i> Unduh
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center" style="padding:40px;">
                <div style="font-size:3rem; color:#ddd; margin-bottom:15px;"><i class="fas fa-inbox"></i></div>
                <p style="color:#999;">Belum ada pengajuan surat.</p>
                <a href="index.php?page=ajukan_surat" class="btn btn-primary btn-sm mt-2">Ajukan Surat Pertama</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
