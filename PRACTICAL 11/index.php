
<?php
require "db.php";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$q = trim($_GET["q"] ?? "");
$course = trim($_GET["course"] ?? "");
$page = max(1, (int)($_GET["page"] ?? 1));
$perPage = 5;

$where = ["status = 'active'"];
$params = [];
$types = "";

if ($q !== "") {
    $where[] = "(name LIKE ? OR email LIKE ? OR course LIKE ?)";
    $like = "%$q%";
    $params = array_merge($params, [$like, $like, $like]);
    $types .= "sss";
}

if ($course !== "") {
    $where[] = "course = ?";
    $params[] = $course;
    $types .= "s";
}

$whereSql = implode(" AND ", $where);

// Count records for pagination.
$countSql = "SELECT COUNT(*) AS total FROM students WHERE $whereSql";
$stmt = $conn->prepare($countSql);

if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$total = (int)$stmt->get_result()->fetch_assoc()["total"];
$totalPages = max(1, (int)ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

// Fetch the current page.
$sql = "SELECT id, name, email, course, phone
        FROM students
        WHERE $whereSql
        ORDER BY id DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$listTypes = $types . "ii";
$listParams = array_merge($params, [$perPage, $offset]);
$stmt->bind_param($listTypes, ...$listParams);
$stmt->execute();
$students = $stmt->get_result();

// Course filter options.
$courses = $conn->query(
    "SELECT DISTINCT course FROM students
     WHERE status = 'active' ORDER BY course"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Management</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Student Management System</h1>

    <?php if (isset($_GET["msg"])): ?>
        <p class="success">
        <?php
        $messages = [
            "added" => "Student added successfully.",
            "updated" => "Student updated successfully.",
            "deleted" => "Student deleted successfully.",
            "restored" => "Student restored successfully."
        ];
        echo e($messages[$_GET["msg"]] ?? "Operation completed.");
        ?>
        </p>
    <?php endif; ?>

    <?php if (isset($_GET["error"])): ?>
        <p class="error">Unable to complete the requested operation.</p>
    <?php endif; ?>

    <a class="button" href="add.php">+ Add Student</a>
    <a class="button secondary" href="export.php">Export CSV</a>

    <form method="GET" class="filters">
        <input type="text" name="q"
               placeholder="Search name, email or course"
               value="<?= e($q) ?>">

        <select name="course">
            <option value="">All courses</option>
            <?php while ($c = $courses->fetch_assoc()): ?>
                <option value="<?= e($c["course"]) ?>"
                    <?= $course === $c["course"] ? "selected" : "" ?>>
                    <?= e($c["course"]) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Search / Filter</button>
        <a class="button secondary" href="index.php">Reset</a>
    </form>

    <p>Active students found: <strong><?= $total ?></strong></p>

    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($students->num_rows === 0): ?>
            <tr>
                <td colspan="6">No students found.</td>
            </tr>
        <?php else: ?>
            <?php while ($s = $students->fetch_assoc()): ?>
                <tr>
                    <td><?= e($s["id"]) ?></td>
                    <td><?= e($s["name"]) ?></td>
                    <td><?= e($s["email"]) ?></td>
                    <td><?= e($s["course"]) ?></td>
                    <td><?= e($s["phone"]) ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?= (int)$s["id"] ?>">Edit</a>

                        <form method="POST" action="delete.php"
                              onsubmit="return confirm('Are you sure you want to delete this student?');">
                            <input type="hidden" name="id"
                                   value="<?= (int)$s["id"] ?>">
                            <input type="hidden" name="csrf"
                                   value="<?= e($_SESSION["csrf"] ?? "") ?>">
                            <button type="submit" class="danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
    </div>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="<?= $page === $i ? 'current' : '' ?>"
               href="?<?= e(http_build_query([
                   "q" => $q,
                   "course" => $course,
                   "page" => $i
               ])) ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>
</body>
</html>
