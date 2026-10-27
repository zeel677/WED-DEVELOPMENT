
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

if (
    isset($_SESSION["last_activity"]) &&
    time() - $_SESSION["last_activity"] > 900
) {
    $_SESSION = [];
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

$_SESSION["last_activity"] = time();

if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

function e($value): string {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}

function verifyCsrf(): void {
    if (
        empty($_SESSION["csrf"]) ||
        !hash_equals($_SESSION["csrf"], $_POST["csrf"] ?? "")
    ) {
        http_response_code(403);
        exit("Invalid request token. Please reload and try again.");
    }
}

function auditEvent(
    mysqli $conn,
    int $eventId,
    string $action,
    string $details
): void {
    $adminId = (int)$_SESSION["admin_id"];

    $stmt = $conn->prepare(
        "INSERT INTO event_audit
         (admin_id, event_id, action, details)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "iiss", $adminId, $eventId, $action, $details
    );
    $stmt->execute();
}
?>
