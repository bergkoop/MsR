<?php
// Wealth Creators — databaseverbinding (mysqli, procedureel)
// Dit bestand wordt geïnclude via config.php, waar de DB_* constanten gedefinieerd staan.

// Toon geen mysqli-fouten rechtstreeks aan de bezoeker; we handelen ze zelf af.
mysqli_report(MYSQLI_REPORT_OFF);

// Maak de verbinding met de database.
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Stop netjes als de verbinding mislukt.
if (!$conn) {
    http_response_code(500);
    die('Er kon geen verbinding met de database worden gemaakt. Probeer het later opnieuw.');
}

// Gebruik utf8mb4 zodat speciale tekens en symbolen correct worden opgeslagen.
mysqli_set_charset($conn, 'utf8mb4');
