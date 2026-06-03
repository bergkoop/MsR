-- Wealth Creators — databaseschema
-- Importeer dit bestand via phpMyAdmin (de schoolserver heeft geen CLI).
-- Databasenaam: wealth_creators

-- Maak de database aan indien die nog niet bestaat en selecteer hem.
CREATE DATABASE IF NOT EXISTS `wealth_creators`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `wealth_creators`;

-- Verwijder bestaande tabellen in de juiste volgorde (FK's eerst).
DROP TABLE IF EXISTS `biedingen`;
DROP TABLE IF EXISTS `product_afbeeldingen`;
DROP TABLE IF EXISTS `producten`;
DROP TABLE IF EXISTS `categorieen`;
DROP TABLE IF EXISTS `gebruikers`;

-- Tabel: gebruikers
-- Geregistreerde accounts. Wachtwoorden worden opgeslagen als password_hash() — nooit plain text.
CREATE TABLE `gebruikers` (
    `id`              INT          NOT NULL AUTO_INCREMENT,
    `naam`            VARCHAR(100) NOT NULL,
    `email`           VARCHAR(150) NOT NULL,
    `wachtwoord_hash` VARCHAR(255) NOT NULL,
    `aangemaakt_op`   DATETIME     NOT NULL DEFAULT NOW(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel: categorieen
-- Productcategorieën zoals supercars, jachten, helikopters, horloges, juwelen.
CREATE TABLE `categorieen` (
    `id`             INT          NOT NULL AUTO_INCREMENT,
    `naam`           VARCHAR(100) NOT NULL,
    `beschrijving`   TEXT,
    `afbeelding_url` VARCHAR(500),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel: producten
-- De exclusieve items die te koop staan.
CREATE TABLE `producten` (
    `id`            INT           NOT NULL AUTO_INCREMENT,
    `categorie_id`  INT           NOT NULL,
    `naam`          VARCHAR(200)  NOT NULL,
    `beschrijving`  TEXT          NOT NULL,
    `prijs`         DECIMAL(15,2) NOT NULL,
    `minimaal_bod`  DECIMAL(15,2) NOT NULL,
    `locatie`       VARCHAR(150),
    `status`        ENUM('beschikbaar','gereserveerd','verkocht') NOT NULL DEFAULT 'beschikbaar',
    `eigenaar_naam` VARCHAR(100)  NOT NULL,
    `aangemaakt_op` DATETIME      NOT NULL DEFAULT NOW(),
    PRIMARY KEY (`id`),
    KEY `idx_categorie_id` (`categorie_id`),
    CONSTRAINT `fk_product_categorie`
        FOREIGN KEY (`categorie_id`) REFERENCES `categorieen` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel: product_afbeeldingen
-- Eén product kan meerdere afbeeldingen hebben; één daarvan is de hoofdafbeelding.
CREATE TABLE `product_afbeeldingen` (
    `id`                 INT          NOT NULL AUTO_INCREMENT,
    `product_id`         INT          NOT NULL,
    `afbeelding_url`     VARCHAR(500) NOT NULL,
    `is_hoofdafbeelding` TINYINT(1)   NOT NULL DEFAULT 0,
    `volgorde`           INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_product_id` (`product_id`),
    CONSTRAINT `fk_afbeelding_product`
        FOREIGN KEY (`product_id`) REFERENCES `producten` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel: biedingen
-- Biedingen die bezoekers via het formulier indienen.
-- Regel: bod_bedrag moet altijd >= minimaal_bod van het bijbehorende product zijn.
CREATE TABLE `biedingen` (
    `id`            INT           NOT NULL AUTO_INCREMENT,
    `product_id`    INT           NOT NULL,
    `gebruiker_id`  INT           NULL,
    `naam`          VARCHAR(100)  NOT NULL,
    `email`         VARCHAR(150)  NOT NULL,
    `bod_bedrag`    DECIMAL(15,2) NOT NULL,
    `bericht`       TEXT          NOT NULL,
    `aangemaakt_op` DATETIME      NOT NULL DEFAULT NOW(),
    `status`        ENUM('nieuw','gelezen','beantwoord') NOT NULL DEFAULT 'nieuw',
    PRIMARY KEY (`id`),
    KEY `idx_bieding_product_id` (`product_id`),
    KEY `idx_bieding_gebruiker_id` (`gebruiker_id`),
    KEY `idx_bieding_email` (`email`),
    CONSTRAINT `fk_bieding_product`
        FOREIGN KEY (`product_id`) REFERENCES `producten` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_bieding_gebruiker`
        FOREIGN KEY (`gebruiker_id`) REFERENCES `gebruikers` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
