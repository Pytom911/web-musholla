<?php
include '../config/connect.php';
session_start();

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($connect, $_POST['username']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);

    $query = mysqli_query($connect, "SELECT * FROM users WHERE username='$username'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        if ($password == $data['password']) {
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role'];

            if ($data['role'] == 'admin') {
                header("Location: ../index.php");
                exit;
            } elseif($data['role'] == 'petugas') {
                header("Location: ../dashboard-petugas.php");
                exit;
            } else {
                header("Location: ../dashboard-user.php");
                exit;
            }
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "User tidak ditemukan!";
    }
};

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Musholla - Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="container-fluid vh-100 p-0">
        <div class="row g-0 h-100">

            <!-- Bagian Kiri: Branding / Ilustrasi -->
            <div class="col-lg-6 d-none d-lg-flex brand-panel">
                <div class="brand-pattern"></div>

                <div class="brand-content text-center">
                    <img src="../img/musholla_logo.png" alt="Logo Musholla" class="brand-logo-img mb-4">

                    <p class="brand-eyebrow mb-1">Sistem Informasi</p>
                    <h1 class="brand-title mb-2">Musholla</h1>
                    <p class="brand-subtitle mb-4">SMK Negeri 1 Kraksaan</p>

                    <div class="brand-divider mb-4">
                        <span></span>
                        <i class="bi bi-asterisk"></i>
                        <span></span>
                    </div>

                    <p class="brand-desc">
                        Kelola informasi kegiatan, keuangan,<br>
                        dan laporan musholla dengan mudah<br>
                        dan terintegrasi.
                    </p>
                </div>

            </div>

            <!-- Bagian Kanan: Form Login -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white">
                <div class="login-container">

                    <div class="login-icon mx-auto mb-4">
                        <i class="bi bi-lock-fill"></i>
                    </div>

                    <h1 class="login-title text-center mb-2">Login</h1>
                    <p class="login-subtitle text-center mb-4">Silakan masuk untuk melanjutkan</p>

                    <?php if(isset($error)): ?>
                        <div class="error-msg mb-3">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form -->
                    <form method="POST" id="loginForm">
                        <div class="mb-3 input-icon-group">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" class="form-control custom-input" name="username" placeholder="Username" required>
                        </div>
                        <div class="mb-4 input-icon-group">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" class="form-control custom-input" name="password" id="passwordField" placeholder="Password" required>
                            <i class="bi bi-eye-slash input-icon-toggle" id="togglePassword"></i>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 text-small">
                            <div class="form-check">
                                <input class="form-check-input custom-checkbox" type="checkbox" id="rememberMe" checked>
                                <label class="form-check-label text-muted" for="rememberMe">
                                    Ingat saya
                                </label>
                            </div>
                            <a href="forget_pass.php" class="forgot-link">Lupa password?</a>
                        </div>

                        <button type="submit" class="btn btn-signin w-100 mb-4" name="login">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </button>
                    </form>

                    <div class="divider-text mb-4">
                        <span>atau masuk dengan</span>
                    </div>

                    <div class="text-center login-footer">
                        <p class="mb-1 text-muted small">&copy; 2026 Sistem Informasi Musholla</p>
                        <p class="mb-0 fw-bold footer-brand small">SMK Negeri 1 Kraksaan</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS: toggle password visibility (UI only, does not affect login logic) -->
    <script>
        const toggleIcon = document.getElementById('togglePassword');
        const passwordField = document.getElementById('passwordField');
        if (toggleIcon && passwordField) {
            toggleIcon.addEventListener('click', function () {
                const isHidden = passwordField.type === 'password';
                passwordField.type = isHidden ? 'text' : 'password';
                toggleIcon.classList.toggle('bi-eye-slash', !isHidden);
                toggleIcon.classList.toggle('bi-eye', isHidden);
            });
        }
    </script>
</body>
</html>