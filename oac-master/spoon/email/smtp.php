<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package     spoon
 * @subpackage  email
 *
 * @author      Davy Hellemans <davy@spoon-library.com>
 * @author      Sam Tubbax <sam@sumocoders.be>
 * @since       1.0.0
 */

/**
 * This class is used to handle email through SMTP.
 *
 * @package     spoon
 * @subpackage  email
 *
 * @author      Dave Lens <dave@spoon-library.com>
 * @since       1.0.0
 */
class SpoonEmailSMTP
{
    /** Carriage return line feed, in hex values */
    const CRLF = "\x0d\x0a";

    /** @var resource|null Connection resource */
    private $connection = null;

    /** @var string SMTP host */
    private $host = 'localhost';

    /** @var int SMTP port */
    private $port = 25;

    /** @var string Last replied SMTP code */
    private $repliedCode;

    /** @var string Host reply storage */
    private $replies = '';

    /** @var string|null Security layer (ssl/tls) */
    private $security = null;

    /** @var int Connection timeout */
    private $timeout = 30;

    /**
     * Class constructor.
     *
     * @param string      $host     The host to connect to.
     * @param int         $port     The port to connect on.
     * @param int         $timeout  The timeout to use.
     * @param string|null $security The security to use (ssl, tls).
     */
    /**
 * STARTTLS command, initiates TLS secure connection
 *
 * @return bool
 * @throws SpoonEmailException If TLS cannot be initialized
 */
private function startTLS()
{
    $this->say('STARTTLS');

    if ($this->repliedCode !== 220) {
        throw new SpoonEmailException("TLS initiation failed with code {$this->repliedCode}");
    }

    if (!stream_socket_enable_crypto($this->connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        throw new SpoonEmailException("TLS encryption could not be enabled.");
    }

    return true;
}


    /**
     * Attempts to authenticate with the SMTP host.
     *
     * @param string $username SMTP username.
     * @param string $password SMTP password.
     *
     * @return bool
     */
    public function authenticate($username, $password)
    {
        if (!$this->connection) {
            throw new SpoonEmailException('No SMTP connection found.');
        }

        if ($this->say('AUTH LOGIN') !== 334) {
            return false;
        }

        if ($this->say(base64_encode($username)) !== 334) {
            return false;
        }

        return $this->say(base64_encode($password)) === 235;
    }

    /**
     * Connects to the SMTP host.
     *
     * @return bool
     */
    private function connect()
    {
        if ($this->security === 'ssl') {
            $this->host = 'ssl://' . $this->host;
        }

        $this->connection = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);

        if (!$this->connection) {
            throw new SpoonEmailException('No connection to the SMTP host could be established.');
        }

        $this->saveReply();
        socket_set_timeout($this->connection, $this->timeout, 0);

        return true;
    }

    /**
     * HELO command.
     *
     * @param string|null $host The host that is sent along with the HELO command.
     *
     * @return bool
     */
    private function helo($host = null)
    {
        $host = $host ?: ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $this->say("HELO $host");
        return $this->repliedCode === 250;
    }

    /**
     * Sends a command to the SMTP host.
     *
     * @param string $message The command message.
     *
     * @return int The SMTP response code.
     */
    private function say($message)
    {
        if (fputs($this->connection, $message . self::CRLF) === false) {
            throw new SpoonEmailException('Failed to communicate with the SMTP server.');
        }

        $reply = $this->listen();
        $this->saveReply($reply);
        return $this->repliedCode = $this->getCode($reply);
    }

    /**
     * Listens for a reply from the SMTP server.
     *
     * @return string
     */
    private function listen()
    {
        return (string) @fgets($this->connection, 515);
    }

    /**
     * Extracts the SMTP code from a reply.
     *
     * @param string $reply The SMTP reply string.
     *
     * @return int
     */
    private function getCode($reply)
    {
        return (int) substr($reply, 0, 3);
    }

    /**
     * Saves a reply from the SMTP host.
     *
     * @param string|null $reply The host's reply.
     */
    private function saveReply($reply = null)
    {
        $reply = $reply ?: $this->listen();
        $this->replies .= $reply;
    }

    /**
     * Sends an email.
     *
     * @param string|null $data The email body.
     *
     * @return bool
     */
    public function send($data = null)
    {
        if ($this->say('DATA') !== 354) {
            return false;
        }

        $data = str_replace("\n", "\r\n", $data);
        $this->say($data . self::CRLF . '.');

        return $this->repliedCode === 250;
    }
}