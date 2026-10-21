<?php

/**
 * config/mail.php
 * Klien Resend (REST API) - tanpa SDK/composer, cukup cURL.
 *
 * Dipakai untuk mengirim kode OTP lupa password. Semua kegagalan
 * dikembalikan sebagai array dan dicatat ke log, tidak pernah
 * ditampilkan mentah ke pengguna.
 */

/**
 * Kirim email HTML lewat Resend.
 *
 * @return array{ok: bool, status: int, error: string, id: ?string}
 */
function kirim_email(string $to, string $subject, string $html): array
{
    $to = trim($to);

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return [
            'ok' => false,
            'status' => 0,
            'error' => 'Alamat email tidak valid',
            'id' => null,
        ];
    }

    if (RESEND_API_KEY === '') {

        /*
         * Tanpa API key alur tetap bisa diuji di lokal: isi email
         * (termasuk OTP) ditulis ke storage/logs/mail.log.
         * Di produksi ini dianggap gagal, bukan sukses palsu.
         */
        if (APP_ENV === 'development') {
            mail_log_dev($to, $subject, $html);

            return [
                'ok' => true,
                'status' => 0,
                'error' => '',
                'id' => 'dev-log',
            ];
        }

        error_log('Resend: RESEND_API_KEY belum diisi');

        return [
            'ok' => false,
            'status' => 0,
            'error' => 'Konfigurasi email belum lengkap',
            'id' => null,
        ];
    }

    if (!function_exists('curl_init')) {
        error_log('Resend: ekstensi cURL tidak aktif');

        return [
            'ok' => false,
            'status' => 0,
            'error' => 'Ekstensi cURL tidak tersedia',
            'id' => null,
        ];
    }

    $payload = [
        'from' => MAIL_FROM,
        'to' => [$to],
        'subject' => $subject,
        'html' => $html,
    ];

    if (MAIL_REPLY_TO !== '') {
        $payload['reply_to'] = MAIL_REPLY_TO;
    }

    $ch = curl_init('https://api.resend.com/emails');

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . RESEND_API_KEY,
            'Content-Type: application/json',
            'Accept: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ),
    ]);

    $body = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);

    curl_close($ch);

    if ($body === false || $curlError !== '') {
        error_log('Resend cURL gagal: ' . $curlError);

        return [
            'ok' => false,
            'status' => $status,
            'error' => 'Koneksi ke layanan email gagal',
            'id' => null,
        ];
    }

    $json = json_decode((string) $body, true);

    if ($status < 200 || $status >= 300) {
        error_log(
            'Resend HTTP ' . $status . ': '
            . mb_substr((string) $body, 0, 500)
        );

        return [
            'ok' => false,
            'status' => $status,
            'error' => is_array($json) && isset($json['message'])
                ? (string) $json['message']
                : 'Email ditolak oleh layanan email',
            'id' => null,
        ];
    }

    return [
        'ok' => true,
        'status' => $status,
        'error' => '',
        'id' => is_array($json) ? ($json['id'] ?? null) : null,
    ];
}


/**
 * Catat isi email ke log saat mode development tanpa API key.
 */
function mail_log_dev(string $to, string $subject, string $html): void
{
    $dir = STORAGE_PATH . '/logs';

    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    $line = '[' . date('Y-m-d H:i:s') . '] ke=' . $to
        . ' subjek=' . $subject . PHP_EOL
        . strip_tags(str_replace('<br>', PHP_EOL, $html)) . PHP_EOL
        . str_repeat('-', 60) . PHP_EOL;

    @file_put_contents($dir . '/mail.log', $line, FILE_APPEND | LOCK_EX);
}


/**
 * Template email OTP (HTML sederhana, aman untuk email client).
 */
function email_otp_html(string $nama, string $otp, int $menit): string
{
    $nama = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
    $otp = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');

    return <<<HTML
<!DOCTYPE html>
<html lang="id">
<body style="margin:0;padding:0;background:#f4f6f5;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#ffffff;border-radius:12px;border:1px solid #e5e7eb;">
          <tr>
            <td style="padding:28px 28px 8px;">
              <p style="margin:0 0 4px;font-size:13px;letter-spacing:.08em;text-transform:uppercase;color:#145a2e;">Sistem Informasi Musholla</p>
              <h1 style="margin:0;font-size:20px;color:#111827;">Reset Password</h1>
            </td>
          </tr>
          <tr>
            <td style="padding:8px 28px 0;">
              <p style="margin:0 0 16px;font-size:14px;line-height:1.6;">Halo {$nama},</p>
              <p style="margin:0 0 20px;font-size:14px;line-height:1.6;">Gunakan kode berikut untuk mengatur ulang password akun Anda:</p>
              <div style="margin:0 0 20px;padding:16px;text-align:center;background:#f0f7f2;border:1px dashed #145a2e;border-radius:10px;">
                <span style="font-size:32px;font-weight:700;letter-spacing:.35em;color:#145a2e;">{$otp}</span>
              </div>
              <p style="margin:0 0 8px;font-size:14px;line-height:1.6;">Kode berlaku <strong>{$menit} menit</strong> dan hanya bisa dipakai satu kali.</p>
              <p style="margin:0 0 20px;font-size:13px;line-height:1.6;color:#6b7280;">Abaikan email ini bila Anda tidak meminta reset password. Password Anda tidak berubah sampai kode di atas dipakai.</p>
            </td>
          </tr>
          <tr>
            <td style="padding:0 28px 24px;">
              <hr style="border:none;border-top:1px solid #e5e7eb;margin:0 0 16px;">
              <p style="margin:0;font-size:12px;color:#9ca3af;">Email otomatis, mohon tidak dibalas.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
}
