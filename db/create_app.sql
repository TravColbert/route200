-- sqlite3 sample app.db
DROP TABLE IF EXISTS `users_domains`;
DROP TABLE IF EXISTS `domains`;
DROP TABLE IF EXISTS `users_ids`;
-- allows users and objects to be siloed into domains of interest
CREATE TABLE `domains` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `description` VARCHAR(255),
  `appid` VARCHAR(255),
  `createdAt` DATETIME NOT NULL,
  `updatedAt` DATETIME NOT NULL,
  `ownerId` INTEGER REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

INSERT INTO domains VALUES (null, "default", "The domain that all new users are put in by default", null, datetime('now'), datetime('now'), 1);

-- connects users to domains
CREATE TABLE `users_domains` (
  `userId` INTEGER REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  `domainId` INTEGER REFERENCES `domains` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  `createdAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (`userId`)
);

-- created a unique short ID for users created through registration page
CREATE TABLE `users_appids` (
  `userId` INTEGER REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  `appid` VARCHAR(6) NOT NULL,
  `createdAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (`userId`) 
);

-- CREATE TABLE `categories` (
--   `id` INTEGER PRIMARY KEY AUTOINCREMENT,
--   `name` VARCHAR(255),
--   `description` TEXT,
--   `createdAt` DATETIME NOT NULL,
--   `updatedAt` DATETIME NOT NULL,
--   `domainId` INTEGER REFERENCES `domains` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
-- );
