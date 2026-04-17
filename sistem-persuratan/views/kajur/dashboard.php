<?php $pageTitle = 'Dashboard Ketua Jurusan'; require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- STATISTIK -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-primary"><i class="fas fa-file-alt"></i></div>
        <div class="stat-info"><h3><?= $statistik['total'] ?? 0 ?></h3><p>Total Pengajuan</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-warning"><i class="fas fa-clock"></i></div>
        <div class="stat-info"><h3><?= count($pendingValidasi) ?></h3><p>Menunggu Validasi</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-success"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info"><h3><?= ($statistik['disetujui'] ?? 0) + ($statistik['selesai'] ?? 0) ?></h3><p>Disetujui</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-danger"><i class="fas fa-times-circle"></i></div>
        <div class="stat-info"><h3><?= $statistik['ditolak'] ?? 0 ?></h3><p>Ditolak</p></div>
    </div>
</div>

<!-- SURAT MENUNGGU VALIDASI -->
<div class="card" id="pending">
    <div class="card-header">
        <h3><i class="fas fa-clock" style="color:var(--warning);"></i> Menunggu Validasi Anda</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($pendingValidasi)): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr><th>No</th><th>Tanggal</th><th>NIM</th><th>Nama</th><th>Prodi</th><th>Jenis Surat</th><th>Catatan Admin</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingValidasi as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['tanggal_diteruskan'])) ?></td>
                        <td><?= htmlspecialchars($p['nim_nip']) ?></td>
                        <td><?= htmlspecialchars($p['nama_mahasiswa']) ?></td>
                        <td><?= htmlspecialchars($p['program_studi'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($p['jenis_surat_nama']) ?></td>
                        <td><?= htmlspecialchars(substr($p['catatan_admin'] ?? '-', 0, 40)) ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="index.php?page=detail_surat&id=<?= $p['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                <button class="btn btn-success btn-sm" onclick="setActionId('modalSetujui', <?= $p['id'] ?>)">
                                    <i class="fas fa-check"></i> ACC
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="setActionId('modalTolak', <?= $p['id'] ?>)">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="text-center" style="padding:30px;">
                <div style="font-size:3rem; color:#ddd;"><i class="fas fa-check-double"></i></div>
                <p class="text-muted mt-2">Tidak ada surat yang menunggu validasi.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- SEMUA PENGAJUAN -->
<div class="card" id="semua">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Riwayat Semua Pengajuan</h3>
    </div>
    <div class="card-body">
        <div style="margin-bottom:15px;">
            <input type="text" id="searchTable" class="form-control" placeholder="Cari..." style="max-width:300px;">
        </div>
        <div class="table-responsive">
            <table id="dataTable">
                <thead>
                    <tr><th>No</th><th>Tanggal</th><th>NIM</th><th>Nama</th><th>Jenis Surat</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($semuaPengajuan as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= date('d/m/Y', strtotime($p['tanggal_pengajuan'])) ?></td>
                        <td><?= htmlspecialchars($p['nim_nip']) ?></td>
                        <td><?= htmlspecialchars($p['nama_mahasiswa']) ?></td>
                        <td><?= htmlspecialchars($p['jenis_surat_nama']) ?></td>
                        <td><span class="badge badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: Setujui -->
<div class="modal-overlay" id="modalSetujui">
    <div class="modal-box">
        <h3><i class="fas fa-check-circle" style="color:var(--success);"></i> Setujui Pengajuan</h3>
        <form method="POST" action="index.php?page=setujui_surat">
            <input type="hidden" name="id" value="">
            <div class="form-group">
                <label>Catatan (opsional)</label>
                <textarea class="form-control" name="catatan_kajur" rows="3" placeholder="Catatan persetujuan..."></textarea>
            </div>
            <div class="d-flex gap-2" style="margin-top:15px;">
                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Setujui</button>
                <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('modalSetujui')">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Tolak -->
<div class="modal-overlay" id="modalTolak">
    <div class="modal-box">
        <h3><i class="fas fa-times-circle" style="color:var(--danger);"></i> Tolak Pengajuan</h3>
        <form method="POST" action="index.php?page=tolak_surat">
            <input type="hidden" name="id" value="">
            <div class="form-group">
                <label>Alasan Penolakan <span class="text-danger">*</span></label>
                <textarea class="form-control" name="catatan_kajur" rows="3" placeholder="Berikan alasan penolakan..." required></textarea>
            </div>
            <div class="d-flex gap-2" style="margin-top:15px;">
                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Tolak</button>
                <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('modalTolak')">Batal</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
