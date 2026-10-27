
<?php
require "auth.php";
require "db.php";

$q = trim($_GET["q"] ?? "");
$category = trim($_GET["category"] ?? "");
$status = $_GET["status"] ?? "";

$where = [];
$params = [];
$types = "";

if ($q !== "") {
    $where[] = "(title LIKE ? OR description LIKE ? OR category LIKE ?)";
    $like = "%$q%";
    $params = array_merge($params, [$like, $like, $like]);
    $types .= "sss";
}

if ($category !== "") {
    $where[] = "category = ?";
    $params[] = $category;
    $types .= "s";
}

if (in_array($status, ["open", "closed"], true)) {
    $where[] = "status = ?";
    $params[] = $status;
    $types .= "s";
}

$sql = "SELECT * FROM events";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY event_date DESC, id DESC";

$stmt = $conn->prepare($sql);

if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$events = $stmt->get_result();

$categories = $conn->query(
    "SELECT DISTINCT category FROM events ORDER BY category"
);

$messages = [
    "added" => "Event added successfully.",
    "updated" => "Event updated successfully.",
    "deleted" => "Event deleted successfully."
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Event Management</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Event Management Admin</h1>
<p>Logged in as <?= e($_SESSION["admin_name"] ?? "Admin") ?></p>

<?php if (isset($_GET["msg"], $messages[$_GET["msg"]])): ?>
<p class="success"><?= e($messages[$_GET["msg"]]) ?></p>
<?php endif; ?>

<?php if (isset($_GET["error"])): ?>
<p class="error">The requested operation could not be completed.</p>
<?php endif; ?>

<a class="button" href="add.php">+ Add Event</a>
<a class="button secondary" href="logout.php">Logout</a>

<form method="GET" class="filters">
    <input name="q" placeholder="Search events"
           value="<?= e($q) ?>">

    <select name="category">
        <option value="">All categories</option>
        <?php while ($c = $categories->fetch_assoc()): ?>
            <option value="<?= e($c["category"]) ?>"
                <?= $category === $c["category"] ? "selected" : "" ?>>
                <?= e($c["category"]) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <select name="status">
        <option value="">All statuses</option>
        <option value="open" <?= $status === "open" ? "selected" : "" ?>>Open</option>
        <option value="closed" <?= $status === "closed" ? "selected" : "" ?>>Closed</option>
    </select>

    <button type="submit">Filter</button>
    <a class="button secondary" href="index.php">Reset</a>
</form>

<div class="table-wrap">
<table>
<thead>
<tr>
    <th>ID</th>
    <th>Poster</th>
    <th>Title / Description</th>
    <th>Date</th>
    <th>Category</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
<?php if ($events->num_rows === 0): ?>
<tr><td colspan="7">No events found.</td></tr>
<?php else: ?>
<?php while ($event = $events->fetch_assoc()): ?>
<tr>
    <td><?= (int)$event["id"] ?></td>
    <td>
        <?php if ($event["poster"]): ?>
            <img class="poster"
                 src="uploads/<?= rawurlencode($event["poster"]) ?>"
                 alt="Event poster">
        <?php else: ?>
            No poster
        <?php endif; ?>
    </td>
    <td>
        <strong><?= e($event["title"]) ?></strong><br>
        <?= nl2br(e($event["description"])) ?>
    </td>
    <td><?= e($event["event_date"]) ?></td>
    <td><?= e($event["category"]) ?></td>
    <td><?= e(ucfirst($event["status"])) ?></td>
    <td>
        <a href="edit.php?id=<?= (int)$event["id"] ?>">Edit</a>
        <form method="POST" action="delete.php"
              onsubmit="return confirm('Delete this event permanently?');">
            <input type="hidden" name="id" value="<?= (int)$event["id"] ?>">
            <input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>">
            <button class="danger" type="submit">Delete</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</body>
</html>
