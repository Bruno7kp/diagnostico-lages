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
        parent::__construct($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'"));
    }
}
