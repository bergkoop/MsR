<?php
// Wealth Creators — "Mijn biedingen"
// Toont de biedingen van de ingelogde gebruiker. Vereist een account.

require_once __DIR__ . '/config.php';

// Alleen ingelogde gebruikers mogen hun biedingen zien.
vereis_login('berichten.php');

$ingelogde = huidige_gebruiker();
$biedingen = array();

// Haal alle biedingen op die gekoppeld zijn aan de ingelogde gebruiker.
$sql = 'SELECT b.bod_bedrag, b.bericht, b.aangemaakt_op, b.status,
               p.id AS product_id, p.naam AS product_naam
          FROM biedingen b
          JOIN producten p ON p.id = b.product_id
         WHERE b.gebruiker_id = ?
         ORDER BY b.aangemaakt_op DESC';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $ingelogde['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $biedingen[] = $row;
}
mysqli_stmt_close($stmt);

$page_title = 'Mijn biedingen — Wealth Creators';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container" style="max-width: 900px;">
        <div class="section__head">
            <span class="eyebrow">Mijn biedingen</span>
            <h2>Uw biedingen</h2>
            <p>Een overzicht van alle biedingen die u heeft geplaatst, <?php echo htmlspecialchars($ingelogde['naam']); ?>.</p>
        </div>

        <?php if (empty($biedingen)): ?>
            <div class="empty-state">
                <h3>Nog geen biedingen</h3>
                <p>U heeft nog geen biedingen geplaatst. Bekijk de collectie en plaats uw eerste bod.</p>
                <a href="overzicht.php" class="btn btn--ghost" style="margin-top: 20px;">Bekijk de collectie</a>
            </div>
        <?php else: ?>
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
