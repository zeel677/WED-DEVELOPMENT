
<?php
require "db.php";

$users = [
    [
        "admin",
        "admin@example.com",
        "Admin@123",
        "admin"
    ],
    [
        "student",
        "student@example.com",
        "Student@123",
        "student"
    ]
];

try {
    $check = $pdo->prepare(
        "SELECT id FROM users WHERE username = ? OR email = ?"
    );

    foreach ($users as $user) {
        $check->execute([$user[0], $user[1]]);

        if ($check->fetch()) {
            exit("A test account already exists. Setup stopped.");
        }
    }

    $insert = $pdo->prepare(
        "INSERT INTO users (username, email, password, role)
         VALUES (?, ?, ?, ?)"
    );

    foreach ($users as $user) {
        $insert->execute([
            $user[0],
            $user[1],
            password_hash($user[2], PASSWORD_DEFAULT),
            $user[3]
        ]);
    }

    echo "Test users created successfully. Delete setup_admin.php now.";
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo "Setup failed. Check the database configuration.";
}
?>
