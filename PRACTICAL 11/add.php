
<?php
require "db.php";
session_start();

if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

function e($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8");
}

$name = $email = $course = $phone = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals($_SESSION["csrf"], $_POST["csrf"] ?? "")) {
        http_response_code(403);
        exit("Invalid request token. Please reload the form.");
    }

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if ($name === "" || $course === "" || $phone === ""
        || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter valid details in all fields.";
    } else {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO students (name, email, course, phone)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $name, $email, $course, $phone);
            $stmt->execute();

            header("Location: index.php?msg=added");
            exit;
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() === 1062) {
                $error = "This email address already exists.";
            } else {
                error_log($ex->getMessage());
                $error = "Could not add the student.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Student</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Add Student</h1>

<?php if ($error !== ""): ?>
    <p class="error"><?= e($error) ?></p>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>">

    <label>Name</label>
    <input name="name" maxlength="100" required value="<?= e($name) ?>">

    <label>Email</label>
    <input type="email" name="email" maxlength="120"
           required value="<?= e($email) ?>">

    <label>Course</label>
    <input name="course" maxlength="80" required value="<?= e($course) ?>">

    <label>Phone</label>
    <input name="phone" maxlength="20" required value="<?= e($phone) ?>">

    <button type="submit">Save Student</button>
    <a class="button secondary" href="index.php">Cancel</a>
</form>
</div>
</body>
</html>
