
<?php
session_start();
require "db.php";

if (isset($_SESSION["admin_id"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, username, password
         FROM admins WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($password, $admin["password"])) {
        session_regenerate_id(true);
        $_SESSION["admin_id"] = (int)$admin["id"];
        $_SESSION["admin_name"] = $admin["username"];
        $_SESSION["csrf"] = bin2hex(random_bytes(32));
        $_SESSION["last_activity"] = time();

        header("Location: index.php");
        exit;
    }

    $error = "Invalid username or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Event Admin Login</h1>

<?php if ($error): ?>
<p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Username</label>
    <input name="username" required autocomplete="username">

    <label>Password</label>
    <input type="password" name="password" required
           autocomplete="current-password">

    <button type="submit">Login</button>
</form>
</div>
</body>
</html>
