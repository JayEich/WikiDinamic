<?php
namespace App\Models;

use Core\Database;
use PDO;
use Ramsey\Uuid\Uuid;

class Wiki {

    // Obtener todas las wikis con nombre de cliente
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT w.*, c.name as client_name
                           FROM wikis w
                           JOIN clients c ON w.client_uuid = c.uuid
                           WHERE w.is_deleted = 0 AND c.is_deleted = 0
                           ORDER BY w.title ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener wikis de un cliente específico
    public static function findByClientUuid(string $client_uuid) {
        $db = Database::getConnection();
         // Verificar si el cliente está activo primero podría ser buena idea
        $stmt = $db->prepare("SELECT * FROM wikis WHERE client_uuid = :client_uuid AND is_deleted = 0 ORDER BY title ASC");
        $stmt->bindParam(':client_uuid', $client_uuid);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Encontrar una wiki por su UUID (con datos del cliente)
     public static function findByUuid(string $uuid) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT w.*, c.name as client_name, c.logo_path, c.color_primary, c.color_secondary, c.color_tertiary
            FROM wikis w
            JOIN clients c ON w.client_uuid = c.uuid
            WHERE w.uuid = :uuid AND w.is_deleted = 0 AND c.is_deleted = 0");
        $stmt->bindParam(':uuid', $uuid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear una nueva wiki
    public static function create(array $data): string|false {
        $db = Database::getConnection();
        $uuid = Uuid::uuid4()->toString(); 

        if (empty($data['client_uuid'])) {
             // Loggear error:
             \App\Helpers\Security::logError("Error al crear la wiki: " . "client_uuid no puede estar vacío");
            return false;
        }
        // Verificar si el client_uuid existe en la tabla clients

        $sql = "INSERT INTO wikis (uuid, client_uuid, title, description, image_card, image_wiki)
                VALUES (:uuid, :client_uuid, :title, :description, :image_card, :image_wiki)";
        $stmt = $db->prepare($sql);
        try {
            $stmt->execute([
                ':uuid' => $uuid,
                ':client_uuid' => $data['client_uuid'],
                ':title' => $data['title'],
                ':description' => $data['description'] ?? null,
                ':image_card' => $data['image_card'] ?? null,
                ':image_wiki' => $data['image_wiki'] ?? null
            ]);
            return $uuid;
        } catch (\PDOException $e) {
            // Loggear error
            \App\Helpers\Security::logError("Error creando Wiki " . $e->getMessage());
            return false;
        }
    }

     // Actualizar una wiki
    public static function update(string $uuid, array $data): bool {
        $db = Database::getConnection();
        $sql = "UPDATE wikis SET
                   title = :title,
                   description = :description,
                   image_card = :image_card,
                   image_wiki = :image_wiki
                WHERE uuid = :uuid AND is_deleted = 0";
         $stmt = $db->prepare($sql);
        try {
            return $stmt->execute([
                ':title' => $data['title'],
                ':description' => $data['description'] ?? null,
                ':image_card' => $data['image_card'] ?? null,
                ':image_wiki' => $data['image_wiki'] ?? null,
                ':uuid' => $uuid
            ]);
        } catch (\PDOException $e) {
             // Loggear error
             \App\Helpers\Security::logError("Error Actualizando Wiki " . $e->getMessage());
            return false;
        }
    }

    // Eliminar una wiki
     public static function delete(string $uuid): bool {
        $db = Database::getConnection();
        $sql = "UPDATE wikis SET is_deleted = 1 WHERE uuid = :uuid";
        $stmt = $db->prepare($sql);
        try {
            return $stmt->execute([':uuid' => $uuid]);
        } catch (\PDOException $e) {
            // Loggear error
            \App\Helpers\Security::logError("Error Eliminando Wiki: " . $e->getMessage());
            return false;
        }
    }

    // Restaurar wiki
    public static function restore(string $uuid): bool {
        $db = Database::getConnection();
        $sql = "UPDATE wikis SET is_deleted = 0 WHERE uuid = :uuid";
        $stmt = $db->prepare($sql);
        try {
            return $stmt->execute([':uuid' => $uuid]);
        } catch (\PDOException $e) {
            // Loggear error
            \App\Helpers\Security::logError("Error restaurando Wiki: " . $e->getMessage());
            return false;
        }
    }

    // --- Métodos para Articles/Subarticles ---
    // Necesitaríamos modelos App\Models\Article y App\Models\Subarticle
    // con sus propios métodos CRUD y de búsqueda.
    // Ejemplo:
    // public static function getArticles(string $wiki_uuid) {
    //     return Article::findByWikiUuid($wiki_uuid);
    // }
}