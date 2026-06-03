<?php
// Wealth Creators — authenticatiehulpfuncties
// Dit bestand wordt geïnclude vanuit config.php (na db.php, zodat $conn beschikbaar is).
// Beheert sessies en biedt helpers voor inlogstatus en toegangsbeveiliging.

// Start de sessie eenmalig; voorkom een dubbele aanroep als dit bestand
// per ongeluk twee keer wordt geïnclude.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cache de ingelogde gebruiker per request zodat we niet bij elk gebruik
// opnieuw de database raadplegen.
$_auth_gebruiker_cache = null;

// Geeft true terug als er een gebruiker is ingelogd.
function is_ingelogd() {
    return isset($_SESSION['gebruiker_id']) && (int) $_SESSION['gebruiker_id'] > 0;
}

// Geeft de gegevens van de ingelogde gebruiker terug (id, naam, email)
// of null als de bezoeker niet is ingelogd.
function huidige_gebruiker() {
    global $conn, $_auth_gebruiker_cache;

    if (!is_ingelogd()) {
        return null;
    }

    // Gebruik de cache als die er al is.
    if ($_auth_gebruiker_cache !== null) {
        return $_auth_gebruiker_cache;
    }

    $id   = (int) $_SESSION['gebruiker_id'];
    $stmt = mysqli_prepare($conn, 'SELECT id, naam, email FROM gebruikers WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $_auth_gebruiker_cache = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Sessie ongeldig als het account niet meer bestaat.
    if (!$_auth_gebruiker_cache) {
        session_destroy();
    }

    return $_auth_gebruiker_cache;
}

// Stuurt een niet-ingelogde bezoeker door naar de loginpagina en stopt de uitvoering.
// Geef $retour mee om de bezoeker na het inloggen terug te sturen naar de originele pagina.
function vereis_login($retour = '') {
    if (!is_ingelogd()) {
        $url = 'inloggen.php';
        if ($retour !== '') {
            $url .= '?retour=' . urlencode($retour);
        }
        header('Location: ' . $url);
        exit;
    }
}
