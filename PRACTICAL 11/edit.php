
<?php
require "db.php";
session_start();

if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

function e($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit("Invalid student ID.");
}

$stmt = $conn->prepare(
    "SELECT name, email, course, phone
     FROM students WHERE id = ? AND status = 'active'"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    http_response_code(404);
    exit("Student not found.");
}

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
        || strlen($name) > 100
        || strlen($email) > 120
        || strlen($course) > 80
        || strlen($phone) > 20
        || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter valid details within the allowed lengths.";
        $student = compact("name", "email", "course", "phone");
    } else {
        try {
            $stmt = $conn->prepare(
                "UPDATE students
                 SET name = ?, email = ?, course = ?, phone = ?
                 WHERE id = ? AND status = 'active'"
            );
            $stmt->bind_param(
                "ssssi", $name, $email, $course, $phone, $id
            );
            $stmt->execute();

            header("Location: index.php?msg=updated");
            exit;
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() === 1062) {
                $error = "That email address is already used.";
            } else {
                error_log($ex->getMessage());
                $error = "Could not update the student.";
            }
            $student = compact("name", "email", "course", "phone");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Student</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Edit Student #<?= (int)$id ?></h1>

<?php if ($error !== ""): ?>
    <p class="error"><?= e($error) ?></p>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>">

    <label>Name</label>
    <input name="name" maxlength="100" required
           value="<?= e($student["name"]) ?>">

    <label>Email</label>
    <input type="email" name="email" maxlength="120" required
           value="<?= e($student["email"]) ?>">

    <label>Course</label>
    <input name="course" maxlength="80" required
           value="<?= e($student["course"]) ?>">

    <label>Phone</label>
    <input name="phone" maxlength="20" required
           value="<?= e($student["phone"]) ?>">

    <button type="submit">Update Student</button>
    <a class="button secondary" href="index.php">Cancel</a>
</form>
</div>
</body>
</html>
