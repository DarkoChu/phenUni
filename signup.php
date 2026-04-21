<?php
declare(strict_types=1);

require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/db.php";

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $role = trim($_POST["role"] ?? "alumni");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($fullName === "" || $email === "" || $password === "") {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email address.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        try {
            $db = getDB();
            $emailEsc = $db->real_escape_string($email);
            $fullNameEsc = $db->real_escape_string($fullName);
            $roleEsc = $db->real_escape_string($role);

            $check = $db->query("SELECT id FROM users WHERE email = '{$emailEsc}' LIMIT 1");
            if ($check && $check->num_rows > 0) {
                $error = "Email is already registered.";
            }

            if ($error === "") {
                $passwordHash = $db->real_escape_string(password_hash($password, PASSWORD_BCRYPT));
                $ok = $db->query(
                    "INSERT INTO users (full_name, email, role, password_hash)
                     VALUES ('{$fullNameEsc}', '{$emailEsc}', '{$roleEsc}', '{$passwordHash}')"
                );
                if (!$ok) {
                    throw new RuntimeException("Signup failed: " . $db->error);
                }
                $success = "Account created successfully. You can log in now.";
            }
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

require_once __DIR__ . "/includes/header.php";
?>

<section class="auth-box">
    <h1>Sign Up</h1>
    <?php if ($error !== ""): ?>
        <p class="form-error"><?= e($error) ?></p>
    <?php endif; ?>
    <?php if ($success !== ""): ?>
        <p class="form-success"><?= e($success) ?></p>
    <?php endif; ?>
    <form method="post" class="auth-form">
        <label>Full Name
            <input type="text" name="full_name" required>
        </label>
        <label>Email
            <input type="email" name="email" required>
        </label>
        <label>Role
            <select name="role" required>
                <option value="alumni">Alumni</option>
                <option value="student">Student</option>
                <option value="professor">Professor</option>
                <option value="staff">Staff</option>
            </select>
        </label>
        <label>Password
            <input type="password" name="password" minlength="8" required>
        </label>
        <label>Confirm Password
            <input type="password" name="confirm_password" minlength="8" required>
        </label>
        <button class="btn btn-primary" type="submit">Create Account</button>
    </form>
    <p class="small-note">Already have an account? <a href="login.php">Login</a>.</p>
</section>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
