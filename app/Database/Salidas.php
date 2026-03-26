<?php

namespace App\Database;

use PDO;

/**
 * Clase Salidas
 *
 * Gestiona las operaciones CRUD para los registros de salida (egresos)
 * en la tabla `gastos`, filtrando por el tipo de registro 'registro_salida'.
 */
class Salidas
{
    private PDO $pdo;
    private string $codigoTipo = 'registro_salida';
    private GestorFacturas $gestorFacturas;

    public function __construct()
    {
        $this->pdo            = Conexion::obtenerInstancia()->getPDO();
        $this->gestorFacturas = new GestorFacturas();
    }

    /**
     * Registra una nueva salida y guarda la imagen de factura en el servidor.
     *
     * @param  int    $userId  ID del usuario autenticado
     * @param  string $nombre  Descripción / nombre de la salida
     * @param  float  $monto   Monto del egreso
     * @param  string $fecha   Fecha en formato Y-m-d
     * @param  array  $archivo Elemento de $_FILES con la imagen de la factura
     * @return int             ID del registro creado
     */
    public function crear(int $userId, string $nombre, float $monto, string $fecha, array $archivo): int
    {
        $facturaUrl = $this->gestorFacturas->guardar($archivo);
        $tipoId     = $this->obtenerTipoId();

        $sql = "INSERT INTO gastos (tipo_registro_id, user_id, nombre, monto, fecha, factura_url, created_at, updated_at)
                VALUES (:tipo_id, :user_id, :nombre, :monto, :fecha, :factura_url, NOW(), NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':tipo_id'     => $tipoId,
            ':user_id'     => $userId,
            ':nombre'      => $nombre,
            ':monto'       => $monto,
            ':fecha'       => $fecha,
            ':factura_url' => $facturaUrl,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Devuelve todas las salidas de un usuario.
     *
     * @param  int   $userId ID del usuario
     * @return array Lista de salidas
     */
    public function listar(int $userId): array
    {
        $sql = "SELECT g.id, g.nombre, g.monto, g.fecha, g.factura_url, g.created_at
                FROM gastos g
                INNER JOIN tipo_registros tr ON tr.id = g.tipo_registro_id
                WHERE tr.codigo = :codigo AND g.user_id = :user_id
                ORDER BY g.fecha DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':codigo' => $this->codigoTipo, ':user_id' => $userId]);

        return $stmt->fetchAll();
    }

    /**
     * Busca una salida por su ID.
     *
     * @param  int        $id     ID del registro
     * @param  int        $userId ID del usuario (para verificar pertenencia)
     * @return array|null         Datos del registro o null si no existe
     */
    public function buscarPorId(int $id, int $userId): ?array
    {
        $sql = "SELECT g.id, g.nombre, g.monto, g.fecha, g.factura_url, g.created_at
                FROM gastos g
                INNER JOIN tipo_registros tr ON tr.id = g.tipo_registro_id
                WHERE g.id = :id AND g.user_id = :user_id AND tr.codigo = :codigo";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id, ':user_id' => $userId, ':codigo' => $this->codigoTipo]);

        $resultado = $stmt->fetch();
        return $resultado !== false ? $resultado : null;
    }

    /**
     * Actualiza una salida existente.
     *
     * @param  int    $id         ID del registro a actualizar
     * @param  int    $userId     ID del usuario
     * @param  string $nombre     Nuevo nombre/descripción
     * @param  float  $monto      Nuevo monto
     * @param  string $fecha      Nueva fecha
     * @param  string $facturaUrl Nueva ruta de factura
     * @return bool               True si se actualizó correctamente
     */
    public function actualizar(int $id, int $userId, string $nombre, float $monto, string $fecha, string $facturaUrl): bool
    {
        $sql = "UPDATE gastos
                SET nombre = :nombre, monto = :monto, fecha = :fecha, factura_url = :factura_url, updated_at = NOW()
                WHERE id = :id AND user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nombre'      => $nombre,
            ':monto'       => $monto,
            ':fecha'       => $fecha,
            ':factura_url' => $facturaUrl,
            ':id'          => $id,
            ':user_id'     => $userId,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Elimina una salida por su ID y borra su factura del servidor.
     *
     * @param  int  $id     ID del registro
     * @param  int  $userId ID del usuario
     * @return bool         True si se eliminó correctamente
     */
    public function eliminar(int $id, int $userId): bool
    {
        $salida = $this->buscarPorId($id, $userId);

        if (!$salida) {
            return false;
        }

        $sql  = "DELETE FROM gastos WHERE id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id, ':user_id' => $userId]);

        if ($stmt->rowCount() > 0) {
            $this->gestorFacturas->eliminar($salida['factura_url']);
            return true;
        }

        return false;
    }

    /**
     * Calcula el total de todas las salidas de un usuario.
     *
     * @param  int   $userId ID del usuario
     * @return float         Suma total de salidas
     */
    public function total(int $userId): float
    {
        $sql = "SELECT COALESCE(SUM(g.monto), 0) AS total
                FROM gastos g
                INNER JOIN tipo_registros tr ON tr.id = g.tipo_registro_id
                WHERE tr.codigo = :codigo AND g.user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':codigo' => $this->codigoTipo, ':user_id' => $userId]);

        return (float) $stmt->fetchColumn();
    }

    /**
     * Obtiene el ID del tipo de registro 'registro_salida'.
     */
    private function obtenerTipoId(): int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM tipo_registros WHERE codigo = :codigo LIMIT 1");
        $stmt->execute([':codigo' => $this->codigoTipo]);

        return (int) $stmt->fetchColumn();
    }
}
