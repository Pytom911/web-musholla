<?php
require_once __DIR__ . '/../config/config.php';

$idUser = (int) ($_SESSION['reset_user_id'] ?? 0);
$email = (string) ($_SESSION['reset_email'] ?? '');

if ($idUser <= 0 || $email === '') {
    redirect('auth/lupa_password.php');
}

$status = trim((string) ($_GET['status'] ?? ''));
$pesan = trim((string) ($_GET['pesan'] ?? ''));

$otpAktif = otp_aktif($idUser);
$sisaPercobaan = $otpAktif
    ? max(0, OTP_MAX_ATTEMPTS - (int) $otpAktif['attempts'])
    : 0;
$sisaCooldown = otp_sisa_cooldown($idUser);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - Sistem Informasi Musholla</title>
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

            <!-- Bagian Kanan: Form OTP -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white">
                <div class="login-container">

                    <div class="login-icon mx-auto mb-4">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>

                    <h1 class="login-title text-center mb-2">Verifikasi OTP</h1>
                    <p class="login-subtitle text-center mb-4">
                        Masukkan kode 6 digit yang dikirim ke
                        <strong><?= htmlspecialchars(mask_email($email)) ?></strong>
                    </p>

                    <?php if ($status === 'terkirim'): ?>
                        <div class="success-msg mb-3">
                            Kode OTP sudah dikirim. Periksa kotak masuk dan folder spam.
                        </div>
                    <?php endif; ?>

                    <?php if ($pesan === 'cooldown'): ?>
                        <div class="alert alert-warning py-2 px-3 small mb-3">
                            Kode sebelumnya masih berlaku. Tunggu
                            <strong id="cooldownText" data-sisa="<?= $sisaCooldown ?>"><?= $sisaCooldown ?></strong>
                            detik sebelum minta kode baru.
                        </div>
                    <?php elseif ($pesan === 'salah'): ?>
                        <div class="error-msg mb-3">
                            Kode OTP salah. Sisa percobaan:
                            <strong><?= $sisaPercobaan ?></strong>.
                        </div>
                    <?php elseif ($pesan === 'expired'): ?>
                        <div class="error-msg mb-3">
                            Kode OTP sudah kedaluwarsa. Minta kode baru.
                        </div>
                    <?php elseif ($pesan === 'locked'): ?>
                        <div class="error-msg mb-3">
                            Terlalu banyak percobaan. Minta kode OTP baru.
                        </div>
                    <?php elseif ($pesan === 'otp_invalid'): ?>
                        <div class="error-msg mb-3">Kode OTP harus 6 digit angka.</div>
                    <?php elseif ($pesan === 'password_lemah'): ?>
                        <div class="error-msg mb-3">Password baru minimal 8 karakter.</div>
                    <?php elseif ($pesan === 'password_beda'): ?>
                        <div class="error-msg mb-3">Konfirmasi password tidak sama.</div>
                    <?php elseif ($pesan === 'sesi'): ?>
                        <div class="error-msg mb-3">Sesi form kedaluwarsa. Silakan coba lagi.</div>
                    <?php elseif ($pesan === 'gagal'): ?>
                        <div class="error-msg mb-3">
                            Terjadi kesalahan saat menyimpan password. Coba lagi.
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= url('auth/simpan_password.php') ?>" id="otpForm">
                        <?= csrf_field() ?>

                        <div class="mb-3 input-icon-group">
                            <i class="bi bi-123 input-icon"></i>
                            <input type="text"
                                class="form-control custom-input otp-input"
                                name="otp"
                                placeholder="Kode OTP"
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                maxlength="6"
                                autocomplete="one-time-code"
                                required>
                        </div>

                        <div class="mb-3 input-icon-group">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password"
                                class="form-control custom-input"
                                name="password"
                                id="passwordBaru"
                                placeholder="Password baru (min. 8 karakter)"
                                minlength="8"
                                maxlength="72"
                                autocomplete="new-password"
                                required>
                            <i class="bi bi-eye-slash input-icon-toggle" data-toggle="passwordBaru"></i>
                        </div>

                        <div class="mb-4 input-icon-group">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <input type="password"
                                class="form-control custom-input"
                                name="password_konfirmasi"
                                id="passwordKonfirmasi"
                                placeholder="Ulangi password baru"
                                minlength="8"
                                maxlength="72"
                                autocomplete="new-password"
                                required>
                            <i class="bi bi-eye-slash input-icon-toggle" data-toggle="passwordKonfirmasi"></i>
                        </div>

                        <button type="submit" class="btn btn-signin w-100 mb-3" id="simpanBtn">
                            <i class="bi bi-check2-circle me-2"></i>Simpan Password
                        </button>
                    </form>

                    <form method="POST" action="<?= url('auth/kirim_otp.php') ?>" class="text-center mb-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

                        <button type="submit"
                            class="btn btn-link forgot-link p-0 text-small"
                            id="kirimUlangBtn"
                            <?= $sisaCooldown > 0 ? 'disabled' : '' ?>>
                            Kirim ulang kode OTP
                        </button>

                        <?php if ($sisaCooldown > 0): ?>
                            <div class="text-muted small mt-1">
                                Bisa dikirim ulang dalam
                                <span id="cooldownUlang" data-sisa="<?= $sisaCooldown ?>"><?= $sisaCooldown ?></span>
                                detik
                            </div>
                        <?php endif; ?>
                    </form>

                    <a href="<?= url('auth/lupa_password.php') ?>" class="btn btn-back w-100 mb-4">
                        <i class="bi bi-arrow-left me-2"></i>Ganti Email
                    </a>

                    <div class="text-center login-footer">
                        <p class="mb-1 text-muted small">
                            Kode berlaku <?= OTP_TTL_MINUTES ?> menit dan hanya bisa dipakai satu kali.
                        </p>
                        <p class="mb-0 fw-bold footer-brand small">SMK Negeri 1 Kraksaan</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle tampil/sembunyi password
        document.querySelectorAll('[data-toggle]').forEach(function (icon) {
            icon.addEventListener('click', function () {
                const input = document.getElementById(icon.dataset.toggle);
                if (!input) return;

                const hidden = input.type === 'password';
                input.type = hidden ? 'text' : 'password';
                icon.classList.toggle('bi-eye', hidden);
                icon.classList.toggle('bi-eye-slash', !hidden);
            });
        });

        // Hitung mundur cooldown kirim ulang
        function mulaiCooldown(el, tombol) {
            if (!el) return;

            let sisa = parseInt(el.dataset.sisa || '0', 10);

            const tick = function () {
                if (sisa <= 0) {
                    el.textContent = '0';
                    if (tombol) {
                        tombol.disabled = false;
                    }
                    clearInterval(timer);
                    return;
                }

                el.textContent = String(sisa);
                sisa -= 1;
            };

            const timer = setInterval(tick, 1000);
            tick();
        }

        mulaiCooldown(
            document.getElementById('cooldownUlang'),
            document.getElementById('kirimUlangBtn')
        );
        mulaiCooldown(
            document.getElementById('cooldownText'),
            document.getElementById('kirimUlangBtn')
        );

        // Cegah submit ganda
        const otpForm = document.getElementById('otpForm');
        const simpanBtn = document.getElementById('simpanBtn');

        if (otpForm && simpanBtn) {
            otpForm.addEventListener('submit', function () {
                simpanBtn.disabled = true;
                simpanBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            });
        }
    </script>
</body>

</html>
