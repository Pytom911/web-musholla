<?php

/**
 * config/otp.php
 * Logika kode OTP lupa password.
 *
 * OTP 6 digit disimpan sebagai hash bcrypt (password_hash), bukan
 * plaintext maupun SHA-256. Ruang OTP hanya 1 juta kemungkinan,
 * jadi hash cepat seperti SHA-256 akan mudah dibalik bila DB bocor.
 */

/**
 * Cari user berdasarkan email (dipakai alur lupa password).
 */
function cari_user_by_email(string $email): ?array
{
    global $connect;

    $email = strtolower(trim($email));

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return null;
    }

    $stmt = $connect->prepare(
        'SELECT id_user, nama, username, email, role
         FROM users
         WHERE email = ?
         LIMIT 1'
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $row ?: null;
}


/**
 * Buat OTP baru: hapus OTP lama user, simpan hash, kembalikan OTP asli.
 */
function otp_buat(int $idUser, ?string $ip): string
{
    global $connect;

    $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $hash = password_hash($otp, PASSWORD_DEFAULT);
    $ttl = OTP_TTL_MINUTES;

    $stmt = $connect->prepare('DELETE FROM password_otps WHERE id_user = ?');
    $stmt->bind_param('i', $idUser);
    $stmt->execute();
    $stmt->close();

    // Housekeeping: buang OTP kedaluwarsa milik user lain.
    $connect->query(
        'DELETE FROM password_otps
         WHERE expires_at < DATE_SUB(NOW(), INTERVAL 1 DAY)'
    );

    $sql = 'INSERT INTO password_otps (id_user, otp_hash, expires_at, requested_ip)
            VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ' . $ttl . ' MINUTE), ?)';
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('iss', $idUser, $hash, $ip);
    $stmt->execute();
    $stmt->close();

    return $otp;
}


/**
 * OTP yang masih berlaku (belum dipakai & belum kedaluwarsa).
 */
function otp_aktif(int $idUser): ?array
{
    global $connect;

    $stmt = $connect->prepare(
        'SELECT id_otp, otp_hash, attempts, expires_at, created_at, requested_ip
         FROM password_otps
         WHERE id_user = ?
           AND used_at IS NULL
           AND expires_at > NOW()
         ORDER BY id_otp DESC
         LIMIT 1'
    );
    $stmt->bind_param('i', $idUser);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $row ?: null;
}


/**
 * Sisa waktu cooldown (detik) sebelum boleh minta OTP baru.
 * Dihitung di MySQL agar tidak terpengaruh beda timezone PHP/DB.
 */
function otp_sisa_cooldown(int $idUser): int
{
    global $connect;

    if (OTP_RESEND_COOLDOWN <= 0) {
        return 0;
    }

    $stmt = $connect->prepare(
        'SELECT GREATEST(
                    0,
                    TIMESTAMPDIFF(
                        SECOND,
                        NOW(),
                        DATE_ADD(created_at, INTERVAL ' . OTP_RESEND_COOLDOWN . ' SECOND)
                    )
                ) AS sisa
         FROM password_otps
         WHERE id_user = ?
         ORDER BY id_otp DESC
         LIMIT 1'
    );
    $stmt->bind_param('i', $idUser);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $row ? max(0, (int) $row['sisa']) : 0;
}


/**
 * Batasi permintaan OTP per IP: maksimal 10 kali per jam.
 */
function otp_terlalu_banyak_permintaan(?string $ip): bool
{
    global $connect;

    if ($ip === null || $ip === '') {
        return false;
    }

    $stmt = $connect->prepare(
        'SELECT COUNT(*) AS total
         FROM password_otps
         WHERE requested_ip = ?
           AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)'
    );
    $stmt->bind_param('s', $ip);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return ($row && (int) $row['total'] >= 10);
}


/**
 * Tambah hitungan percobaan salah, kembalikan nilai terbaru.
 */
function otp_catat_gagal(int $idOtp): int
{
    global $connect;

    $stmt = $connect->prepare(
        'UPDATE password_otps SET attempts = attempts + 1 WHERE id_otp = ?'
    );
    $stmt->bind_param('i', $idOtp);
    $stmt->execute();
    $stmt->close();

    $stmt = $connect->prepare(
        'SELECT attempts FROM password_otps WHERE id_otp = ? LIMIT 1'
    );
    $stmt->bind_param('i', $idOtp);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $row ? (int) $row['attempts'] : 0;
}


/**
 * Hapus semua OTP milik user (setelah reset password berhasil).
 */
function otp_hapus(int $idUser): void
{
    global $connect;

    $stmt = $connect->prepare('DELETE FROM password_otps WHERE id_user = ?');
    $stmt->bind_param('i', $idUser);
    $stmt->execute();
    $stmt->close();
}


/**
 * Samarkan email untuk ditampilkan: budi@gmail.com -> b***i@gmail.com
 */
function mask_email(string $email): string
{
    $parts = explode('@', $email, 2);

    if (count($parts) !== 2) {
        return $email;
    }

    [$user, $domain] = $parts;
    $length = mb_strlen($user);

    if ($length <= 1) {
        $masked = $user . '***';
    } elseif ($length === 2) {
        $masked = mb_substr($user, 0, 1) . '***';
    } else {
        $masked = mb_substr($user, 0, 1)
            . str_repeat('*', max(1, $length - 2))
            . mb_substr($user, -1);
    }

    return $masked . '@' . $domain;
}


/**
 * IP klien untuk rate limit.
 */
function client_ip(): ?string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    return is_string($ip) && $ip !== '' ? $ip : null;
}
