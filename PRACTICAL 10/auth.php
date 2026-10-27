
<?php
// Use HTTPS in production. This local setup uses HTTP.
ini_set("session.use_strict_mode", "1");
ini_set("session.use_only_cookies", "1");

session_set_cookie_params([
    "lifetime" => 0,
    "path" => "/",
    "secure" => !empty($_SERVER["HTTPS"])
        && $_SERVER["HTTPS"] !== "off",
    "httponly" => true,
    "samesite" => "Lax"
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

define("SESSION_TIMEOUT", 900); // 15 minutes

if (isset($_SESSION["user_id"])) {
    if (
        isset($_SESSION["last_activity"]) &&
        time() - $_SESSION["last_activity"] > SESSION_TIMEOUT
    ) {
        $_SESSION = [];
        session_destroy();

        header("Location: index.php?timeout=1");
        exit;
    }

    $_SESSION["last_activity"] = time();
}

function requireLogin(): void {
    if (!isset($_SESSION["user_id"])) {
        header("Location: index.php?error=login_required");
        exit;
    }
}

function requireRole(string $role): void {
    requireLogin();

    if (($_SESSION["role"] ?? "") !== $role) {
        http_response_code(403);
        exit("403 Forbidden: You are not authorized to access this page.");
    }
}
?>
