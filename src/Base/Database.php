<?php


namespace App\Base;


use PDO;

class Database extends PDO
{
    public function __construct()
    {
        $dsn = getenv("DB_DSN");
        $username = getenv("DB_USER");
        $password = getenv("DB_PASS");
        parent::__construct($dsn, $username, $password);
    }
}
