<?php
// Wealth Creators — inlogpagina
// Bestaande gebruikers loggen hier in met e-mail en wachtwoord.
// Na succes worden ze doorgestuurd naar de originele pagina of naar de homepage.

require_once __DIR__ . '/config.php';

// Al ingelogde gebruikers hoeven niet opnieuw in te loggen.
if (is_ingelogd()) {
    header('Location: index.php');
    exit;
}

// Lees de retour-URL uit de query string (alleen relatieve paden toestaan).
$retour = isset($_GET['retour']) ? trim($_GET['retour']) : '';

// Alleen doorstuuren naar een relatieve URL (geen http:// of //) om open-redirect te voorkomen.
if ($retour !== '' && (strpos($retour, '//') !== false || strpos($retour, ':') !== false)) {
    $retour = '';
}

$fout    = '';
$waarden = array('email' => '');

// Verwerk het formulier alleen bij een POST-aanvraag.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email      = isset($_POST['email'])      ? trim($_POST['email'])  : '';
    $wachtwoord = isset($_POST['wachtwoord']) ? $_POST['wachtwoord']   : '';

    $waarden['email'] = $email;

    // Zoek de gebruiker op via zijn e-mailadres.
    $gebruiker = null;
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = mysqli_prepare($conn,
            'SELECT id, naam, email, wachtwoord_hash FROM gebruikers WHERE email = ?');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result    = mysqli_stmt_get_result($stmt);
        $gebruiker = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }

    // Controleer het wachtwoord. Gebruik een generieke foutmelding om te voorkomen
    // dat een aanvaller kan achterhalen of het e-mailadres bekend is.
    if ($gebruiker && password_verify($wachtwoord, $gebruiker['wachtwoord_hash'])) {
        $_SESSION['gebruiker_id'] = $gebruiker['id'];

        // Stuur door naar de retour-URL of de homepage.
        $bestemming = ($retour !== '') ? $retour : 'index.php';
        header('Location: ' . $bestemming);
        exit;
    }

    $fout = 'Onjuiste e-mail en/of wachtwoord. Controleer uw gegevens en probeer het opnieuw.';
}

$page_title = 'Inloggen — Wealth Creators';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container" style="max-width: 520px;">
        <div class="section__head">
            <span class="eyebrow">Welkom terug</span>
            <h2>Inloggen</h2>
            <p>Log in om uw biedingen te bekijken en nieuwe biedingen te plaatsen.</p>
        </div>

        <?php if ($fout !== ''): ?>
            <div class="alert alert--error">
                <?php echo htmlspecialchars($fout); ?>
            </div>
        <?php endif; ?>

        <form class="form" method="post"
              action="inloggen.php<?php echo $retour !== '' ? '?retour=' . urlencode($retour) : ''; ?>"
              id="loginForm" novalidate>

            <div class="form__group">
                <label for="email">E-mailadres</label>
                <input type="email" id="email" name="email" maxlength="150"
                       value="<?php echo htmlspecialchars($waarden['email']); ?>"
                       required>
                <div class="field-error" data-error-for="email"></div>
            </div>

            <div class="form__group">
                <label for="wachtwoord">Wachtwoord</label>
                <input type="password" id="wachtwoord" name="wachtwoord" required>
                <div class="field-error" data-error-for="wachtwoord"></div>
            </div>

            <button type="submit" class="btn btn--primary" style="width: 100%;">Inloggen</button>

            <p style="text-align: center; margin-top: 20px; color: var(--text-muted);">
                Nog geen account?
                <a href="registreren.php" style="color: var(--accent-soft);">Registreren</a>
            </p>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
