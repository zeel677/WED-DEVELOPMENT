
<?php
require "db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");
    exit("Method not allowed.");
}

if (
    empty($_SESSION["csrf"]) ||
    !hash_equals($_SESSION["csrf"], $_POST["csrf"] ?? "")
) {
    http_response_code(403);
    exit("Invalid request token. Please reload the page.");
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php?error=1");
    exit;
}

try {
    // Permanent delete, as required by the basic CRUD practical.
    $stmt = $conn->prepare(
        "DELETE FROM students WHERE id = ? AND status = 'active'"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 1) {
        header("Location: index.php?msg=deleted");
    } else {
        header("Location: index.php?error=1");
    }
    exit;
} catch (mysqli_sql_exception $ex) {
    error_log($ex->getMessage());
    header("Location: index.php?error=1");
    exit;
}
?>
