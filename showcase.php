<?php
// Wealth Creators — productdetailpagina (showcase)
// Toont één product met galerij, specificaties en het biedformulier.
// Aanroep: showcase.php?id=X

require_once __DIR__ . '/config.php';

// Valideer het id uit de URL: het moet een positief geheel getal zijn.
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id < 1) {
    http_response_code(404);
    $page_title = 'Niet gevonden — Wealth Creators';
    require_once __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container empty-state">'
       . '<h3>Product niet gevonden</h3><p>Dit object bestaat niet (meer).</p>'
       . '<a class="btn btn--ghost" href="overzicht.php" style="margin-top:20px;">Terug naar overzicht</a>'
       . '</div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Haal het product met zijn categorie op via een prepared statement.
$product = null;
$sql = 'SELECT p.*, c.naam AS categorie_naam
          FROM producten p
          JOIN categorieen c ON c.id = p.categorie_id
         WHERE p.id = ?';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Bestaat het product niet, toon dan een nette foutmelding.
if (!$product) {
    http_response_code(404);
    $page_title = 'Niet gevonden — Wealth Creators';
    require_once __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container empty-state">'
       . '<h3>Product niet gevonden</h3><p>Dit object bestaat niet (meer).</p>'
       . '<a class="btn btn--ghost" href="overzicht.php" style="margin-top:20px;">Terug naar overzicht</a>'
       . '</div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Haal alle afbeeldingen van dit product op (hoofdafbeelding eerst).
$images = array();
$stmt = mysqli_prepare($conn,
    'SELECT afbeelding_url, is_hoofdafbeelding
       FROM product_afbeeldingen
      WHERE product_id = ?
      ORDER BY is_hoofdafbeelding DESC, volgorde ASC');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $images[] = $row;
}
mysqli_stmt_close($stmt);

// Val terug op een placeholder als er geen afbeeldingen zijn.
if (empty($images)) {
    $images[] = array('afbeelding_url' => 'https://picsum.photos/seed/wc' . $id . '/1200/800', 'is_hoofdafbeelding' => 1);
}

// Lees een eventuele melding van verwerk_bod.php (Post/Redirect/Get).
$flash = isset($_GET['status']) ? $_GET['status'] : '';

$page_title = htmlspecialchars($product['naam']) . ' — Wealth Creators';
require_once __DIR__ . '/includes/header.php';
?>

<section class="container">
    <div class="showcase">
        <!-- Galerij -->
        <div class="gallery">
            <div class="gallery__main">
                <img id="galleryMain"
                     src="<?php echo htmlspecialchars($images[0]['afbeelding_url']); ?>"
                     alt="<?php echo htmlspecialchars($product['naam']); ?>">
            </div>
            <?php if (count($images) > 1): ?>
                <div class="gallery__thumbs" id="galleryThumbs">
                    <?php foreach ($images as $index => $image): ?>
                        <img src="<?php echo htmlspecialchars($image['afbeelding_url']); ?>"
                             alt="<?php echo htmlspecialchars($product['naam']); ?> afbeelding <?php echo $index + 1; ?>"
                             class="<?php echo $index === 0 ? 'is-active' : ''; ?>">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Productinformatie -->
        <div class="showcase__info">
            <span class="eyebrow"><?php echo htmlspecialchars($product['categorie_naam']); ?></span>
            <h1><?php echo htmlspecialchars($product['naam']); ?></h1>

            <span class="badge badge--<?php echo htmlspecialchars($product['status']); ?>">
                <?php echo htmlspecialchars($product['status']); ?>
            </span>

            <div class="showcase__price">
                &euro; <?php echo number_format((float) $product['prijs'], 0, ',', '.'); ?>
                <small>Minimaal bod: &euro; <?php echo number_format((float) $product['minimaal_bod'], 0, ',', '.'); ?></small>
            </div>

            <p class="showcase__desc"><?php echo nl2br(htmlspecialchars($product['beschrijving'])); ?></p>

            <ul class="spec-list">
                <li><span>Categorie</span><span><?php echo htmlspecialchars($product['categorie_naam']); ?></span></li>
                <li><span>Locatie</span><span><?php echo htmlspecialchars($product['locatie']); ?></span></li>
                <li><span>Eigenaar</span><span><?php echo htmlspecialchars($product['eigenaar_naam']); ?></span></li>
                <li><span>Status</span><span><?php echo htmlspecialchars($product['status']); ?></span></li>
            </ul>

            <a href="#bod-form" class="btn btn--primary">Plaats een bod</a>
        </div>
    </div>
</section>

<!-- Biedformulier -->
<section class="section" id="bod-form" style="padding-top: 20px;">
    <div class="container" style="max-width: 720px;">
        <?php if ($flash === 'succes'): ?>
            <div class="alert alert--success">
                Bedankt! Uw bod is ontvangen. U vindt het terug op de pagina
                <a href="berichten.php" style="text-decoration: underline;">Mijn biedingen</a>.
            </div>
        <?php elseif ($flash === 'te-laag'): ?>
            <div class="alert alert--error">
                Uw bod is lager dan het minimale bod. Plaats een hoger bod.
            </div>
        <?php elseif ($flash === 'ongeldig'): ?>
            <div class="alert alert--error">
                Niet alle velden zijn correct ingevuld. Controleer uw gegevens en probeer het opnieuw.
            </div>
        <?php endif; ?>

        <form class="form" action="verwerk_bod.php" method="post" id="bidForm"
              data-minimaal-bod="<?php echo (float) $product['minimaal_bod']; ?>">
            <h3>Plaats uw bod</h3>

            <!-- Verborgen veld: koppelt het bod aan dit product -->
            <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">

            <div class="form__group">
                <label for="naam">Naam</label>
                <input type="text" id="naam" name="naam" maxlength="100"
                       value="<?php echo isset($_GET['naam']) ? htmlspecialchars($_GET['naam']) : ''; ?>" required>
                <div class="field-error" data-error-for="naam"></div>
            </div>

            <div class="form__group">
                <label for="email">E-mailadres</label>
                <input type="email" id="email" name="email" maxlength="150"
                       value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" required>
                <div class="field-error" data-error-for="email"></div>
            </div>

            <div class="form__group">
                <label for="bod_bedrag">Uw bod (in euro)</label>
                <input type="number" id="bod_bedrag" name="bod_bedrag"
                       min="<?php echo (float) $product['minimaal_bod']; ?>" step="0.01" required>
                <div class="field-error" data-error-for="bod_bedrag"></div>
            </div>

            <div class="form__group">
                <label for="bericht">Bericht</label>
                <textarea id="bericht" name="bericht" minlength="10" required></textarea>
                <div class="field-error" data-error-for="bericht"></div>
            </div>

            <button type="submit" class="btn btn--primary">Verstuur bod</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
