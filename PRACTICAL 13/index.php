
<?php
session_start();

if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION["csrf"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Management - Fetch API</title>
    <link rel="stylesheet" href="style.css">
    <script>
        window.API_CSRF = <?= json_encode($csrf) ?>;
    </script>
    <script src="app.js" defer></script>
</head>
<body>
<div class="container">
    <h1>Student Management System</h1>
    <p>PHP JSON API + JavaScript Fetch API</p>

    <div id="message" role="status" aria-live="polite"></div>

    <section class="form-card">
        <h2 id="formTitle">Add Student</h2>

        <form id="studentForm">
            <input type="hidden" id="studentId">

            <label for="name">Student Name</label>
            <input id="name" maxlength="100" required>

            <label for="email">Email</label>
            <input id="email" type="email" maxlength="120" required>

            <label for="course">Course</label>
            <input id="course" maxlength="80" required>

            <button type="submit" id="saveBtn">Add Student</button>
            <button type="button" id="cancelBtn" class="secondary" hidden>
                Cancel Edit
            </button>
        </form>
    </section>

    <section>
        <h2>Student Records</h2>

        <label for="search">Search by name, email, or course</label>
        <input id="search" type="search"
               placeholder="Start typing to search...">

        <p id="loading" hidden>Loading...</p>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="studentTable">
                    <tr><td colspan="5">Loading students...</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</div>
</body>
</html>
