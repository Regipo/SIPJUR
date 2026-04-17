<?php $pageTitle = 'Detail Pengajuan'; require_once __DIR__ . '/../layouts/header.php'; ?>

<?php if ($detail): ?>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-file-alt"></i> Detail Pengajuan Surat</h3>
        <a href="index.php?page=admin_dashboard" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
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
                <p class="small text-muted">Email / No. HP</p>
                <p><?= htmlspecialchars($detail['email'] ?? '-') ?> | <?= htmlspecialchars($detail['no_hp'] ?? '-') ?></p>
            </div>
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
                <p class="small text-muted">Keterangan Mahasiswa</p>
                <p><?= nl2br(htmlspecialchars($detail['keterangan'] ?: '-')) ?></p>
            </div>
            <?php if ($detail['file_lampiran']): ?>
            <div style="grid-column:1/-1;">
                <p class="small text-muted">File Lampiran</p>
                <a href="uploads/<?= $detail['file_lampiran'] ?>" class="btn btn-info btn-sm" target="_blank">
                    <i class="fas fa-paperclip"></i> Lihat Lampiran
                </a>
            </div>
            <?php endif; ?>
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

        <!-- Aksi berdasarkan status -->
        <div class="d-flex gap-2" style="margin-top:15px;">
            <?php if ($detail['status'] === 'diajukan'): ?>
                <button class="btn btn-primary btn-sm" onclick="setActionId('modalTeruskan', <?= $detail['id'] ?>)">
                    <i class="fas fa-paper-plane"></i> Teruskan ke Kajur
                </button>
            <?php elseif ($detail['status'] === 'disetujui'): ?>
                <button class="btn btn-success btn-sm" onclick="setActionId('modalSelesaikan', <?= $detail['id'] ?>)">
                    <i class="fas fa-check-double"></i> Selesaikan Surat
                </button>
            <?php endif; ?>
        </div>

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

<!-- MODAL: Teruskan ke Kajur -->
<div class="modal-overlay" id="modalTeruskan">
    <div class="modal-box">
        <h3><i class="fas fa-paper-plane"></i> Teruskan ke Kajur</h3>
        <form method="POST" action="index.php?page=teruskan_surat">
            <input type="hidden" name="id" value="">
            <div class="form-group">
                <label>Catatan untuk Kajur (opsional)</label>
                <textarea class="form-control" name="catatan_admin" rows="3" placeholder="Tambahkan catatan..."></textarea>
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

<?php else: ?>
    <div class="alert alert-danger">Pengajuan tidak ditemukan.</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
