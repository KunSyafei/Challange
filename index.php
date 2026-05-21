<?php
require_once 'config/database.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Monitoring Kegiatan Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/login.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="login-icon">
                    <i class="bi bi-person-badge"></i>
                </div>
                <h3>Monitoring Kegiatan Siswa</h3>
                <p>Silakan login untuk melanjutkan</p>
            </div>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger">Username atau password salah!</div>
            <?php endif; ?>
            
            <form action="api/auth.php" method="POST" class="login-form">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
            <div class="login-footer">
                <p>© 2024 Monitoring Kegiatan Siswa</p>
            </div>
        </div>
    </div>
</body>
</html>