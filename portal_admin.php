<?php
declare(strict_types=1);

require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/env.php";

$adminUser = env("ADMIN_USERNAME", "admin");
$adminPassword = env("ADMIN_PASSWORD", "");

$message = "";
$error = "";
$adminInputUser = trim($_POST["admin_username"] ?? "");
$adminInputPass = $_POST["admin_password"] ?? "";
$isAdmin = $adminPassword !== "" && $adminInputUser === $adminUser && $adminInputPass === $adminPassword;

if ($isAdmin && $_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    try {
        $db = getDB();

        if ($action === "create_user") {
            $fullName = trim($_POST["full_name"] ?? "");
            $email = trim($_POST["email"] ?? "");
            $role = trim($_POST["role"] ?? "alumni");
            $password = $_POST["password"] ?? "";

            if ($fullName === "" || $email === "" || $password === "") {
                throw new RuntimeException("Name, email, and password are required.");
            }

            $stmt = $db->prepare("INSERT INTO users (full_name, email, role, password_hash) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                throw new RuntimeException("Failed to prepare create query.");
            }
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bind_param("ssss", $fullName, $email, $role, $hash);
            $stmt->execute();
            $stmt->close();
            $message = "Account created.";
        }

        if ($action === "update_user") {
            $id = (int) ($_POST["id"] ?? 0);
            $fullName = trim($_POST["full_name"] ?? "");
            $email = trim($_POST["email"] ?? "");
            $role = trim($_POST["role"] ?? "alumni");
            $newPassword = $_POST["new_password"] ?? "";

            if ($id <= 0 || $fullName === "" || $email === "") {
                throw new RuntimeException("Invalid user update payload.");
            }

            if ($newPassword !== "") {
                $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, role = ?, password_hash = ? WHERE id = ?");
                if (!$stmt) {
                    throw new RuntimeException("Failed to prepare update query.");
                }
                $hash = password_hash($newPassword, PASSWORD_BCRYPT);
                $stmt->bind_param("ssssi", $fullName, $email, $role, $hash, $id);
            } else {
                $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, role = ? WHERE id = ?");
                if (!$stmt) {
                    throw new RuntimeException("Failed to prepare update query.");
                }
                $stmt->bind_param("sssi", $fullName, $email, $role, $id);
            }
            $stmt->execute();
            $stmt->close();
            $message = "Account updated.";
        }

        if ($action === "delete_user") {
            $id = (int) ($_POST["id"] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException("Invalid user id.");
            }
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            if (!$stmt) {
                throw new RuntimeException("Failed to prepare delete query.");
            }
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $message = "Account deleted.";
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "admin_login") {
    $error = "Invalid admin credentials.";
}

require_once __DIR__ . "/includes/header.php";
?>

<?php if (!$isAdmin): ?>
    <section class="auth-box">
        <h1>Admin Access</h1>
        <?php if ($error !== ""): ?>
            <p class="form-error"><?= e($error) ?></p>
        <?php endif; ?>
        <form method="post" class="auth-form">
            <input type="hidden" name="action" value="admin_login">
            <label>Admin Username
                <input type="text" name="admin_username" required>
            </label>
            <label>Admin Password
                <input type="password" name="admin_password" required>
            </label>
            <button class="btn btn-primary" type="submit">Enter Admin Panel</button>
        </form>
        <p class="small-note">This page is intentionally hidden and does not keep admin login sessions.</p>
    </section>
<?php else: ?>
    <?php
    $db = null;
    $users = [];
    $tables = [];
    $selectedTable = "";
    $columns = [];
    $rows = [];
    $tableError = "";

    try {
        $db = getDB();

        $usersResult = $db->query("SELECT id, full_name, email, role, created_at FROM users ORDER BY id DESC");
        while ($usersResult && ($u = $usersResult->fetch_assoc())) {
            $users[] = $u;
        }

        $tableListResult = $db->query("SHOW TABLES");
        while ($tableListResult && ($t = $tableListResult->fetch_row())) {
            $tables[] = (string) $t[0];
        }

        $selectedTable = trim($_POST["table"] ?? "");
        if ($selectedTable === "" || !in_array($selectedTable, $tables, true)) {
            $selectedTable = $tables[0] ?? "";
        }

        if ($selectedTable !== "") {
            $colResult = $db->query("SHOW COLUMNS FROM `{$selectedTable}`");
            while ($colResult && ($c = $colResult->fetch_assoc())) {
                $columns[] = $c;
            }

            $rowResult = $db->query("SELECT * FROM `{$selectedTable}` LIMIT 200");
            while ($rowResult && ($r = $rowResult->fetch_assoc())) {
                $rows[] = $r;
            }
        }
    } catch (Throwable $e) {
        $tableError = $e->getMessage();
    }
    ?>

    <section>
        <div class="admin-head">
            <h1>Admin Panel</h1>
            <a class="btn btn-secondary" href="portal_admin.php">Clear Admin View</a>
        </div>
        <?php if ($message !== ""): ?>
            <p class="form-success"><?= e($message) ?></p>
        <?php endif; ?>
        <?php if ($error !== ""): ?>
            <p class="form-error"><?= e($error) ?></p>
        <?php endif; ?>
        <?php if ($tableError !== ""): ?>
            <p class="form-error"><?= e($tableError) ?></p>
        <?php endif; ?>

        <div class="admin-grid">
            <article class="admin-card">
                <h3>Create Account</h3>
                <form method="post" class="auth-form">
                    <input type="hidden" name="action" value="create_user">
                    <input type="hidden" name="admin_username" value="<?= e($adminInputUser) ?>">
                    <input type="hidden" name="admin_password" value="<?= e($adminInputPass) ?>">
                    <label>Full Name
                        <input type="text" name="full_name" required>
                    </label>
                    <label>Email
                        <input type="email" name="email" required>
                    </label>
                    <label>Role
                        <select name="role">
                            <option value="alumni">Alumni</option>
                            <option value="student">Student</option>
                            <option value="professor">Professor</option>
                            <option value="staff">Staff</option>
                        </select>
                    </label>
                    <label>Password
                        <input type="password" name="password" required>
                    </label>
                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </article>

            <article class="admin-card">
                <h3>Manage Accounts</h3>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= e((string) $user["id"]) ?></td>
                                <td>
                                    <form method="post" class="inline-form">
                                        <input type="hidden" name="action" value="update_user">
                                        <input type="hidden" name="id" value="<?= e((string) $user["id"]) ?>">
                                        <input type="hidden" name="admin_username" value="<?= e($adminInputUser) ?>">
                                        <input type="hidden" name="admin_password" value="<?= e($adminInputPass) ?>">
                                        <input type="text" name="full_name" value="<?= e((string) $user["full_name"]) ?>" required>
                                </td>
                                <td><input type="email" name="email" value="<?= e((string) $user["email"]) ?>" required></td>
                                <td>
                                    <select name="role">
                                        <?php foreach (["alumni", "student", "professor", "staff"] as $r): ?>
                                            <option value="<?= e($r) ?>" <?= $user["role"] === $r ? "selected" : "" ?>><?= e(ucfirst($r)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><?= e((string) $user["created_at"]) ?></td>
                                <td class="admin-actions">
                                    <input type="password" name="new_password" placeholder="New pass (optional)">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    </form>
                                    <form method="post" onsubmit="return confirm('Delete this user?');">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="id" value="<?= e((string) $user["id"]) ?>">
                                        <input type="hidden" name="admin_username" value="<?= e($adminInputUser) ?>">
                                        <input type="hidden" name="admin_password" value="<?= e($adminInputPass) ?>">
                                        <button type="submit" class="btn btn-secondary">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </article>
        </div>

        <article class="admin-card">
            <h3>SQL Table Viewer</h3>
            <form method="post" class="directory-form">
                <input type="hidden" name="action" value="view_table">
                <input type="hidden" name="admin_username" value="<?= e($adminInputUser) ?>">
                <input type="hidden" name="admin_password" value="<?= e($adminInputPass) ?>">
                <select name="table">
                    <?php foreach ($tables as $tableName): ?>
                        <option value="<?= e($tableName) ?>" <?= $selectedTable === $tableName ? "selected" : "" ?>><?= e($tableName) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-primary" type="submit">Load Table</button>
            </form>

            <p class="small-note">Showing columns and rows for table: <strong><?= e($selectedTable) ?></strong></p>

            <?php if ($selectedTable !== ""): ?>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                        <tr>
                            <th>Column</th>
                            <th>Type</th>
                            <th>Null</th>
                            <th>Key</th>
                            <th>Default</th>
                            <th>Extra</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($columns as $col): ?>
                            <tr>
                                <td><?= e((string) $col["Field"]) ?></td>
                                <td><?= e((string) $col["Type"]) ?></td>
                                <td><?= e((string) $col["Null"]) ?></td>
                                <td><?= e((string) $col["Key"]) ?></td>
                                <td><?= e((string) ($col["Default"] ?? "NULL")) ?></td>
                                <td><?= e((string) $col["Extra"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                        <tr>
                            <?php foreach ($columns as $col): ?>
                                <th><?= e((string) $col["Field"]) ?></th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <?php foreach ($columns as $col): ?>
                                    <?php $field = (string) $col["Field"]; ?>
                                    <td><?= e((string) ($row[$field] ?? "")) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </article>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
