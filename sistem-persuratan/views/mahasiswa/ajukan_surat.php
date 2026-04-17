<?php $pageTitle = 'Ajukan Surat'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-file-alt"></i> Form Pengajuan Surat
            <?= $jenisSuratDetail ? ' - ' . htmlspecialchars($jenisSuratDetail['nama']) : '' ?>
        </h3>
        <a href="index.php?page=mahasiswa_dashboard" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $err): ?>
                    <div><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form id="ajukanForm" method="POST" action="index.php?page=proses_ajukan" enctype="multipart/form-data">
            <div class="form-group">
                <label>Jenis Surat <span class="text-danger">*</span></label>
                <select class="form-control" id="jenis_surat_id" name="jenis_surat_id" required>
                    <option value="">-- Pilih Jenis Surat --</option>
                    <?php
                    $current_kat = '';
                    foreach ($allJenisSurat as $js):
                        if ($js['kategori'] !== $current_kat):
                            if ($current_kat !== '') echo '</optgroup>';
                            $kat_label = ucfirst(str_replace('_', ' ', $js['kategori']));
                            echo "<optgroup label='Bidang {$kat_label}'>";
                            $current_kat = $js['kategori'];
                        endif;
                    ?>
                        <option value="<?= $js['id'] ?>" 
                            <?= ($jenisSuratDetail && $jenisSuratDetail['id'] == $js['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($js['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                    <?php if ($current_kat !== '') echo '</optgroup>'; ?>
                </select>
            </div>

            <?php if ($jenisSuratDetail && $jenisSuratDetail['persyaratan']): ?>
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Persyaratan:</strong><br>
                    <?= nl2br(htmlspecialchars($jenisSuratDetail['persyaratan'])) ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Keterangan / Keperluan</label>
                <textarea class="form-control" name="keterangan" rows="4" 
                          placeholder="Jelaskan keperluan pengajuan surat ini..."><?= htmlspecialchars($keterangan ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Lampiran (opsional)</label>
                <input type="file" class="form-control" id="file_lampiran" name="file_lampiran" 
                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                <small class="text-muted">Format: PDF, JPG, PNG, DOC, DOCX. Maksimal 5MB.</small>
            </div>

            <div style="margin-top:20px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
