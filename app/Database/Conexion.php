<?php

namespace App\Database;

use PDO;
use PDOException;

/**
 * Clase Conexion
 *
 * Maneja la conexión a la base de datos MySQL usando PDO.
 * Implementa el patrón Singleton para reutilizar una única instancia.
 */
class Conexion
{
    private static ?Conexion $instancia = null;
    private PDO $pdo;

    private function __construct()
    {
        $host     = env('DB_HOST', '127.0.0.1');
        $port     = env('DB_PORT', '3306');
        $database = env('DB_DATABASE', 'finance_db');
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            throw new PDOException("Error al conectar con la base de datos: " . $e->getMessage());
        }
    }

    /**
     * Devuelve la única instancia de Conexion (Singleton).
     */
    public static function obtenerInstancia(): Conexion
    {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }

        return self::$instancia;
    }

    /**
     * Devuelve el objeto PDO para ejecutar consultas.
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    // Evitar clonación y deserialización del Singleton
    private function __clone() {}
    public function __wakeup() {}
}
