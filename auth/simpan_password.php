<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('auth/sign_in.php');
}

$idUser = (int) ($_SESSION['reset_user_id'] ?? 0);
$email = (string) ($_SESSION['reset_email'] ?? '');

if ($idUser <= 0 || $email === '') {
    redirect('auth/lupa_password.php');
}

if (!csrf_check()) {
    redirect('auth/verifikasi_otp.php?pesan=sesi');
}

$otp = preg_replace('/\s+/', '', (string) post('otp'));
$password = (string) post('password');
$konfirmasi = (string) post('password_konfirmasi');

if (!is_string($otp) || !preg_match('/^[0-9]{6}$/', $otp)) {
    redirect('auth/verifikasi_otp.php?pesan=otp_invalid');
}

if (strlen($password) < 8 || strlen($password) > 72) {
    redirect('auth/verifikasi_otp.php?pesan=password_lemah');
}

if ($password !== $konfirmasi) {
    redirect('auth/verifikasi_otp.php?pesan=password_beda');
}

try {
    $user = cari_user_by_email($email);

    if (!$user || (int) $user['id_user'] !== $idUser) {
        unset($_SESSION['reset_user_id'], $_SESSION['reset_email']);
        redirect('auth/lupa_password.php');
    }

    $otpRow = otp_aktif($idUser);

    if (!$otpRow) {
        redirect('auth/verifikasi_otp.php?pesan=expired');
    }

    if ((int) $otpRow['attempts'] >= OTP_MAX_ATTEMPTS) {
        redirect('auth/verifikasi_otp.php?pesan=locked');
    }

    if (!password_verify($otp, (string) $otpRow['otp_hash'])) {
        $percobaan = otp_catat_gagal((int) $otpRow['id_otp']);

        if ($percobaan >= OTP_MAX_ATTEMPTS) {
            redirect('auth/verifikasi_otp.php?pesan=locked');
        }

        redirect('auth/verifikasi_otp.php?pesan=salah');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $connect->prepare(
        'UPDATE users SET password = ? WHERE id_user = ?'
    );
    $stmt->bind_param('si', $hash, $idUser);
    $stmt->execute();
    $stmt->close();

    otp_hapus($idUser);

    unset($_SESSION['reset_user_id'], $_SESSION['reset_email']);
    session_regenerate_id(true);

    redirect('auth/sign_in.php?pesan=reset_berhasil');
} catch (Throwable $e) {
    error_log('Reset password (simpan) gagal: ' . $e->getMessage());
    redirect('auth/verifikasi_otp.php?pesan=gagal');
}
