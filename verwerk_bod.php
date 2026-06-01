<?php
// Wealth Creators — verwerkt een ingediend bod (geen eigen weergave)
// Deze pagina accepteert alleen POST, valideert alles opnieuw aan de serverkant
// (nooit vertrouwen op de JavaScript-validatie alleen) en slaat het bod op.
// Daarna volgt een redirect terug naar de showcase (Post/Redirect/Get).

require_once __DIR__ . '/config.php';

// Stuurt de bezoeker terug naar de showcase met een statusmelding en gaat weg.
// Naam en e-mail worden meegegeven zodat het formulier ze opnieuw kan tonen.
function redirect_terug($product_id, $status, $naam = '', $email = '') {
    $url = 'showcase.php?id=' . (int) $product_id . '&status=' . urlencode($status);
    if ($naam !== '') {
        $url .= '&naam=' . urlencode($naam);
    }
    if ($email !== '') {
        $url .= '&email=' . urlencode($email);
    }
    $url .= '#bod-form';
    header('Location: ' . $url);
    exit;
}

// Alleen POST-aanvragen zijn toegestaan.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Lees en normaliseer de invoer.
$product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$naam       = isset($_POST['naam']) ? trim($_POST['naam']) : '';
$email      = isset($_POST['email']) ? trim($_POST['email']) : '';
$bericht    = isset($_POST['bericht']) ? trim($_POST['bericht']) : '';
// Komma's toestaan als decimaalteken voor het bedrag.
$bod_ruw    = isset($_POST['bod_bedrag']) ? str_replace(',', '.', trim($_POST['bod_bedrag'])) : '';
$bod_bedrag = is_numeric($bod_ruw) ? (float) $bod_ruw : null;

// Zonder geldig product kunnen we nergens naartoe terugkeren.
if ($product_id < 1) {
    header('Location: index.php');
    exit;
}

// Serverkant-validatie van alle velden (zelfde regels als in main.js).
$fouten = array();

if (mb_strlen($naam) < 2 || mb_strlen($naam) > 100) {
    $fouten[] = 'naam';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
    $fouten[] = 'email';
}
if (mb_strlen($bericht) < 10) {
    $fouten[] = 'bericht';
}
if ($bod_bedrag === null || $bod_bedrag <= 0) {
    $fouten[] = 'bod_bedrag';
}

// Bij ontbrekende of ongeldige velden: terug met een melding.
if (!empty($fouten)) {
    redirect_terug($product_id, 'ongeldig', $naam, $email);
}

// Haal het minimale bod én controleer of het product bestaat.
$minimaal_bod = null;
$stmt = mysqli_prepare($conn, 'SELECT minimaal_bod FROM producten WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rij = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$rij) {
    // Het product bestaat niet (meer).
    header('Location: index.php');
    exit;
}
$minimaal_bod = (float) $rij['minimaal_bod'];

// Kernregel: het bod moet altijd >= het minimale bod zijn.
if ($bod_bedrag < $minimaal_bod) {
    redirect_terug($product_id, 'te-laag', $naam, $email);
}

// Sla het bod op via een prepared statement.
$stmt = mysqli_prepare($conn,
    'INSERT INTO biedingen (product_id, naam, email, bod_bedrag, bericht)
     VALUES (?, ?, ?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'issds', $product_id, $naam, $email, $bod_bedrag, $bericht);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Terug naar de showcase met de juiste melding.
if ($ok) {
    redirect_terug($product_id, 'succes');
} else {
    redirect_terug($product_id, 'ongeldig', $naam, $email);
}
