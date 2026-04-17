<?php $pageTitle = 'Dashboard Admin'; require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- STATISTIK -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-primary"><i class="fas fa-file-alt"></i></div>
        <div class="stat-info"><h3><?= $statistik['total'] ?? 0 ?></h3><p>Total Pengajuan</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-info"><i class="fas fa-inbox"></i></div>
        <div class="stat-info"><h3><?= $statistik['diajukan'] ?? 0 ?></h3><p>Pengajuan Baru</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-warning"><i class="fas fa-paper-plane"></i></div>
        <div class="stat-info"><h3><?= $statistik['diteruskan'] ?? 0 ?></h3><p>Menunggu Kajur</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-success"><i class="fas fa-check-double"></i></div>
        <div class="stat-info"><h3><?= $statistik['disetujui'] ?? 0 ?></h3><p>Siap Diproses</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-success"><i class="fas fa-flag-checkered"></i></div>
        <div class="stat-info"><h3><?= $statistik['selesai'] ?? 0 ?></h3><p>Selesai</p></div>
    </div>
</div>

<!-- PENGAJUAN BARU (perlu diteruskan ke Kajur) -->
<div class="card" id="baru">
    <div class="card-header">
        <h3><i class="fas fa-inbox" style="color:var(--info);"></i> Pengajuan Baru - Perlu Diteruskan ke Kajur</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($pengajuanBaru)): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr><th>No</th><th>Tanggal</th><th>NIM</th><th>Nama</th><th>Prodi</th><th>Jenis Surat</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($pengajuanBaru as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['tanggal_pengajuan'])) ?></td>
                        <td><?= htmlspecialchars($p['nim_nip']) ?></td>
                        <td><?= htmlspecialchars($p['nama_mahasiswa']) ?></td>
                        <td><?= htmlspecialchars($p['jenis_surat_nama']) ?></td>
                        <td class="d-flex gap-2">
                            <a href="index.php?page=detail_surat&id=<?= $p['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            <button class="btn btn-primary btn-sm" onclick="setActionId('modalTeruskan', <?= $p['id'] ?>)">
                                <i class="fas fa-paper-plane"></i> Teruskan
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted text-center" style="padding:20px;">Tidak ada pengajuan baru.</p>
        <?php endif; ?>
    </div>
</div>

<!-- SURAT DISETUJUI KAJUR (siap diproses/selesaikan) -->
<div class="card" id="disetujui">
    <div class="card-header">
        <h3><i class="fas fa-check-circle" style="color:var(--success);"></i> Disetujui Kajur - Siap Diproses</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($pengajuanDisetujui)): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr><th>No</th><th>NIM</th><th>Nama</th><th>Jenis Surat</th><th>Catatan Kajur</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($pengajuanDisetujui as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($p['nim_nip']) ?></td>
                        <td><?= htmlspecialchars($p['nama_mahasiswa']) ?></td>
                        <td><?= htmlspecialchars($p['jenis_surat_nama']) ?></td>
                        <td><?= htmlspecialchars($p['catatan_kajur'] ?? '-') ?></td>
                        <td>
                            <button class="btn btn-success btn-sm" onclick="setActionId('modalSelesaikan', <?= $p['id'] ?>)">
                                <i class="fas fa-check-double"></i> Selesaikan
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted text-center" style="padding:20px;">Tidak ada surat yang siap diproses.</p>
        <?php endif; ?>
    </div>
</div>

<!-- SEMUA PENGAJUAN -->
<div class="card" id="semua">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Semua Pengajuan</h3>
    </div>
    <div class="card-body">
        <div style="margin-bottom:15px;">
            <input type="text" id="searchTable" class="form-control" placeholder="Cari..." style="max-width:300px;">
        </div>
        <div class="table-responsive">
            <table id="dataTable">
                <thead>
                    <tr><th>No</th><th>Tanggal</th><th>NIM</th><th>Nama</th><th>Jenis Surat</th><th>Status</th><th>Aksi</th></tr>
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
                        <td>
                            <a href="index.php?page=detail_surat&id=<?= $p['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: Teruskan ke Kajur -->
<div class="modal-overlay" id="modalTeruskan">
    <div class="modal-box">
        <h3><i class="fas fa-paper-plane"></i> Teruskan ke Kajur</h3>
        <form method="POST" action="index.php?page=teruskan_surat">
            <input type="hidden" name="id" value="">
            <div class="form-group">
                <label>Catatan untuk Kajur (opsional)</label>
                <textarea class="form-control" name="catatan_admin" rows="3" placeholder="Tambahkan catatan jika perlu..."></textarea>
            </div>
            <div class="d-flex gap-2" style="margin-top:15px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane"></i> Teruskan</button>
                <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('modalTeruskan')">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Selesaikan Surat -->
<div class="modal-overlay" id="modalSelesaikan">
    <div class="modal-box">
        <h3><i class="fas fa-check-double"></i> Selesaikan Surat</h3>
        <form method="POST" action="index.php?page=selesaikan_surat" enctype="multipart/form-data">
            <input type="hidden" name="id" value="">
            <div class="form-group">
                <label>Nomor Surat <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nomor_surat" placeholder="Contoh: 001/TI/UPR/2025" required>
            </div>
            <div class="form-group">
                <label>Upload Surat Final (PDF ber-TTD digital)</label>
                <input type="file" class="form-control" name="file_surat_selesai" accept=".pdf">
            </div>
            <div class="d-flex gap-2" style="margin-top:15px;">
                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check-double"></i> Selesaikan</button>
                <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('modalSelesaikan')">Batal</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
