<?php
// Wealth Creators — registratiepagina
// Bezoekers maken hier een nieuw account aan. Na succesvol registreren
// worden ze automatisch ingelogd en doorgestuurd naar de homepage.

require_once __DIR__ . '/config.php';

// Al ingelogde gebruikers hoeven niet te registreren.
if (is_ingelogd()) {
    header('Location: index.php');
    exit;
}

$fouten   = array();
$waarden  = array('naam' => '', 'email' => '');

// Verwerk het formulier alleen bij een POST-aanvraag.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Lees en normaliseer de invoer.
    $naam       = isset($_POST['naam'])       ? trim($_POST['naam'])       : '';
    $email      = isset($_POST['email'])      ? trim($_POST['email'])      : '';
    $wachtwoord = isset($_POST['wachtwoord']) ? $_POST['wachtwoord']       : '';
    $herhaling  = isset($_POST['herhaling'])  ? $_POST['herhaling']        : '';

    // Bewaar waarden zodat ze bij fouten opnieuw getoond worden (nooit het wachtwoord).
    $waarden['naam']  = $naam;
    $waarden['email'] = $email;

    // Serverkant-validatie: naam.
    if (mb_strlen($naam) < 2 || mb_strlen($naam) > 100) {
        $fouten['naam'] = 'Vul een naam in van 2 tot 100 tekens.';
    }

    // Serverkant-validatie: e-mail.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
        $fouten['email'] = 'Vul een geldig e-mailadres in (maximaal 150 tekens).';
    }

    // Serverkant-validatie: wachtwoord.
    if (mb_strlen($wachtwoord) < 8) {
        $fouten['wachtwoord'] = 'Uw wachtwoord moet minimaal 8 tekens bevatten.';
    }

    // Serverkant-validatie: herhaling.
    if ($wachtwoord !== $herhaling) {
        $fouten['herhaling'] = 'De wachtwoorden komen niet overeen.';
    }

    // Als alles geldig is, controleer of het e-mailadres al bestaat.
    if (empty($fouten)) {
        $stmt = mysqli_prepare($conn, 'SELECT id FROM gebruikers WHERE email = ?');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $bestaat = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);

        if ($bestaat) {
            $fouten['email'] = 'Dit e-mailadres is al in gebruik. Probeer in te loggen.';
        }
    }

    // Sla het account op als er geen fouten zijn.
    if (empty($fouten)) {
        $hash = password_hash($wachtwoord, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn,
            'INSERT INTO gebruikers (naam, email, wachtwoord_hash) VALUES (?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sss', $naam, $email, $hash);
        $ok = mysqli_stmt_execute($stmt);
        $nieuw_id = mysqli_stmt_insert_id($stmt);
        mysqli_stmt_close($stmt);

        if ($ok) {
            // Log de gebruiker direct in na registratie.
            $_SESSION['gebruiker_id'] = $nieuw_id;
            header('Location: berichten.php');
            exit;
        }

        // Onverwachte databasefout.
        $fouten['_algemeen'] = 'Er is een fout opgetreden. Probeer het later opnieuw.';
    }
}

$page_title = 'Registreren — Wealth Creators';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container" style="max-width: 520px;">
        <div class="section__head">
            <span class="eyebrow">Nieuw account</span>
            <h2>Registreren</h2>
            <p>Maak een account aan om biedingen te plaatsen en te beheren.</p>
        </div>

        <?php if (isset($fouten['_algemeen'])): ?>
            <div class="alert alert--error">
                <?php echo htmlspecialchars($fouten['_algemeen']); ?>
            </div>
        <?php endif; ?>

        <form class="form" method="post" action="registreren.php" id="registerForm" novalidate>

            <div class="form__group">
                <label for="naam">Naam</label>
                <input type="text" id="naam" name="naam" maxlength="100"
                       value="<?php echo htmlspecialchars($waarden['naam']); ?>"
                       class="<?php echo isset($fouten['naam']) ? 'is-invalid' : ''; ?>"
                       required>
                <div class="field-error"><?php echo isset($fouten['naam']) ? htmlspecialchars($fouten['naam']) : ''; ?></div>
            </div>

            <div class="form__group">
                <label for="email">E-mailadres</label>
                <input type="email" id="email" name="email" maxlength="150"
                       value="<?php echo htmlspecialchars($waarden['email']); ?>"
                       class="<?php echo isset($fouten['email']) ? 'is-invalid' : ''; ?>"
                       required>
                <div class="field-error"><?php echo isset($fouten['email']) ? htmlspecialchars($fouten['email']) : ''; ?></div>
            </div>

            <div class="form__group">
                <label for="wachtwoord">Wachtwoord</label>
                <input type="password" id="wachtwoord" name="wachtwoord" minlength="8"
                       class="<?php echo isset($fouten['wachtwoord']) ? 'is-invalid' : ''; ?>"
                       required>
                <div class="field-error"><?php echo isset($fouten['wachtwoord']) ? htmlspecialchars($fouten['wachtwoord']) : ''; ?></div>
            </div>

            <div class="form__group">
                <label for="herhaling">Wachtwoord herhalen</label>
                <input type="password" id="herhaling" name="herhaling"
                       class="<?php echo isset($fouten['herhaling']) ? 'is-invalid' : ''; ?>"
                       required>
                <div class="field-error"><?php echo isset($fouten['herhaling']) ? htmlspecialchars($fouten['herhaling']) : ''; ?></div>
            </div>

            <button type="submit" class="btn btn--primary" style="width: 100%;">Account aanmaken</button>

            <p style="text-align: center; margin-top: 20px; color: var(--text-muted);">
                Heeft u al een account?
                <a href="inloggen.php" style="color: var(--accent-soft);">Inloggen</a>
            </p>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
