-- Script para crear la base de datos y las tablas necesarias para la aplicación WikiConcept
CREATE DATABASE wikiconceptmvc CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE wikiconceptmvc;

-- 1. Tabla 'clients'
CREATE TABLE `clients` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL DEFAULT (uuid()),
  `name` VARCHAR(255) NOT NULL COMMENT 'Nombre de la empresa/cliente',
  `logo_path` VARCHAR(255) DEFAULT NULL COMMENT 'Ruta al logo del cliente',
  `color_primary` VARCHAR(7) NOT NULL DEFAULT '#030339',
  `color_secondary` VARCHAR(7) NOT NULL DEFAULT '#ffffff',
  `color_tertiary` VARCHAR(7) NOT NULL DEFAULT '#555555',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabla para clientes/empresas';

-- 2. Tabla 'users' 
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL DEFAULT (uuid()),
  `client_uuid` CHAR(36) DEFAULT NULL COMMENT 'FK a clients.uuid - NULL para usuarios globales',
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL COMMENT '¡Guardar SIEMPRE usando password_hash()!',
  `role` ENUM('admin', 'user', 'superadmin') NOT NULL DEFAULT 'user',
  `profile_image` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_user_client_idx` (`client_uuid`), -- Índice para la FK
  CONSTRAINT `fk_user_client` FOREIGN KEY (`client_uuid`) REFERENCES `clients` (`uuid`) ON DELETE SET NULL ON UPDATE CASCADE -- ON UPDATE CASCADE es opcional pero útil
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabla de usuarios de la aplicación';

-- 3. Tabla 'wikis'
CREATE TABLE `wikis` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL DEFAULT (uuid()),
  `client_uuid` CHAR(36) NOT NULL COMMENT 'FK a clients.uuid - Una wiki siempre pertenece a un cliente',
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `image_card` VARCHAR(255) DEFAULT NULL COMMENT 'Imagen específica para la card de la wiki',
  `image_wiki` VARCHAR(255) DEFAULT NULL COMMENT 'Imagen de cabecera dentro de la wiki',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `fk_wiki_client_idx` (`client_uuid`), -- Índice para la FK
  CONSTRAINT `fk_wiki_client` FOREIGN KEY (`client_uuid`) REFERENCES `clients` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE -- Si se borra el cliente, se borran sus wikis
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabla principal de las wikis';

-- 4. Tabla 'articles'
CREATE TABLE `articles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL DEFAULT (uuid()),
  `wiki_uuid` CHAR(36) NOT NULL COMMENT 'FK a wikis.uuid',
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `image` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `fk_article_wiki_idx` (`wiki_uuid`), -- Índice para la FK
  CONSTRAINT `fk_article_wiki` FOREIGN KEY (`wiki_uuid`) REFERENCES `wikis` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE -- Si se borra la wiki, se borran sus artículos
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Artículos dentro de una wiki';

-- 5. Tabla 'subarticles' 
CREATE TABLE `subarticles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL DEFAULT (uuid()),
  `article_uuid` CHAR(36) NOT NULL COMMENT 'FK a articles.uuid',
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CU RRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `fk_subarticle_article_idx` (`article_uuid`), -- Índice para la FK
  CONSTRAINT `fk_subarticle_article` FOREIGN KEY (`article_uuid`) REFERENCES `articles` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE -- Si se borra el artículo, se borran sus subartículos
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Subdivisiones de un artículo';




-- VOLCADO DE DATA CON UUID MANUALES
-- Volcado de Datos de Prueba para 'wikiconcept' (Nueva Estructura)

-- ¡¡ADVERTENCIA!! Las contraseñas aquí están en TEXTO PLANO solo para pruebas
-- temporales debido a la futura integración con Directorio Activo.
-- ¡NO USAR EN PRODUCCIÓN SIN HASHING O DA!

-- Necesitamos generar algunos UUIDs manualmente para mantener las relaciones
SET @client1_uuid = uuid();
SET @client2_uuid = uuid();

SET @superadmin1_uuid = uuid();
SET @admin1_uuid = uuid();
SET @admin2_uuid = uuid();
SET @user1_uuid = uuid();
SET @user2_uuid = uuid();
SET @user_global_uuid = uuid();

SET @wiki1_uuid = uuid();
SET @wiki2_uuid = uuid();

SET @article1_uuid = uuid();
SET @article2_uuid = uuid();

-- 1. Insertar Clientes
INSERT INTO `clients` (`uuid`, `name`, `logo_path`, `color_primary`, `color_secondary`, `color_tertiary`) VALUES
(@client1_uuid, 'Empresa Alpha', 'localimages/alpha/logo.png', '#1E88E5', '#FFFFFF', '#EEEEEE'), -- Cliente 1 (Azul)
(@client2_uuid, 'Organización Beta', NULL, '#43A047', '#F5F5F5', '#212121');                 -- Cliente 2 (Verde)

-- 2. Insertar Usuarios
-- Superadmin para Cliente 1
INSERT INTO `users` (`uuid`, `client_uuid`, `username`, `email`, `password`, `role`) VALUES
(@superadmin1_uuid, @client1_uuid, 'superadmin_alpha', 'super@alpha.com', 'pass_super_alpha', 'superadmin');

-- Admins (uno para cada cliente)
INSERT INTO `users` (`uuid`, `client_uuid`, `username`, `email`, `password`, `role`) VALUES
(@admin1_uuid, @client1_uuid, 'admin_alpha', 'admin@alpha.com', 'pass_admin_alpha', 'admin'),
(@admin2_uuid, @client2_uuid, 'admin_beta', 'admin@beta.org', 'pass_admin_beta', 'admin');

-- Users (uno asociado a cliente 1, otro a cliente 2, uno global)
INSERT INTO `users` (`uuid`, `client_uuid`, `username`, `email`, `password`, `role`) VALUES
(@user1_uuid, @client1_uuid, 'user_alpha', 'user1@alpha.com', 'pass_user_alpha', 'user'),
(@user2_uuid, @client2_uuid, 'user_beta', 'user2@beta.org', 'pass_user_beta', 'user'),
(@user_global_uuid, NULL, 'user_global', 'user@global.net', 'pass_user_global', 'user');

-- 3. Insertar Wikis (una para cada cliente)
INSERT INTO `wikis` (`uuid`, `client_uuid`, `title`, `description`, `image_card`) VALUES
(@wiki1_uuid, @client1_uuid, 'Wiki Interna Alpha', 'Documentación y procesos de Empresa Alpha.', 'localimages/alpha/card.jpg'),
(@wiki2_uuid, @client2_uuid, 'Base de Conocimiento Beta', 'Guías y tutoriales para la Organización Beta.', NULL);

-- 4. Insertar Artículos (uno en cada wiki)
INSERT INTO `articles` (`uuid`, `wiki_uuid`, `title`, `content`) VALUES
(@article1_uuid, @wiki1_uuid, 'Guía de Inicio Rápido Alpha', 'Pasos iniciales para nuevos empleados...'),
(@article2_uuid, @wiki2_uuid, 'Cómo usar la Herramienta X (Beta)', 'Instrucciones detalladas sobre la herramienta X...');

-- 5. Insertar Subartículos (ejemplo para el artículo 1)
INSERT INTO `subarticles` (`article_uuid`, `title`, `content`) VALUES
(@article1_uuid, 'Paso 1: Configuración Inicial', 'Detalles sobre la configuración inicial...'),
(@article1_uuid, 'Paso 2: Acceso a Sistemas', 'Cómo solicitar acceso a los sistemas...');