<?php


namespace SecureShell;


use Codeception\Module;
use phpseclib\Net\SSH2;

class SSH extends Module
{
    /**
     * @var SSH2
     */
    private $connection;

    /**
     * @var string
     */
    private $output;

    public function openConnection($host, $port, $username, $password)
    {
        $ssh = new SSH2($host, $port);
        if ($ssh->login($username, $password)) {
            $this->connection = $ssh;
        }
    }

    public function execCommand($command)
    {
        $this->output = $this->connection->exec($command);
    }

    public function grabOutput()
    {
        return $this->output;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}