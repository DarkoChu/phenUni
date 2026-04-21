<?php
declare(strict_types=1);

require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/db.php";

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Email and password are required.";
    } else {
        try {
            $db = getDB();
            $emailEsc = $db->real_escape_string($email);
            $result = $db->query("SELECT id, full_name, password_hash FROM users WHERE email = '{$emailEsc}' LIMIT 1");
            $user = $result ? $result->fetch_assoc() : null;

            if ($user && password_verify($password, (string) $user["password_hash"])) {
                $_SESSION["user_id"] = (int) $user["id"];
                $_SESSION["user_name"] = (string) $user["full_name"];
                header("Location: directory.php");
                exit;
            }
            $error = "Invalid credentials.";
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

require_once __DIR__ . "/includes/header.php";
?>

<section class="auth-box">
    <h1>Login</h1>
    <?php if ($error !== ""): ?>
        <p class="form-error"><?= e($error) ?></p>
    <?php endif; ?>
    <form method="post" class="auth-form">
        <label>Email
            <input type="email" name="email" required>
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <button class="btn btn-primary" type="submit">Login</button>
    </form>
    <p class="small-note">No account yet? <a href="signup.php">Create one</a>.</p>
</section>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
