
<?php
require "db.php";

header("Content-Type: text/csv; charset=UTF-8");
header('Content-Disposition: attachment; filename="students.csv"');

$output = fopen("php://output", "w");

// UTF-8 BOM helps spreadsheet programs display UTF-8 correctly.
fwrite($output, "\xEF\xBB\xBF");

fputcsv($output, ["ID", "Name", "Email", "Course", "Phone"]);

$result = $conn->query(
    "SELECT id, name, email, course, phone
     FROM students
     WHERE status = 'active'
     ORDER BY id"
);

function csvSafe($value) {
    $value = (string)$value;

    if (preg_match('/^[\s]*[=+\-@]/u', $value)) {
        return "'" . $value;
    }

    return $value;
}

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row["id"],
        csvSafe($row["name"]),
        csvSafe($row["email"]),
        csvSafe($row["course"]),
        csvSafe($row["phone"])
    ]);
}

fclose($output);
exit;
?>
