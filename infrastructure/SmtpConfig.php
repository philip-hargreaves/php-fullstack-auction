<?php

namespace infrastructure;

class SmtpConfig
{
    public function __construct() {}

    public function getSmtpConfig(): array
    {
        return [
            'host'       => $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com',
            'port'       => (int)($_ENV['SMTP_PORT'] ?? 587),
            'username'   => $_ENV['SMTP_USERNAME'] ?? '',
            'password'   => $_ENV['SMTP_PASSWORD'] ?? '',
            'from_email' => $_ENV['SMTP_FROM_EMAIL'] ?? '',
            'from_name'  => $_ENV['SMTP_FROM_NAME'] ?? 'Auctivity',
            'encryption' => $_ENV['SMTP_ENCRYPTION'] ?? 'tls',
        ];
    }
}