```php
<?php
require_once "db.php";

function showMessage($message, $success = false) {
    $color = $success ? "#15803d" : "#dc2626";

    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Registration Result</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f1f5f9;
                padding: 40px 20px;
            }
            .box {
                max-width: 450px;
                margin: auto;
                padding: 25px;
                background: white;
                border-radius: 10px;
                text-align: center;
            }
            h2 { color: $color; }
            a { color: #2563eb; }
        </style>
    </head>
    <body>
        <div class='box'>
            <h2>" . htmlspecialchars(
                $message, ENT_QUOTES, 'UTF-8'
            ) . "</h2>
            <a href='index.php'>Back to Registration</a>
        </div>
    </body>
    </html>";
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");
    showMessage("Please submit the registration form.");
    exit;
}

$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

/* Server-side validation */
if (!preg_match('/\A[A-Za-z0-9_]{3,30}\z/', $username)) {
    showMessage("Username must be 3–30 letters, numbers, or underscores.");
    exit;
}

if (
    strlen($email) > 254 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    showMessage("Please enter a valid email address.");
    exit;
}

if (
    !is_string($password) ||
    strlen($password) < 8 ||
    strlen($password) > 72
) {
    showMessage("Password must be at least 8 characters and at most 72 bytes.");
    exit;
}

try {
    /* Check for duplicate username or email */
    $check = $pdo->prepare(
        "SELECT id FROM users
         WHERE username = :username OR email = :email
         LIMIT 1"
    );

    $check->execute([
        "username" => $username,
        "email" => $email
    ]);

    if ($check->fetch()) {
        showMessage("Username or email is already registered.");
        exit;
    }

    /* Hash the password before saving */
    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    /* Insert user details securely */
    $insert = $pdo->prepare(
        "INSERT INTO users (username, email, password)
         VALUES (:username, :email, :password)"
    );

    $insert->execute([
        "username" => $username,
        "email" => $email,
        "password" => $hashedPassword
    ]);

    showMessage("Registration successful!", true);

} catch (PDOException $e) {
    error_log($e->getMessage());

    /* Handle a duplicate caused by a concurrent request */
    if ($e->getCode() === "23000") {
        showMessage("Username or email is already registered.");
    } else {
        http_response_code(500);
        showMessage("Registration could not be completed. Please try again.");
    }
}
?>
```