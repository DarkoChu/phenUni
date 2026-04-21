<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

function isLoggedIn(): bool
{
    return isset($_SESSION["user_id"]);
}

function currentUserName(): string
{
    return $_SESSION["user_name"] ?? "Guest";
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

