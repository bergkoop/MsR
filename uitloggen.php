<?php
// Wealth Creators — uitloggen
// Vernietigt de sessie en stuurt de bezoeker terug naar de homepage.

require_once __DIR__ . '/config.php';

// Wis alle sessievariabelen en vernietig de sessie.
$_SESSION = array();
session_destroy();

header('Location: index.php');
exit;
