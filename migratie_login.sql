-- Wealth Creators — migratie: gebruikersaccounts
-- Voer dit eenmalig uit via phpMyAdmin op een bestaande wealth_creators database.
-- Voor een schone installatie: gebruik schema.sql + seed.sql (reeds bijgewerkt).

USE `wealth_creators`;

-- Maak de gebruikerstabel aan (vóór biedingen, want biedingen krijgt een FK naar hier).
CREATE TABLE IF NOT EXISTS `gebruikers` (
    `id`              INT          NOT NULL AUTO_INCREMENT,
    `naam`            VARCHAR(100) NOT NULL,
    `email`           VARCHAR(150) NOT NULL,
    `wachtwoord_hash` VARCHAR(255) NOT NULL,
    `aangemaakt_op`   DATETIME     NOT NULL DEFAULT NOW(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Voeg gebruiker_id toe aan biedingen (nullable zodat oude biedingen intact blijven).
ALTER TABLE `biedingen`
    ADD COLUMN `gebruiker_id` INT NULL AFTER `product_id`,
    ADD KEY `idx_bieding_gebruiker_id` (`gebruiker_id`),
    ADD CONSTRAINT `fk_bieding_gebruiker`
        FOREIGN KEY (`gebruiker_id`) REFERENCES `gebruikers` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE;
