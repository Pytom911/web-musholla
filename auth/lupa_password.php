<?php
require_once __DIR__ . '/../config/config.php';

$status = trim((string) ($_GET['status'] ?? ''));
$error = trim((string) ($_GET['error'] ?? ''));
$emailInput = '';

// Membuka halaman ini berarti memulai ulang alur reset.
unset($_SESSION['reset_user_id'], $_SESSION['reset_email']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Sistem Informasi Musholla</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>

<body>

    <div class="container-fluid vh-100 p-0">
        <div class="row g-0 h-100">

            <!-- Bagian Kiri: Branding -->
            <div class="col-lg-6 d-none d-lg-flex brand-panel">
                <div class="brand-pattern"></div>

                <div class="brand-content text-center">
                    <img src="<?= asset('img/musholla_logo.png') ?>" alt="Logo Musholla" class="brand-logo-img mb-4">

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

            <!-- Bagian Kanan: Form Lupa Password -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white">
                <div class="login-container">

                    <div class="login-icon mx-auto mb-4">
                        <i class="bi bi-key-fill"></i>
                    </div>

                    <h1 class="login-title text-center mb-2">Lupa Password</h1>
                    <p class="login-subtitle text-center mb-4">
                        Masukkan email akun Anda. Kami akan mengirim kode OTP
                        untuk mengatur ulang password.
                    </p>

                    <?php if ($status === 'terkirim'): ?>
                        <div class="success-msg mb-3">
                            Jika email tersebut terdaftar, kode OTP sudah dikirim.
                            Periksa kotak masuk dan folder spam.
                        </div>
                    <?php elseif ($error === 'email_kosong'): ?>
                        <div class="error-msg mb-3">Email wajib diisi.</div>
                    <?php elseif ($error === 'email_invalid'): ?>
                        <div class="error-msg mb-3">Format email tidak valid.</div>
                    <?php elseif ($error === 'gagal_kirim'): ?>
                        <div class="error-msg mb-3">
                            Kode OTP gagal dikirim. Coba lagi beberapa saat.
                        </div>
                    <?php elseif ($error === 'sesi'): ?>
                        <div class="error-msg mb-3">
                            Sesi form kedaluwarsa. Silakan coba lagi.
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= url('auth/kirim_otp.php') ?>" id="lupaPasswordForm">
                        <?= csrf_field() ?>

                        <div class="mb-4 input-icon-group">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email"
                                class="form-control custom-input"
                                name="email"
                                placeholder="Email akun"
                                maxlength="150"
                                autocomplete="email"
                                value="<?= htmlspecialchars($emailInput) ?>"
                                required>
                        </div>

                        <button type="submit" class="btn btn-signin w-100 mb-3" id="kirimOtpBtn">
                            <i class="bi bi-send me-2"></i>Kirim Kode OTP
                        </button>

                        <a href="<?= url('auth/sign_in.php') ?>" class="btn btn-back w-100 mb-4">
                            <i class="bi bi-arrow-left me-2"></i>Kembali ke Login
                        </a>
                    </form>

                    <div class="divider-text mb-4">
                        <span>Keamanan Akun</span>
                    </div>

                    <div class="text-center login-footer">
                        <p class="mb-1 text-muted small">
                            Kode OTP berlaku <?= OTP_TTL_MINUTES ?> menit dan hanya
                            bisa dipakai satu kali.
                        </p>
                        <p class="mb-0 fw-bold footer-brand small">SMK Negeri 1 Kraksaan</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const lupaForm = document.getElementById('lupaPasswordForm');
        const kirimBtn = document.getElementById('kirimOtpBtn');

        if (lupaForm && kirimBtn) {
            lupaForm.addEventListener('submit', function () {
                kirimBtn.disabled = true;
                kirimBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
            });
        }
    </script>
</body>

</html>
