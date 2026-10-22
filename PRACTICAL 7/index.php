<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 30px;
        }
        .container {
            max-width: 450px;
            margin: auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 12px #ccc;
        }
        h2 {
            text-align: center;
            color: #1e3a8a;
        }
        label {
            display: block;
            margin-top: 14px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Student Registration</h2>
    <form action="process.php" method="POST">
        <label for="name">Student Name</label>
        <input type="text" id="name" name="name"
               maxlength="80" required>
        <label for="roll">Roll Number</label>
        <input type="text" id="roll" name="roll"
               maxlength="20" required>
        <label for="email">Email</label>
        <input type="email" id="email" name="email"
               maxlength="254" required>
        <label for="course">Course</label>
        <select id="course" name="course" required>
            <option value="">Select Course</option>
            <option value="B.Tech IT">B.Tech IT</option>
            <option value="B.Tech CSE">B.Tech CSE</option>
            <option value="BCA">BCA</option>
            <option value="MCA">MCA</option>
        </select>

        <button type="submit">Register Student</button>
    </form>

    <p style="text-align:center">
        <a href="view.php">View Registered Students</a>
    </p>
    </div>
</body>
</html>
