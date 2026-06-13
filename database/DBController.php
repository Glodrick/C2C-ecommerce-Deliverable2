<?php

class DBController
{
    protected $host     = 'localhost';
    protected $user     = 'root';
    protected $password = '';
    protected $database = 'c2c_ecommerce';
    protected $port     = 3306;

    public $con = null;

    public function __construct()
    {
        $this->con = mysqli_connect(
            $this->host,
            $this->user,
            $this->password,
            $this->database,
            $this->port
        );

        if (!$this->con || $this->con->connect_error) {
            // In production replace die() with a proper error page
            die(json_encode([
                'success' => false,
                'message' => 'Database connection failed: ' . mysqli_connect_error()
            ]));
        }

        // Set charset to prevent encoding issues
        $this->con->set_charset('utf8mb4');
    }

    public function __destruct()
    {
        $this->closeConnection();
    }

    protected function closeConnection()
    {
        if ($this->con != null) {
            $this->con->close();
            $this->con = null;
        }
    }
}
