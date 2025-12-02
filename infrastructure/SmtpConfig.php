<?php

namespace infrastructure;

class SmtpConfig
{
    public function __construct(){}
    public function getSmtpConfig()
    {
        return [
            'host'       => 'smtp.gmail.com',
            'port'       => 587,
            'username'   => 'auctivity@gmail.com',
            'password'   => 'cugc sjfq pexs rbks',
            'from_email' => 'auctivity@gmail.com',
            'from_name'  => 'Auctivity',
            'encryption' => 'tls',
        ];
    }
}