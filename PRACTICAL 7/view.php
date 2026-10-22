<?php
$file = __DIR__ . "/students.csv";
$students = [];

if (is_file($file) && is_readable($file)) {
    $handle = fopen($file, "r");

    if ($handle !== false) {
        $header = fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === 4) {
                $students[] = $row;
            }
        }
        fclose($handle);
    }
}
function e($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Students</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 25px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
        h2 { text-align: center; color: #1e3a8a; }
        table {
            width: 100%;
            border-collapse: collapse;
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
        .table-wrapper { overflow-x: auto; }
        a { color: #2563eb; }
    </style>
</head>
<body>
<div class="container">
    <h2>Registered Students</h2>

    <p>
        <a href="index.php">Register Another Student</a>
    </p>
    <?php if (empty($students)): ?>
        <p>No student records found.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Roll Number</th>
                        <th>Email</th>
                        <th>Course</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <?php foreach ($student as $value): ?>
                                <td><?= e($value) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
