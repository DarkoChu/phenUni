<?php
declare(strict_types=1);

function getDB(): mysqli
{
    static $conn = null;
    if ($conn instanceof mysqli) {
        return $conn;
    }

    $conn = @new mysqli("127.0.0.1", "phen_user", "phen_pass_123", "phen_university");
    if ($conn->connect_errno) {
        throw new RuntimeException("DB connect failed. Start MySQL and import phen_university.sql.");
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}
