<?php
// Wealth Creators — gedeelde header (nav + <head>)
// Pagina's kunnen vóór de include $page_title instellen voor een eigen titel.
$page_title = isset($page_title) ? $page_title : 'Wealth Creators';

// Bepaal welk navigatie-item actief is op basis van het huidige bestand.
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="Wealth Creators — de exclusieve marktplaats voor de super rijken.">

    <!-- Google Fonts: Cormorant Garamond (koppen) + Inter (tekst) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container site-header__inner">
            <a class="brand" href="index.php">
                <span class="brand__name">Wealth Creators</span>
                <span class="brand__tagline">Marktplaats voor de $up&euro;r Rich</span>
            </a>

            <button class="nav-toggle" aria-label="Menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>

            <nav class="site-nav">
                <a href="index.php" class="<?php echo $current_page === 'index.php' ? 'is-active' : ''; ?>">Home</a>
                <a href="overzicht.php" class="<?php echo $current_page === 'overzicht.php' ? 'is-active' : ''; ?>">Overzicht</a>
                <a href="berichten.php" class="<?php echo $current_page === 'berichten.php' ? 'is-active' : ''; ?>">Mijn biedingen</a>
            </nav>
        </div>
    </header>

    <main class="site-main">
