```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure User Registration</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 30px;
        }

        .container {
            max-width: 420px;
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
            margin-top: 15px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 11px;
            margin-top: 5px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            margin-top: 22px;
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

        .note {
            color: #555;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Create Account</h2>

    <form action="register.php" method="POST">
        <label for="username">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            minlength="3"
            maxlength="30"
            pattern="[A-Za-z0-9_]+"
            title="Use letters, numbers, and underscores only."
            required
        >

        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            maxlength="254"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            maxlength="72"
            required
        >

        <p class="note">
            Use at least 8 characters. Maximum 72 UTF-8 bytes.
        </p>

        <button type="submit">Register</button>
    </form>
</div>

</body>
</html>
```