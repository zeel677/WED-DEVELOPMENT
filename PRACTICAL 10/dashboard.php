
<?php
require "auth.php";
requireRole("student");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
</head>
<body>
<h2>Student Dashboard</h2>

<p>Welcome,
<?= htmlspecialchars($_SESSION["username"], ENT_QUOTES, "UTF-8") ?>!
</p>
<p>Role: Student</p>

<a href="logout.php">Logout</a>
</body>
</html>
