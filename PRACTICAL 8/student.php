<?php
require_once "db.php";

$sql = "SELECT
            s.student_id,
            s.name,
            s.email,
            s.course,
            e.event_name
        FROM students s
        LEFT JOIN registrations r
            ON s.student_id = r.student_id
        LEFT JOIN events e
            ON r.event_id = e.event_id
        ORDER BY s.student_id";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$records = $stmt->fetchAll();

function escape($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StudentHub Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 25px;
            background: #f1f5f9;
        }

        h2 { color: #1e3a8a; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #2563eb;
            color: white;
        }

        tr:nth-child(even) { background: #f8fafc; }
    </style>
</head>
<body>

<h2>StudentHub: Student Registrations</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Course</th>
            <th>Registered Event</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($records as $row): ?>
        <tr>
            <td><?= escape($row["student_id"]) ?></td>
            <td><?= escape($row["name"]) ?></td>
            <td><?= escape($row["email"]) ?></td>
            <td><?= escape($row["course"]) ?></td>
            <td><?= escape($row["event_name"] ?? "No registration") ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>