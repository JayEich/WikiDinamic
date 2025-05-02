<?php
namespace App\Models;

use Core\Database;
use PDO;
use Ramsey\Uuid\Uuid;

class Client {

    // Read Solo obtener clientes activos
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM clients WHERE is_deleted = 0 ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Read Solo encontrar cliente activo por UUID
    public static function findByUuid(string $uuid) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM clients WHERE uuid = :uuid AND is_deleted = 0");
        $stmt->bindParam(':uuid', $uuid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear cliente (UUID v4)
    public static function create(array $data): string|false {
         $db = Database::getConnection();
        $uuid = Uuid::uuid4()->toString(); // Generar UUID v4 en PHP

        $sql = "INSERT INTO clients (uuid, name, logo_path, color_primary, color_secondary, color_tertiary)
                VALUES (:uuid, :name, :logo_path, :color_primary, :color_secondary, :color_tertiary)";
        $stmt = $db->prepare($sql);

        try {
            $stmt->execute([
                ':uuid' => $uuid,
                ':name' => $data['name'],
                ':logo_path' => $data['logo_path'] ?? null,
                ':color_primary' => $data['color_primary'] ?? '#030339',
                ':color_secondary' => $data['color_secondary'] ?? '#ffffff',
                ':color_tertiary' => $data['color_tertiary'] ?? '#555555',
            ]);
            return $uuid;
        } catch (\PDOException $e) {
            // Loggear error
            \App\Helpers\Security::logError("Error: " . $e->getMessage());
            return false;
        }
    }

    public static function update(string $uuid, array $data): bool {
         $db = Database::getConnection();
         $sql = "UPDATE clients SET
                    name = :name,
                    logo_path = :logo_path,
                    color_primary = :color_primary,
                    color_secondary = :color_secondary,
                    color_tertiary = :color_tertiary
                 WHERE uuid = :uuid AND is_deleted = 0";
        $stmt = $db->prepare($sql);
       
        try {
            return $stmt->execute([
                ':name' => $data['name'],
                ':logo_path' => $data['logo_path'] ?? null,
                ':color_primary' => $data['color_primary'] ?? '#030339',
                ':color_secondary' => $data['color_secondary'] ?? '#ffffff',
                ':color_tertiary' => $data['color_tertiary'] ?? '#555555',
                ':uuid' => $uuid
            ]);
        } catch (\PDOException $e) {
            // Loggear error
            \App\Helpers\Security::logError("Error: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar (Soft Delete): Cambiar DELETE por UPDATE
    public static function delete(string $uuid): bool {
         $db = Database::getConnection();
         $sql = "UPDATE clients SET is_deleted = 1 WHERE uuid = :uuid";
         $stmt = $db->prepare($sql);
         try {
            return $stmt->execute([':uuid' => $uuid]);
         } catch (\PDOException $e) {
             // Loggear error
             \App\Helpers\Security::logError("Error: " . $e->getMessage());
             return false;
         }
    }

    // Método para restaurar un cliente
    public static function restore(string $uuid): bool {
         $db = Database::getConnection();
         $sql = "UPDATE clients SET is_deleted = 0 WHERE uuid = :uuid";
         $stmt = $db->prepare($sql);
         try {
            return $stmt->execute([':uuid' => $uuid]);
         } catch (\PDOException $e) {
             // Loggear error
             \App\Helpers\Security::logError("Error: " . $e->getMessage());
             return false;
         }
    }

    // --- Métodos de relaciones ajustados ---
    public static function getWikis(string $client_uuid) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM wikis WHERE client_uuid = :client_uuid AND is_deleted = 0 ORDER BY title ASC");
        $stmt->bindParam(':client_uuid', $client_uuid);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUsers(string $client_uuid) {
         $db = Database::getConnection();
         $stmt = $db->prepare("SELECT uuid, username, email, role, profile_image FROM users WHERE client_uuid = :client_uuid AND is_deleted = 0 ORDER BY username ASC");
         $stmt->bindParam(':client_uuid', $client_uuid);
         $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}