
<?php
require "auth.php";
require "db.php";

function savePoster(array $file): ?string {
    if (($file["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file["error"] !== UPLOAD_ERR_OK) {
        throw new RuntimeException("Poster upload failed.");
    }

    if ($file["size"] > 2 * 1024 * 1024) {
        throw new RuntimeException("Poster must be 2 MB or smaller.");
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file["tmp_name"]);
    $allowed = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException("Only JPG, PNG, and WebP are allowed.");
    }

    $dir = __DIR__ . "/uploads/";
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        throw new RuntimeException("Cannot create upload directory.");
    }

    $filename = bin2hex(random_bytes(16)) . "." . $allowed[$mime];

    if (!move_uploaded_file($file["tmp_name"], $dir . $filename)) {
        throw new RuntimeException("Could not save poster.");
    }

    return $filename;
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit("Invalid event ID.");
}

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    http_response_code(404);
    exit("Event not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrf();

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $date = $_POST["event_date"] ?? "";
    $category = trim($_POST["category"] ?? "");
    $status = $_POST["status"] ?? "";

    $d = DateTime::createFromFormat("!Y-m-d", $date);
    $validDate = $d && $d->format("Y-m-d") === $date;

    if (
        $title === "" || strlen($title) > 150 ||
        $description === "" || !$validDate ||
        $category === "" || strlen($category) > 80 ||
        !in_array($status, ["open", "closed"], true)
    ) {
        $error = "Please enter valid event details.";
    } else {
        $newPoster = null;

        try {
            $newPoster = savePoster($_FILES["poster"] ?? []);

            $poster = $newPoster ?? $event["poster"];

            $stmt = $conn->prepare(
                "UPDATE events SET
                 title = ?, description = ?, event_date = ?,
                 category = ?, status = ?, poster = ?
                 WHERE id = ?"
            );
            $stmt->bind_param(
                "ssssssi",
                $title, $description, $date, $category,
                $status, $poster, $id
            );
            $stmt->execute();

            auditEvent($conn, $id, "UPDATE", "Updated event: " . $title);

            if ($newPoster && $event["poster"]) {
                $old = __DIR__ . "/uploads/" . basename($event["poster"]);
                if (is_file($old)) {
                    unlink($old);
                }
            }

            header("Location: index.php?msg=updated");
            exit;
        } catch (Throwable $ex) {
            if ($newPoster && is_file(__DIR__ . "/uploads/" . $newPoster)) {
                unlink(__DIR__ . "/uploads/" . $newPoster);
            }

            if ($ex instanceof RuntimeException) {
                $error = $ex->getMessage();
            } else {
                error_log($ex->getMessage());
                $error = "Could not update event.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Event</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Edit Event</h1>

<?php if ($error !== ""): ?>
<p class="error"><?= e($error) ?></p>
<?php endif; ?>

<?php if ($event["poster"]): ?>
<p>Current poster:</p>
<img class="poster large"
     src="uploads/<?= rawurlencode($event["poster"]) ?>"
     alt="Current event poster">
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>">

    <label>Title</label>
    <input name="title" maxlength="150" required value="<?= e($event["title"]) ?>">

    <label>Description</label>
    <textarea name="description" required><?= e($event["description"]) ?></textarea>

    <label>Event Date</label>
    <input type="date" name="event_date" required value="<?= e($event["event_date"]) ?>">

    <label>Category</label>
    <input name="category" maxlength="80" required value="<?= e($event["category"]) ?>">

    <label>Status</label>
    <select name="status">
        <option value="open" <?= $event["status"] === "open" ? "selected" : "" ?>>Open</option>
        <option value="closed" <?= $event["status"] === "closed" ? "selected" : "" ?>>Closed</option>
    </select>

    <label>Replace poster (optional, max 2 MB)</label>
    <input type="file" name="poster"
           accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

    <button type="submit">Update Event</button>
    <a class="button secondary" href="index.php">Cancel</a>
</form>
</div>
</body>
</html>
