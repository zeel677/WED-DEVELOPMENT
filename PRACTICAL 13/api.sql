
<?php
session_start();
require "db.php";

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store");

function respond(int $status, array $data): never {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function readInput(): array {
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        respond(400, ["success" => false, "message" => "Invalid JSON body."]);
    }

    return $data;
}

function verifyCsrf(): void {
    $sent = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";
    $stored = $_SESSION["csrf"] ?? "";

    if ($stored === "" || !hash_equals($stored, $sent)) {
        respond(403, ["success" => false, "message" => "Invalid security token. Refresh the page."]);
    }
}

function validateStudent(array $data): array {
    $name = trim((string)($data["name"] ?? ""));
    $email = trim((string)($data["email"] ?? ""));
    $course = trim((string)($data["course"] ?? ""));

    if (
        $name === "" || strlen($name) > 100 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 120 ||
        $course === "" || strlen($course) > 80
    ) {
        respond(422, [
            "success" => false,
            "message" => "Enter a valid name, email, and course."
        ]);
    }

    return [$name, $email, $course];
}

$method = $_SERVER["REQUEST_METHOD"];

try {
    if ($method === "GET") {
        $q = trim($_GET["q"] ?? "");
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

        if (isset($_GET["id"])) {
            if (!$id || $id < 1) {
                respond(400, ["success" => false, "message" => "Invalid student ID."]);
            }

            $stmt = $conn->prepare(
                "SELECT id, name, email, course
                 FROM students WHERE id = ?"
            );
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $student = $stmt->get_result()->fetch_assoc();

            if (!$student) {
                respond(404, ["success" => false, "message" => "Student not found."]);
            }

            respond(200, ["success" => true, "student" => $student]);
        }

        $like = "%$q%";
        $stmt = $conn->prepare(
            "SELECT id, name, email, course
             FROM students
             WHERE name LIKE ? OR email LIKE ? OR course LIKE ?
             ORDER BY id DESC"
        );
        $stmt->bind_param("sss", $like, $like, $like);
        $stmt->execute();

        respond(200, [
            "success" => true,
            "students" => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)
        ]);
    }

    if (!in_array($method, ["POST", "PUT", "DELETE"], true)) {
        header("Allow: GET, POST, PUT, DELETE");
        respond(405, ["success" => false, "message" => "Method not allowed."]);
    }

    verifyCsrf();
    $data = readInput();

    if ($method === "POST") {
        [$name, $email, $course] = validateStudent($data);

        $stmt = $conn->prepare(
            "INSERT INTO students (name, email, course) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $name, $email, $course);
        $stmt->execute();

        respond(201, [
            "success" => true,
            "message" => "Student added successfully.",
            "id" => $conn->insert_id
        ]);
    }

    $id = filter_var($data["id"] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        respond(400, ["success" => false, "message" => "Valid student ID required."]);
    }

    if ($method === "PUT") {
        [$name, $email, $course] = validateStudent($data);

        $stmt = $conn->prepare(
            "UPDATE students SET name = ?, email = ?, course = ? WHERE id = ?"
        );
        $stmt->bind_param("sssi", $name, $email, $course, $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            $check = $conn->prepare("SELECT id FROM students WHERE id = ?");
            $check->bind_param("i", $id);
            $check->execute();

            if (!$check->get_result()->fetch_assoc()) {
                respond(404, ["success" => false, "message" => "Student not found."]);
            }
        }

        respond(200, ["success" => true, "message" => "Student updated successfully."]);
    }

    if ($method === "DELETE") {
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows !== 1) {
            respond(404, ["success" => false, "message" => "Student not found."]);
        }

        respond(200, ["success" => true, "message" => "Student deleted successfully."]);
    }
} catch (mysqli_sql_exception $e) {
    if ((int)$e->getCode() === 1062) {
        respond(409, ["success" => false, "message" => "That email already exists."]);
    }

    error_log($e->getMessage());
    respond(500, ["success" => false, "message" => "A database error occurred."]);
} catch (Throwable $e) {
    error_log($e->getMessage());
    respond(500, ["success" => false, "message" => "An unexpected error occurred."]);
}
?>
