
<?php
require "auth.php";
require "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");
    exit("Method not allowed.");
}

verifyCsrf();

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php?error=1");
    exit;
}

try {
    $stmt = $conn->prepare(
        "SELECT title, poster FROM events WHERE id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();

    if (!$event) {
        header("Location: index.php?error=1");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows !== 1) {
        header("Location: index.php?error=1");
        exit;
    }

    auditEvent($conn, $id, "DELETE", "Deleted event: " . $event["title"]);

    if ($event["poster"]) {
        $path = __DIR__ . "/uploads/" . basename($event["poster"]);
        if (is_file($path)) {
            unlink($path);
        }
    }

    header("Location: index.php?msg=deleted");
    exit;
} catch (Throwable $ex) {
    error_log($ex->getMessage());
    header("Location: index.php?error=1");
    exit;
}
?>
