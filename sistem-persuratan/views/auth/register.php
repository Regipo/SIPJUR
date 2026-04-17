<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SIPJUR</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-bg">
    <div class="login-card" style="max-width:500px;">
        <div style="font-size:3rem;">🎓</div>
        <h2>Registrasi Akun</h2>
        <p class="subtitle">Buat akun mahasiswa SIPJUR</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $err): ?>
                    <div><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="index.php?page=register">
            <div class="form-group">
                <label>NIM <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nim_nip" name="nim_nip" 
                       placeholder="Masukkan NIM" value="<?= htmlspecialchars($nim ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama" name="nama" 
                       placeholder="Masukkan nama lengkap" value="<?= htmlspecialchars($nama ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" 
                       placeholder="contoh@email.com" value="<?= htmlspecialchars($email ?? '') ?>">
            </div>
            <div class="form-group">
                <label>No. HP</label>
                <input type="text" class="form-control" name="no_hp" 
                       placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($no_hp ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Minimal 6 karakter" required>
            </div>
            <div class="form-group">
                <label>Konfirmasi Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                       placeholder="Ulangi password" required>
            </div>
            <button type="submit" class="btn btn-primary">Daftar</button>
        </form>

        <p class="mt-3" style="font-size:0.9rem;">
            Sudah punya akun? <a href="index.php?page=login">Login di sini</a>
        </p>
    </div>
</div>
<script src="js/script.js"></script>
</body>
</html>
