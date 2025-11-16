<?php

namespace Tiny\Core;

use PDO;

class Database
{
    protected PDO $pdo;

    public function __construct($config)
    {
        $this->pdo = new PDO(
            $config['dsn'],
            $config['user'],
            $config['password']
        );
    }
}