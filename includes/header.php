<?php
declare(strict_types=1);

$currentPage = basename($_SERVER["PHP_SELF"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phen University Alumni Network</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="index.php">Phen University</a>
        <button class="menu-toggle" id="menuToggle" type="button" aria-label="Toggle menu">Menu</button>
        <nav class="site-nav" id="siteNav">
            <a href="index.php" class="<?= $currentPage === "index.php" ? "active" : "" ?>">Home</a>
            <a href="directory.php" class="<?= $currentPage === "directory.php" ? "active" : "" ?>">Directory</a>
            <a href="support.php" class="<?= $currentPage === "support.php" ? "active" : "" ?>">Support</a>
            <?php if (isLoggedIn()): ?>
                <span class="welcome">Hi, <?= e(currentUserName()) ?></span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php" class="<?= $currentPage === "login.php" ? "active" : "" ?>">Login</a>
                <a href="signup.php" class="<?= $currentPage === "signup.php" ? "active" : "" ?>">Sign Up</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container page-content">

