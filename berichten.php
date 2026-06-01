<?php
// Wealth Creators — "Mijn biedingen"
// Bezoekers zoeken hun eigen biedingen op via hun e-mailadres (geen login nodig).

require_once __DIR__ . '/config.php';

$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$gezocht = ($email !== '');
$geldig_email = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
$biedingen = array();

// Alleen zoeken wanneer er een geldig e-mailadres is opgegeven.
if ($gezocht && $geldig_email) {
    $sql = 'SELECT b.bod_bedrag, b.bericht, b.aangemaakt_op, b.status,
                   p.id AS product_id, p.naam AS product_naam
              FROM biedingen b
              JOIN producten p ON p.id = b.product_id
             WHERE b.email = ?
             ORDER BY b.aangemaakt_op DESC';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $biedingen[] = $row;
    }
    mysqli_stmt_close($stmt);
}

$page_title = 'Mijn biedingen — Wealth Creators';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container" style="max-width: 900px;">
        <div class="section__head">
            <span class="eyebrow">Mijn biedingen</span>
            <h2>Bekijk uw biedingen</h2>
            <p>Vul het e-mailadres in waarmee u uw bod(en) heeft geplaatst.</p>
        </div>

        <!-- Zoekformulier: stuurt het e-mailadres via GET naar deze pagina -->
        <form class="form" method="get" action="berichten.php" style="max-width: 520px; margin: 0 auto 40px;">
            <div class="form__group">
                <label for="email">E-mailadres</label>
                <input type="email" id="email" name="email" maxlength="150"
                       value="<?php echo htmlspecialchars($email); ?>"
                       placeholder="naam@voorbeeld.nl" required>
            </div>
            <button type="submit" class="btn btn--primary">Zoek mijn biedingen</button>
        </form>

        <?php if ($gezocht && !$geldig_email): ?>
            <div class="alert alert--error">
                Vul een geldig e-mailadres in om uw biedingen te bekijken.
            </div>
        <?php elseif ($gezocht && empty($biedingen)): ?>
            <div class="empty-state">
                <h3>Geen biedingen gevonden</h3>
                <p>Er zijn geen biedingen gevonden voor <strong><?php echo htmlspecialchars($email); ?></strong>.</p>
            </div>
        <?php elseif (!empty($biedingen)): ?>
            <table class="bids-table">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Product</th>
                        <th>Uw bod</th>
                        <th>Bericht</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($biedingen as $bod): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($bod['aangemaakt_op']))); ?></td>
                            <td>
                                <a href="showcase.php?id=<?php echo (int) $bod['product_id']; ?>"
                                   style="color: var(--accent-soft);">
                                    <?php echo htmlspecialchars($bod['product_naam']); ?>
                                </a>
                            </td>
                            <td>&euro; <?php echo number_format((float) $bod['bod_bedrag'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($bod['bericht']); ?></td>
                            <td>
                                <?php
                                    // Koppel de bied-status aan een badge-kleur:
                                    // nieuw = groen, gelezen = amber, beantwoord = rood.
                                    $badge_kleur = array(
                                        'nieuw'      => 'beschikbaar',
                                        'gelezen'    => 'gereserveerd',
                                        'beantwoord' => 'verkocht',
                                    );
                                    $kleur = isset($badge_kleur[$bod['status']]) ? $badge_kleur[$bod['status']] : 'beschikbaar';
                                ?>
                                <span class="badge badge--<?php echo $kleur; ?>">
                                    <?php echo htmlspecialchars($bod['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
