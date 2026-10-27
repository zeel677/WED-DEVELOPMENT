
<?php
require "auth.php";
require "db.php";
require "remember.php";

// Already logged-in users go to their own dashboard.
if (isset($_SESSION["user_id"])) {
    header(
        "Location: " .
        (($_SESSION["role"] ?? "") === "admin"
            ? "admin.php" : "dashboard.php")
    );
    exit;
}

// Restore login from a valid remember-me cookie.
if (restoreRememberedLogin($pdo)) {
    header(
        "Location: " .
        (($_SESSION["role"] ?? "") === "admin"
            ? "admin.php" : "dashboard.php")
    );
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $remember = isset($_POST["remember"]);

    $stmt = $pdo->prepare(
        "SELECT id, username, password, role
         FROM users
         WHERE username = ? OR email = ?
         LIMIT 1"
    );
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);

        $_SESSION["user_id"] = (int)$user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["last_activity"] = time();

        $update = $pdo->prepare(
            "UPDATE users SET last_login = NOW() WHERE id = ?"
        );
        $update->execute([$user["id"]]);

        if ($remember) {
            createRememberToken($pdo, (int)$user["id"]);
        }

        header(
            "Location: " .
            ($user["role"] === "admin"
                ? "admin.php" : "dashboard.php")
        );
        exit;
    }

    $error = "Invalid username/email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Secure Login</title>
</head>
<body>
<h2>Secure Login</h2>

<?php if (isset($_GET["timeout"])): ?>
    <p>Your session expired. Please log in again.</p>
<?php endif; ?>

<?php if (isset($_GET["error"])): ?>
    <p>Please log in to continue.</p>
<?php endif; ?>

<?php if ($error !== ""): ?>
    <p style="color:red">
        <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
    </p>
<?php endif; ?>
<?php if (isset($_GET["logout"])): ?>
    <p>You have been logged out successfully.</p>
<?php endif; ?>

<form method="POST" action="index.php">
    <label>Username or Email:</label><br>
    <input type="text" name="username" required
           autocomplete="username"><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required
           autocomplete="current-password"><br><br>

    <label>
        <input type="checkbox" name="remember" value="1">
        Remember me on this device
    </label><br><br>

    <button type="submit">Login</button>
</form>
</body>
</html>
