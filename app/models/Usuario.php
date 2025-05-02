<?php
namespace App\Models;

use Core\Database;
use PDO;
use Ramsey\Uuid\Uuid;

class Usuario {

    public static function autenticar($username, $password_o_token_da) {
        //AQUI VA LA AUTENTICACION CON EL DA
        $autenticado_en_da = true;

        if ($autenticado_en_da) {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT uuid, client_uuid, username, email, role, profile_image FROM users WHERE username = :username AND is_deleted = 0");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                return $user;
            } else {
                $newUserUuid = self::create([
                    'username' => $username,
                    'email' => 'email_del_da@example.com',
                    'role' => 'user', 
                    'client_uuid' => null 
                ]);
                if ($newUserUuid) {
                    return self::findByUuid($newUserUuid);
                }
                return false;
            }
        }

        return false; 
    }

    public static function findByUuid(string $uuid) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT uuid, client_uuid, username, email, role, profile_image, created_at, updated_at FROM users WHERE uuid = :uuid AND is_deleted = 0");
        $stmt->bindParam(':uuid', $uuid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByUsername(string $username) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT uuid, client_uuid, username, email, role, profile_image FROM users WHERE username = :username AND is_deleted = 0");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear usuario (Tomando los datos del DA si existen)
    public static function create(array $data): string|false {
        $db = Database::getConnection();
        $uuid = Uuid::uuid4()->toString();

        $sql = "INSERT INTO users (uuid, client_uuid, username, email, role, profile_image)
                VALUES (:uuid, :client_uuid, :username, :email, :role, :profile_image)";
        $stmt = $db->prepare($sql);
        try {
            $stmt->execute([
                ':uuid' => $uuid,
                ':client_uuid' => $data['client_uuid'] ?? null,
                ':username' => $data['username'],
                ':email' => $data['email'] ?? null,
                ':role' => $data['role'] ?? 'user',
                ':profile_image' => $data['profile_image'] ?? null
            ]);
            return $uuid;
        } catch (\PDOException $e) {
            // Loggear error
            \App\Helpers\Security::logError("Error creando User del DA: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar perfil 
    public static function updateProfile(string $uuid, array $data): bool {
        $db = Database::getConnection();
        
        $sql = "UPDATE users SET
                   profile_image = :profile_image,
                   role = :role
                WHERE uuid = :uuid";
        $stmt = $db->prepare($sql);
        try {
            $params = [
                ':profile_image' => $data['profile_image'] ?? null,
                ':role' => $data['role'],
                ':uuid' => $uuid
            ];
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            // Loggear error
            \App\Helpers\Security::logError("Error Editando cliente: " . $e->getMessage());
            return false;
        }
    }

    // BORRAR tipo soft(con update)
    public static function delete(string $uuid): bool {
        $db = Database::getConnection();
        $sql = "UPDATE users SET is_deleted = 1 WHERE uuid = :uuid";
        $stmt = $db->prepare($sql);
        try {
           return $stmt->execute([':uuid' => $uuid]);
        } catch (\PDOException $e) {
            // Loggear error
            \App\Helpers\Security::logError("Error Eliminando Usuario: " . $e->getMessage());
            return false;
        }
   }

    // Restaurar usuario
    public static function restore(string $uuid): bool {
        $db = Database::getConnection();
        $sql = "UPDATE users SET is_deleted = 0 WHERE uuid = :uuid";
        $stmt = $db->prepare($sql);
        try {
           return $stmt->execute([':uuid' => $uuid]);
        } catch (\PDOException $e) {
            // Loggear error
            \App\Helpers\Security::logError("Error Restaurante Usuario eliminado: " . $e->getMessage());
            return false;
        }
    }

     // Asociar un usuario a un cliente
    public static function assignClient(string $user_uuid, ?string $client_uuid): bool {
        $db = Database::getConnection();
        $sql = "UPDATE users SET client_uuid = :client_uuid WHERE uuid = :uuid";
        $stmt = $db->prepare($sql);
        try {
            return $stmt->execute([':client_uuid' => $client_uuid, ':uuid' => $user_uuid]);
        } catch (\PDOException $e) {
             // Loggear error
             \App\Helpers\Security::logError("Error asociando user a cliente: " . $e->getMessage());
            return false;
        }
    }

     // Cambiar el rol de un usuario
     public static function changeRole(string $user_uuid, string $role): bool {
        if (!in_array($role, ['admin', 'user', 'superadmin'])) return false;

        $db = Database::getConnection();
        $sql = "UPDATE users SET role = :role WHERE uuid = :uuid";
        $stmt = $db->prepare($sql);
        try {
            return $stmt->execute([':role' => $role, ':uuid' => $user_uuid]);
        } catch (\PDOException $e) {
             // Loggear error
             \App\Helpers\Security::logError("Error Cambiando rol del cliente: " . $e->getMessage());
            return false;
        }
    }
}