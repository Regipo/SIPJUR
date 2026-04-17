<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPJUR</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-bg">
    <div class="login-card">
        <div style="font-size:3rem;">🎓</div>
        <h2>SIPJUR</h2>
        <p class="subtitle">Sistem Informasi Persuratan Jurusan<br>Universitas Palangka Raya</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $err): ?>
                    <div><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="index.php?page=login">
            <div class="form-group">
                <input type="text" class="form-control" id="nim_nip" name="nim_nip" 
                       placeholder="NIM / NIP" value="<?= htmlspecialchars($nim_nip ?? '') ?>" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary">Masuk</button>
        </form>

        <p class="mt-3" style="font-size:0.9rem;">
            Belum punya akun? <a href="index.php?page=register">Buat Akun!</a>
        </p>

        <p class="mt-2" style="font-size:0.75rem; color:#e74c3c;">
            *) Gunakan NIM sebagai username untuk mahasiswa
        </p>
    </div>
</div>
<script src="js/script.js"></script>
</body>
</html>
