<?php $pageTitle = 'Detail Pengajuan'; require_once __DIR__ . '/../layouts/header.php'; ?>

<?php if ($detail): ?>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-file-alt"></i> Detail Pengajuan Surat</h3>
        <a href="index.php?page=mahasiswa_dashboard" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
    <div class="card-body">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:20px;">
            <div>
                <p class="small text-muted">Jenis Surat</p>
                <p style="font-weight:600;"><?= htmlspecialchars($detail['jenis_surat_nama']) ?></p>
            </div>
            <div>
                <p class="small text-muted">Status</p>
                <span class="badge badge-<?= $detail['status'] ?>"><?= ucfirst($detail['status']) ?></span>
            </div>
            <div>
                <p class="small text-muted">Tanggal Pengajuan</p>
                <p><?= date('d F Y, H:i', strtotime($detail['tanggal_pengajuan'])) ?></p>
            </div>
            <div>
                <p class="small text-muted">Nomor Surat</p>
                <p><?= $detail['nomor_surat'] ?: '-' ?></p>
            </div>
            <div style="grid-column:1/-1;">
                <p class="small text-muted">Keterangan</p>
                <p><?= nl2br(htmlspecialchars($detail['keterangan'] ?: '-')) ?></p>
            </div>
            <?php if ($detail['catatan_admin']): ?>
            <div style="grid-column:1/-1;">
                <p class="small text-muted">Catatan Admin</p>
                <p><?= nl2br(htmlspecialchars($detail['catatan_admin'])) ?></p>
            </div>
            <?php endif; ?>
            <?php if ($detail['catatan_kajur']): ?>
            <div style="grid-column:1/-1;">
                <p class="small text-muted">Catatan Kajur</p>
                <p><?= nl2br(htmlspecialchars($detail['catatan_kajur'])) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($detail['status'] === 'selesai' && $detail['file_surat_selesai']): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Surat Anda telah selesai diproses!
                <a href="uploads/<?= $detail['file_surat_selesai'] ?>" class="btn btn-success btn-sm" download style="margin-left:10px;">
                    <i class="fas fa-download"></i> Unduh Surat
                </a>
            </div>
        <?php endif; ?>

        <!-- Timeline Log -->
        <h4 style="margin:20px 0 15px;"><i class="fas fa-history"></i> Riwayat Proses</h4>
        <div class="timeline">
            <?php foreach ($logAktivitas as $log): ?>
            <div class="timeline-item">
                <div class="time"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></div>
                <div class="action"><?= htmlspecialchars($log['aksi']) ?></div>
                <div class="by"><?= htmlspecialchars($log['nama_user']) ?> (<?= ucfirst($log['role']) ?>)</div>
                <?php if ($log['keterangan']): ?>
                    <div class="small text-muted"><?= htmlspecialchars($log['keterangan']) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
    <div class="alert alert-danger">Pengajuan tidak ditemukan.</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
