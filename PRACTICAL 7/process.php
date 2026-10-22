<?php
session_start();
function showMessage($message, $success = false) {
    $color = $success ? "#15803d" : "#dc2626";
    $safeMessage = htmlspecialchars(
        $message,
        ENT_QUOTES,
        "UTF-8"
    );
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
                padding: 40px;
                text-align: center;
            }
            .box {
                background: white;
                max-width: 450px;
                margin: auto;
                padding: 25px;
                border-radius: 10px;
            }
            h2 { color: $color; }
            a { color: #2563eb; }
        </style>
    </head>
    <body>
        <div class='box'>
            <h2>$safeMessage</h2>
            <a href='index.php'>Back to Registration</a>
            <br><br>
            <a href='view.php'>View Registered Students</a>
        </div>
    </body>
    </html>";
}
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");
    showMessage("Invalid request method.");
    exit;
}
$name = trim($_POST["name"] ?? "");
$roll = trim($_POST["roll"] ?? "");
$email = trim($_POST["email"] ?? "");
$course = $_POST["course"] ?? "";
$allowedCourses = [
    "B.Tech IT",
    "B.Tech CSE",
    "BCA",
    "MCA"
];
if ($name === "" || strlen($name) > 80 ||
    !preg_match("/^[\p{L}\s.'-]+$/u", $name)) {
    showMessage("Please enter a valid student name.");
    exit;
}
if (!preg_match("/^[A-Za-z0-9_-]{1,20}$/", $roll)) {
    showMessage("Please enter a valid roll number.");
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($email) > 254) {
    showMessage("Please enter a valid email address.");
    exit;
    }
if (!in_array($course, $allowedCourses, true)) {
    showMessage("Please select a valid course.");
    exit;
}
$name = preg_replace("/[\r\n]+/", " ", $name);
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$file = __DIR__ . "/students.csv";
$handle = fopen($file, "c+");
if ($handle === false) {
    showMessage("Unable to open the storage file.");
    exit;
}
if (!flock($handle, LOCK_EX)) {
    fclose($handle);
    showMessage("Unable to lock the storage file.");
    exit;
}
clearstatcache(true, $file);
if (fstat($handle)["size"] === 0) {
    fputcsv($handle, ["Name", "Roll Number", "Email", "Course"]);
}
rewind($handle);
$existingRolls = [];
$duplicate = false;
while (($row = fgetcsv($handle)) !== false) {
    if (isset($row[1])) {
        $existingRolls[] = strtolower($row[1]);
    }
}
if (in_array(strtolower($roll), $existingRolls, true)) {
    $duplicate = true;
}
if ($duplicate) {
    flock($handle, LOCK_UN);
    fclose($handle);
    showMessage("This roll number is already registered.");
    exit;
}
fseek($handle, 0, SEEK_END);
$result = fputcsv($handle, [
    $name,
    $roll,
    $email,
    $course
]);
fflush($handle);
flock($handle, LOCK_UN);
fclose($handle);
if ($result === false) {
    showMessage("Unable to save the registration.");
    exit;
}
showMessage("Student registered successfully!", true);
?>
