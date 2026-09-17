<?php

namespace Model;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {

            try {

                $dsn = "mysql:host=" . DB_HOST .
                       ";port=" . DB_PORT .
                       ";dbname=" . DB_NAME .
                       ";charset=utf8mb4";

                self::$instance = new PDO(
                    $dsn,
                    DB_USER,
                    DB_PASSWORD
                );

                self::$instance->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

                self::$instance->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE,
                    PDO::FETCH_ASSOC
                );

            } catch (PDOException $error) {

                throw new PDOException(
                    "Erro ao conectar com o banco de dados: "
                    . $error->getMessage()
                );
            }
        }

        return self::$instance;
    }
}
