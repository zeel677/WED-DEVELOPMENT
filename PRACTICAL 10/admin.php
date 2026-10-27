
<?php
require "auth.php";
requireRole("admin");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
</head>
<body>
<h2>Admin Dashboard</h2>

<p>Welcome,
<?= htmlspecialchars($_SESSION["username"], ENT_QUOTES, "UTF-8") ?>!
</p>
<p>Role: Administrator</p>

<a href="logout.php">Logout</a>
</body>
</html>
