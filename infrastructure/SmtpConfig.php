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
            'username'   => 'auctivityteam@gmail.com',
            'password'   => 'faem irac ejcd sukh',
            'from_email' => 'auctivityteam@gmail.com',
            'from_name'  => 'Auctivity',
            'encryption' => 'tls',
        ];
    }
}