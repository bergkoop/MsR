<?php
// Wealth Creators — homepage
// Toont een hero, het categorie-overzicht en een selectie uitgelichte producten.

require_once __DIR__ . '/config.php';

// Haal alle categorieën op (geen gebruikersinvoer, maar consistent met prepared statements).
$categories = array();
$stmt = mysqli_prepare($conn, 'SELECT id, naam, afbeelding_url FROM categorieen ORDER BY naam ASC');
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}
mysqli_stmt_close($stmt);

// Haal een aantal uitgelichte producten op: de nieuwste, beschikbare items met hun hoofdafbeelding.
$featured = array();
$sql = 'SELECT p.id, p.naam, p.prijs, p.locatie, p.status, c.naam AS categorie_naam,
               (SELECT pa.afbeelding_url
                  FROM product_afbeeldingen pa
                 WHERE pa.product_id = p.id
                 ORDER BY pa.is_hoofdafbeelding DESC, pa.volgorde ASC
                 LIMIT 1) AS afbeelding_url
          FROM producten p
          JOIN categorieen c ON c.id = p.categorie_id
         WHERE p.status = "beschikbaar"
         ORDER BY p.aangemaakt_op DESC
         LIMIT 6';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $featured[] = $row;
}
mysqli_stmt_close($stmt);

$page_title = 'Wealth Creators — Marktplaats voor de super rijken';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero -->
<section class="hero">
    <div class="container hero__content">
        <span class="eyebrow">Marktplaats voor de $up&euro;r Rich</span>
        <h1>Bezit het <em>onbereikbare</em>.</h1>
        <p>Een zorgvuldig samengestelde collectie supercars, jachten, helikopters en
           tijdloze juwelen. Voor wie alleen het beste verdient.</p>
        <div class="hero__actions">
            <a href="overzicht.php" class="btn btn--primary">Ontdek de collectie</a>
            <a href="#categorieen" class="btn btn--ghost">Bekijk categorie&euml;n</a>
        </div>
    </div>
</section>

<!-- Categorieën -->
<section class="section" id="categorieen">
    <div class="container">
        <div class="section__head">
            <span class="eyebrow">Categorie&euml;n</span>
            <h2>Verken per categorie</h2>
            <p>Van hypercars tot zeldzame diamanten &mdash; elk object met een verhaal.</p>
        </div>

        <div class="grid grid--categories">
            <?php foreach ($categories as $category): ?>
                <a class="category-card" href="overzicht.php?categorie=<?php echo (int) $category['id']; ?>">
                    <img src="<?php echo htmlspecialchars($category['afbeelding_url']); ?>"
                         alt="<?php echo htmlspecialchars($category['naam']); ?>">
                    <div class="category-card__overlay">
                        <h3><?php echo htmlspecialchars($category['naam']); ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Uitgelichte producten -->
<section class="section" style="padding-top: 0;">
    <div class="container">
        <div class="section__head">
            <span class="eyebrow">Uitgelicht</span>
            <h2>Recent toegevoegd</h2>
            <p>Een greep uit de nieuwste objecten in de collectie.</p>
        </div>

        <?php if (empty($featured)): ?>
            <div class="empty-state">
                <h3>Nog geen producten</h3>
                <p>Er zijn op dit moment geen uitgelichte producten beschikbaar.</p>
            </div>
        <?php else: ?>
            <div class="grid grid--products">
                <?php foreach ($featured as $product): ?>
                    <a class="product-card" href="showcase.php?id=<?php echo (int) $product['id']; ?>">
                        <div class="product-card__media">
                            <img src="<?php echo htmlspecialchars($product['afbeelding_url']); ?>"
                                 alt="<?php echo htmlspecialchars($product['naam']); ?>">
                        </div>
                        <div class="product-card__body">
                            <span class="product-card__cat"><?php echo htmlspecialchars($product['categorie_naam']); ?></span>
                            <h3 class="product-card__title"><?php echo htmlspecialchars($product['naam']); ?></h3>
                            <span class="product-card__meta"><?php echo htmlspecialchars($product['locatie']); ?></span>
                            <span class="product-card__price">&euro; <?php echo number_format((float) $product['prijs'], 0, ',', '.'); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div style="text-align: center; margin-top: 48px;">
                <a href="overzicht.php" class="btn btn--ghost">Bekijk alle producten</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
