
<?php
require "auth.php";
require "db.php";
require "remember.php";

clearRememberToken($pdo);

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(session_name(), "", [
        "expires" => time() - 42000,
        "path" => $params["path"],
        "domain" => $params["domain"],
        "secure" => $params["secure"],
        "httponly" => $params["httponly"],
        "samesite" => $params["samesite"] ?? "Lax"
    ]);
}

session_destroy();

header("Location: index.php?logout=1");
exit;
?>
