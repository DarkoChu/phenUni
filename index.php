<?php
declare(strict_types=1);

require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/header.php";
?>

<section class="hero">
    <div class="hero-overlay"></div>
    <img src="https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1600&q=80" alt="Phen University campus" class="hero-image">
    <div class="hero-content">
        <p class="tagline">Phen University Alumni Network</p>
        <h1>Reconnect, collaborate, and grow with the Phen community.</h1>
        <p>Meet fellow alumni, discover campus contacts, and stay connected with students, professors, and university staff.</p>
        <div class="hero-actions">
            <?php if (isLoggedIn()): ?>
                <a href="directory.php" class="btn btn-primary">Explore Directory</a>
            <?php else: ?>
                <a href="signup.php" class="btn btn-primary">Join Now</a>
                <a href="login.php" class="btn btn-secondary">Log In</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="cards-section">
    <article class="feature-card">
        <h3>Alumni Connections</h3>
        <p>Find old classmates and create new opportunities through the alumni network.</p>
    </article>
    <article class="feature-card">
        <h3>Campus Directory</h3>
        <p>Search students, professors, and staff by name, role, or department with built-in filters.</p>
    </article>
    <article class="feature-card">
        <h3>Support Team</h3>
        <p>Need help? Reach out to the Phen University support desk anytime from the support page.</p>
    </article>
</section>

<?php require_once __DIR__ . "/includes/footer.php"; ?>

