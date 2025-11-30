<?php
namespace infrastructure;
class SmtpConfig
{
    public function __construct(){}
    public function getSmtpConfig()
    {
        return [
            'host'       => 'smtp.gmail.com',
            'port'       => 587, // Use 465 for SSL
            'username'   => 'auctivity@gmail.com', // Your Gmail address
            'password'   => 'cugc sjfq pexs rbks',    // Use an App Password if 2FA is enabled
            'from_email' => 'auctivity@gmail.com', // Same as username
            'from_name'  => 'Auctivity',            // Display name
            'encryption' => 'tls',                  // 'tls' for port 587, 'ssl' for 465
        ];
    }
}
