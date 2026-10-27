
<?php
require "db.php";

$username = "admin";
$password = "Admin@123";

$stmt = $conn->prepare(
    "SELECT id FROM admins WHERE username = ?"
);
$stmt->bind_param("s", $username);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    exit("Admin already exists. Setup stopped.");
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO admins (username, password) VALUES (?, ?)"
);
$stmt->bind_param("ss", $username, $hash);
$stmt->execute();

echo "Admin created. Delete setup_admin.php now.";
?>
