<?php
// Wealth Creators — productoverzicht
// Toont alle producten met een categoriefilter. Het filteren gebeurt direct in de
// browser (main.js); de ?categorie=ID parameter bepaalt welk filter bij het laden
// actief is, zodat links vanaf de homepage meteen op de juiste categorie staan.

require_once __DIR__ . '/config.php';

// Bepaal het initieel actieve filter uit de URL (0 = alle categorieën).
$active_categorie = isset($_GET['categorie']) ? (int) $_GET['categorie'] : 0;

// Haal alle categorieën op voor de filterbalk.
$categories = array();
$stmt = mysqli_prepare($conn, 'SELECT id, naam FROM categorieen ORDER BY naam ASC');
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}
mysqli_stmt_close($stmt);

// Haal alle producten op met hun categorie en hoofdafbeelding.
$products = array();
$sql = 'SELECT p.id, p.naam, p.prijs, p.locatie, p.status, p.categorie_id,
               c.naam AS categorie_naam,
               (SELECT pa.afbeelding_url
                  FROM product_afbeeldingen pa
                 WHERE pa.product_id = p.id
                 ORDER BY pa.is_hoofdafbeelding DESC, pa.volgorde ASC
                 LIMIT 1) AS afbeelding_url
          FROM producten p
          JOIN categorieen c ON c.id = p.categorie_id
         ORDER BY p.aangemaakt_op DESC';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
mysqli_stmt_close($stmt);

$page_title = 'Overzicht — Wealth Creators';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="section__head">
            <span class="eyebrow">De collectie</span>
            <h2>Alle objecten</h2>
            <p>Filter op categorie om sneller te vinden wat u zoekt.</p>
        </div>

        <!-- Filterbalk: filtert direct in de browser via main.js -->
        <div class="filter-bar" id="filterBar">
            <button type="button" data-filter="all"
                    class="<?php echo $active_categorie === 0 ? 'is-active' : ''; ?>">
                Alle
            </button>
            <?php foreach ($categories as $category): ?>
                <button type="button" data-filter="<?php echo (int) $category['id']; ?>"
                        class="<?php echo $active_categorie === (int) $category['id'] ? 'is-active' : ''; ?>">
                    <?php echo htmlspecialchars($category['naam']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <?php if (empty($products)): ?>
            <div class="empty-state">
                <h3>Nog geen producten</h3>
                <p>Er staan op dit moment geen objecten in de collectie.</p>
            </div>
        <?php else: ?>
            <div class="grid grid--products" id="productGrid">
                <?php foreach ($products as $product): ?>
                    <a class="product-card" href="showcase.php?id=<?php echo (int) $product['id']; ?>"
                       data-categorie="<?php echo (int) $product['categorie_id']; ?>">
                        <div class="product-card__media">
                            <img src="<?php echo htmlspecialchars($product['afbeelding_url']); ?>"
                                 alt="<?php echo htmlspecialchars($product['naam']); ?>">
                        </div>
                        <div class="product-card__body">
                            <span class="product-card__cat"><?php echo htmlspecialchars($product['categorie_naam']); ?></span>
                            <h3 class="product-card__title"><?php echo htmlspecialchars($product['naam']); ?></h3>
                            <span class="product-card__meta">
                                <?php echo htmlspecialchars($product['locatie']); ?> &middot;
                                <span class="badge badge--<?php echo htmlspecialchars($product['status']); ?>">
                                    <?php echo htmlspecialchars($product['status']); ?>
                                </span>
                            </span>
                            <span class="product-card__price">&euro; <?php echo number_format((float) $product['prijs'], 0, ',', '.'); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Melding wanneer een filter geen resultaten oplevert (getoond door main.js) -->
            <div class="empty-state" id="noResults" style="display: none;">
                <h3>Geen objecten in deze categorie</h3>
                <p>Kies een andere categorie of bekijk alle objecten.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
