<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('auth/lupa_password.php');
}

if (!csrf_check()) {
    redirect('auth/lupa_password.php?error=sesi');
}

$email = strtolower(trim((string) post('email')));

if ($email === '') {
    redirect('auth/lupa_password.php?error=email_kosong');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect('auth/lupa_password.php?error=email_invalid');
}

try {
    $user = cari_user_by_email($email);

    /*
     * Email tidak terdaftar tetap dijawab "terkirim" agar halaman ini
     * tidak bisa dipakai menebak email mana yang punya akun.
     */
    if (!$user) {
        error_log('Reset password: email tidak terdaftar (' . $email . ')');
        redirect('auth/lupa_password.php?status=terkirim');
    }

    $idUser = (int) $user['id_user'];
    $nama = (string) ($user['nama'] ?? $user['username']);

    $_SESSION['reset_user_id'] = $idUser;
    $_SESSION['reset_email'] = (string) $user['email'];

    if (otp_sisa_cooldown($idUser) > 0) {
        redirect('auth/verifikasi_otp.php?pesan=cooldown');
    }

    if (otp_terlalu_banyak_permintaan(client_ip())) {
        error_log('Reset password: rate limit IP tercapai');
        redirect('auth/lupa_password.php?status=terkirim');
    }

    $otp = otp_buat($idUser, client_ip());

    $hasil = kirim_email(
        (string) $user['email'],
        'Kode OTP Reset Password - Sistem Informasi Musholla',
        email_otp_html($nama, $otp, OTP_TTL_MINUTES)
    );

    if (!$hasil['ok']) {
        error_log(
            'Reset password: gagal kirim OTP ke '
            . $user['email'] . ' - ' . $hasil['error']
        );
        redirect('auth/lupa_password.php?error=gagal_kirim');
    }

    session_regenerate_id(true);

    redirect('auth/verifikasi_otp.php?status=terkirim');
} catch (Throwable $e) {
    error_log('Reset password (kirim OTP) gagal: ' . $e->getMessage());
    redirect('auth/lupa_password.php?error=gagal_kirim');
}
