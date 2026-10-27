<?php
require_once "db.php";

echo "<h2>StudentHub Database</h2>";
echo "<p style='color:green;'>Database connected successfully!</p>";

$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM students");
$stmt->execute();

$result = $stmt->fetch();

echo "<p>Total students: " . (int)$result["total"] . "</p>";
?>