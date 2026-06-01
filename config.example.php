<?php
// Wealth Creators — voorbeeld van de databaseconfiguratie
// Kopieer dit bestand naar config.php en vul de echte gegevens in.
// config.php zelf staat in .gitignore en wordt niet meegecommit.

define('DB_HOST', 'localhost');
define('DB_USER', 'jouw_gebruikersnaam');
define('DB_PASS', 'jouw_wachtwoord');
define('DB_NAME', 'wealth_creators');

// Maak de databaseverbinding aan.
require_once __DIR__ . '/includes/db.php';
