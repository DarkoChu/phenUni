<?php
declare(strict_types=1);

require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/db.php";

requireLogin();

$q = trim($_GET["q"] ?? "");
$role = trim($_GET["role"] ?? "");
$department = trim($_GET["department"] ?? "");

$directoryError = "";
$departments = [];
$people = [];

try {
    $db = getDB();
    $deptResult = $db->query("SELECT DISTINCT department FROM campus_people ORDER BY department ASC");
    while ($deptResult && ($row = $deptResult->fetch_assoc())) {
        $departments[] = $row;
    }

    $where = [];
    if ($q !== "") {
        $qEsc = $db->real_escape_string($q);
        $where[] = "(full_name LIKE '%{$qEsc}%' OR email LIKE '%{$qEsc}%' OR phone LIKE '%{$qEsc}%')";
    }
    if ($role !== "") {
        $roleEsc = $db->real_escape_string($role);
        $where[] = "role = '{$roleEsc}'";
    }
    if ($department !== "") {
        $deptEsc = $db->real_escape_string($department);
        $where[] = "department = '{$deptEsc}'";
    }

    $sql = "SELECT full_name, role, department, email, phone, profile_image, bio FROM campus_people";
    if ($where) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY full_name ASC";

    $peopleResult = $db->query($sql);
    while ($peopleResult && ($row = $peopleResult->fetch_assoc())) {
        $people[] = $row;
    }
} catch (Throwable $e) {
    $directoryError = $e->getMessage();
}

require_once __DIR__ . "/includes/header.php";
?>

<section>
    <h1>Campus Directory</h1>
    <p>Search across students, professors, alumni, and campus staff.</p>
    <?php if ($directoryError !== ""): ?>
        <p class="form-error"><?= e($directoryError) ?></p>
    <?php endif; ?>

    <form class="directory-form" method="get">
        <input type="text" name="q" id="liveSearch" placeholder="Search name, phone, or email" value="<?= e($q) ?>">
        <select name="role" id="liveRole">
            <option value="">All Roles</option>
            <option value="student" <?= $role === "student" ? "selected" : "" ?>>Student</option>
            <option value="professor" <?= $role === "professor" ? "selected" : "" ?>>Professor</option>
            <option value="staff" <?= $role === "staff" ? "selected" : "" ?>>Staff</option>
            <option value="alumni" <?= $role === "alumni" ? "selected" : "" ?>>Alumni</option>
        </select>
        <select name="department">
            <option value="">All Departments</option>
            <?php foreach ($departments as $item): ?>
                <?php $dept = $item["department"]; ?>
                <option value="<?= e($dept) ?>" <?= $department === $dept ? "selected" : "" ?>><?= e($dept) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary" type="submit">Search</button>
    </form>

    <div class="directory-grid" id="directoryGrid">
        <?php foreach ($people as $person): ?>
            <article
                class="person-card"
                data-role="<?= e($person["role"]) ?>"
                data-name="<?= e(strtolower($person["full_name"])) ?>"
                data-email="<?= e(strtolower($person["email"])) ?>"
                data-phone="<?= e(strtolower($person["phone"])) ?>"
            >
                <img src="<?= e($person["profile_image"] ?: "https://via.placeholder.com/120x120.png?text=Profile") ?>" alt="<?= e($person["full_name"]) ?> profile picture">
                <h3><?= e($person["full_name"]) ?></h3>
                <p class="badge"><?= e(ucfirst($person["role"])) ?> | <?= e($person["department"]) ?></p>
                <p>Email: <?= e($person["email"]) ?></p>
                <p>Phone: <?= e($person["phone"]) ?></p>
                <p><?= e($person["bio"]) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
    <?php if (!$people): ?>
        <p class="small-note">No results matched your current search.</p>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
