<?php

namespace infrastructure;

use Exception;

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

    public function send($to, $subject, $message)
    {
        //Connect with Gmail SMPT server.
        $connection = fsockopen($this->host, $this->port, $errno, $errstr, 10);
        if (!$connection)
        {
            throw new Exception("Connection failed: $errstr ($errno)");
        }
        $this->getServerResponse($connection);

        //Sends SMTP commands and receives server responses

        //EHLO
        fwrite($connection, "EHLO {$this->host}\r\n");
        $this->getServerResponse($connection);

        //Starts TLS, upgrades plaintext to encrypted TLS
        fwrite($connection, "STARTTLS\r\n");
        $this->getServerResponse($connection);

        stream_socket_enable_crypto($connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

        //EHLO again. STARTTLS resets the session
        fwrite($connection, "EHLO {$this->host}\r\n");
        $this->getServerResponse($connection);

        //Authenticate
        fwrite($connection, "AUTH LOGIN\r\n");
        $this->getServerResponse($connection);

        //Encode username
        fwrite($connection, base64_encode($this->username) . "\r\n");
        $this->getServerResponse($connection);

        //Encode password.
        fwrite($connection, base64_encode($this->password) . "\r\n");
        $this->getServerResponse($connection);

        //Defines sender
        fwrite($connection, "MAIL FROM:<{$this->fromEmail}>\r\n");
        $this->getServerResponse($connection);

        //Defines recipients
        fwrite($connection, "RCPT TO:<$to>\r\n");
        $this->getServerResponse($connection);

        //Notifies server a message is about to be sent
        fwrite($connection, "DATA\r\n");
        $this->getServerResponse($connection);

        //Sets up headers and the message to be sent
        $headers  = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
        $headers .= "To: $to\r\n";
        $headers .= "Subject: $subject\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        //Sends the email
        fwrite($connection, $headers . "\r\n" . $message . "\r\n.\r\n");
        $this->getServerResponse($connection);

        // QUIT
        fwrite($connection, "QUIT\r\n");
        $this->getServerResponse($connection);

        //Close socket
        fclose($connection);
    }

    private function getServerResponse($connection)
    {
        //Handles retrieval of multi-line SMTP response,
        $response = '';
        while(true)
        {
            $line = fgets($connection, 500);
            $response .= $line;

            //Checks for the 3 digit status code at end of SMTP responses. Marks end of response
            if(isset($line[3]) && $line[3] === ' ')
            {
                break;
            }
        }

        //Error handling: catches any error codes and throw exception
        if($response[0] !== '2' && $response[0] !== '3')
        {
            throw new Exception("SMTP Error: $response");
        }

        return $response;
    }
}