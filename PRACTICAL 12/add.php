
<?php
require "auth.php";
require "db.php";

$error = "";
$maxSize = 2 * 1024 * 1024;

function savePoster(array $file, int $maxSize): ?string {
    if (!isset($file["error"]) || $file["error"] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file["error"] !== UPLOAD_ERR_OK) {
        throw new RuntimeException("Poster upload failed.");
    }

    if ($file["size"] > $maxSize) {
        throw new RuntimeException("Poster must be 2 MB or smaller.");
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file["tmp_name"]);

    $allowed = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/webp" => "webp"
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException("Only JPG, PNG, and WebP images are allowed.");
    }

    $dir = __DIR__ . "/uploads/";

    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        throw new RuntimeException("Upload directory could not be created.");
    }

    $filename = bin2hex(random_bytes(16)) . "." . $allowed[$mime];

    if (!move_uploaded_file($file["tmp_name"], $dir . $filename)) {
        throw new RuntimeException("Could not save the poster.");
    }

    return $filename;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrf();

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $date = $_POST["event_date"] ?? "";
    $category = trim($_POST["category"] ?? "");
    $status = $_POST["status"] ?? "open";

    $dateObj = DateTime::createFromFormat("!Y-m-d", $date);
    $validDate = $dateObj && $dateObj->format("Y-m-d") === $date;

    if (
        $title === "" || strlen($title) > 150 ||
        $description === "" || !$validDate ||
        $category === "" || strlen($category) > 80 ||
        !in_array($status, ["open", "closed"], true)
    ) {
        $error = "Please enter valid event details.";
    } else {
        $poster = null;

        try {
            $poster = savePoster($_FILES["poster"] ?? [], $maxSize);

            $stmt = $conn->prepare(
                "INSERT INTO events
                 (title, description, event_date, category, status, poster)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "ssssss",
                $title, $description, $date, $category, $status, $poster
            );
            $stmt->execute();

            $eventId = $conn->insert_id;
            auditEvent($conn, $eventId, "CREATE", "Created event: " . $title);

            header("Location: index.php?msg=added");
            exit;
        } catch (Throwable $ex) {
            if ($poster !== null && is_file(__DIR__ . "/uploads/" . $poster)) {
                unlink(__DIR__ . "/uploads/" . $poster);
            }

            if ($ex instanceof RuntimeException) {
                $error = $ex->getMessage();
            } else {
                error_log($ex->getMessage());
                $error = "Could not save the event.";
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
<title>Add Event</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Add Event</h1>

<?php if ($error !== ""): ?>
<p class="error"><?= e($error) ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>">
    <input type="hidden" name="MAX_FILE_SIZE" value="2097152">

    <label>Event Title</label>
    <input name="title" maxlength="150" required>

    <label>Description</label>
    <textarea name="description" required></textarea>

    <label>Event Date</label>
    <input type="date" name="event_date" required>

    <label>Category</label>
    <input name="category" maxlength="80"
           placeholder="Technology, Sports, Cultural..." required>

    <label>Status</label>
    <select name="status">
        <option value="open">Open</option>
        <option value="closed">Closed</option>
    </select>

    <label>Poster (JPG, PNG, WebP; maximum 2 MB)</label>
    <input type="file" name="poster"
           accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

    <button type="submit">Save Event</button>
    <a class="button secondary" href="index.php">Cancel</a>
</form>
</div>
</body>
</html>
