<?php

namespace infrastructure;

use Exception;

//this section was written mostly with ChatGPT
//Gmail SMTP set up
class Mailer
{
    private $host;
    private $port;
    private $username;
    private $password;
    private $fromEmail;
    private $fromName;

    public function __construct(array $config)
    {
        $this->host      = $config['host'];
        $this->port      = $config['port'];
        $this->username  = $config['username'];
        $this->password  = $config['password'];
        $this->fromEmail = $config['from_email'];
        $this->fromName  = $config['from_name'];
    }

//    public function send($to, $subject, $message)
//    {
//        // Connect to SMTP server
//        $socket = fsockopen($this->host, $this->port, $errno, $errstr, 10);
//
//        if (!$socket)
//        {
//            throw new Exception("SMTP connection failed: $errstr ($errno)");
//        }
//
//        // Read server response
//        $this->getResponse($socket);
//
//        // Say EHLO
//        fwrite($socket, "EHLO {$this->host}\r\n");
//        $this->getResponse($socket);
//
//        // Start TLS
//        fwrite($socket, "STARTTLS\r\n");
//        $this->getResponse($socket);
//
//        // Enable crypto
//        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT))
//        {
//            throw new Exception("Failed to enable TLS encryption");
//        }
//
//        // EHLO again after TLS
//        fwrite($socket, "EHLO {$this->host}\r\n");
//        $this->getResponse($socket);
//
//        // Authenticate
//        fwrite($socket, "AUTH LOGIN\r\n");
//        $this->getResponse($socket);
//
//        fwrite($socket, base64_encode($this->username) . "\r\n");
//        $this->getResponse($socket);
//
//        fwrite($socket, base64_encode($this->password) . "\r\n");
//        $this->getResponse($socket);
//
//        // MAIL FROM
//        fwrite($socket, "MAIL FROM:<{$this->fromEmail}>\r\n");
//        $this->getResponse($socket);
//
//        // RCPT TO
//        fwrite($socket, "RCPT TO:<$to>\r\n");
//        $this->getResponse($socket);
//
//        // DATA
//        fwrite($socket, "DATA\r\n");
//        $this->getResponse($socket);
//
//        // Send headers + message
//        $headers  = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
//        $headers .= "To: $to\r\n";
//        $headers .= "Subject: $subject\r\n";
//        $headers .= "MIME-Version: 1.0\r\n";
//        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
//
//        fwrite($socket, $headers . "\r\n" . $message . "\r\n.\r\n");
//        $this->getResponse($socket);
//
//        // QUIT
//        fwrite($socket, "QUIT\r\n");
//        $this->getResponse($socket);
//
//        fclose($socket);
//
//        return true;
//    }
//
//    // Helper to read server response and check for errors
//    private function getResponse($socket)
//    {
//        $response = '';
//        while ($line = fgets($socket, 515)) {
//            $response .= $line;
//            if (substr($line, 3, 1) === ' ') break; // end of multi-line response
//        }
//
//        if (!preg_match('/^[23]/', $response)) {
//            throw new Exception("SMTP Error: $response");
//        }
//
//        return $response;
//    }
}