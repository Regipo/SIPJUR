<?php $pageTitle = 'Detail Pengajuan'; require_once __DIR__ . '/../layouts/header.php'; ?>

<?php if ($detail): ?>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-file-alt"></i> Detail Pengajuan Surat</h3>
        <a href="index.php?page=kajur_dashboard" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
    <div class="card-body">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:20px;">
            <div>
                <p class="small text-muted">Nama Mahasiswa</p>
                <p style="font-weight:600;"><?= htmlspecialchars($detail['nama_mahasiswa']) ?></p>
            </div>
            <div>
                <p class="small text-muted">NIM</p>
                <p><?= htmlspecialchars($detail['nim_nip']) ?></p>
            </div>
            <div>
                <p class="small text-muted">Program Studi</p>
                <p><?= htmlspecialchars($detail['program_studi'] ?? '-') ?></p>
            </div>
            <div>
                <p class="small text-muted">Status</p>
                <span class="badge badge-<?= $detail['status'] ?>"><?= ucfirst($detail['status']) ?></span>
            </div>
            <div>
                <p class="small text-muted">Jenis Surat</p>
                <p style="font-weight:600;"><?= htmlspecialchars($detail['jenis_surat_nama']) ?></p>
            </div>
            <div>
                <p class="small text-muted">Tanggal Pengajuan</p>
                <p><?= date('d F Y, H:i', strtotime($detail['tanggal_pengajuan'])) ?></p>
            </div>
            <div style="grid-column:1/-1;">
                <p class="small text-muted">Keterangan Mahasiswa</p>
                <p><?= nl2br(htmlspecialchars($detail['keterangan'] ?: '-')) ?></p>
            </div>
            <?php if ($detail['catatan_admin']): ?>
            <div style="grid-column:1/-1;">
                <p class="small text-muted">Catatan Admin</p>
                <p><?= nl2br(htmlspecialchars($detail['catatan_admin'])) ?></p>
            </div>
            <?php endif; ?>
            <?php if ($detail['file_lampiran']): ?>
            <div style="grid-column:1/-1;">
                <p class="small text-muted">File Lampiran</p>
                <a href="uploads/<?= $detail['file_lampiran'] ?>" class="btn btn-info btn-sm" target="_blank">
                    <i class="fas fa-paperclip"></i> Lihat Lampiran
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Aksi jika status diteruskan -->
        <?php if ($detail['status'] === 'diteruskan'): ?>
        <div class="d-flex gap-2" style="margin-top:15px;">
            <button class="btn btn-success btn-sm" onclick="setActionId('modalSetujui', <?= $detail['id'] ?>)">
                <i class="fas fa-check"></i> Setujui
            </button>
            <button class="btn btn-danger btn-sm" onclick="setActionId('modalTolak', <?= $detail['id'] ?>)">
                <i class="fas fa-times"></i> Tolak
            </button>
        </div>
        <?php endif; ?>

        <!-- Timeline -->
        <h4 style="margin:25px 0 15px;"><i class="fas fa-history"></i> Riwayat Proses</h4>
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

<!-- MODAL: Setujui -->
<div class="modal-overlay" id="modalSetujui">
    <div class="modal-box">
        <h3><i class="fas fa-check-circle" style="color:var(--success);"></i> Setujui Pengajuan</h3>
        <form method="POST" action="index.php?page=setujui_surat">
            <input type="hidden" name="id" value="">
            <div class="form-group">
                <label>Catatan (opsional)</label>
                <textarea class="form-control" name="catatan_kajur" rows="3"></textarea>
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
                <textarea class="form-control" name="catatan_kajur" rows="3" required></textarea>
            </div>
            <div class="d-flex gap-2" style="margin-top:15px;">
                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Tolak</button>
                <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('modalTolak')">Batal</button>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
    <div class="alert alert-danger">Pengajuan tidak ditemukan.</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
