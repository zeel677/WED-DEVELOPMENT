
<?php
function createRememberToken(PDO $pdo, int $userId): void {
    $selector = bin2hex(random_bytes(12));
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash("sha256", $token);

    $expires = time() + (30 * 24 * 60 * 60);

    $stmt = $pdo->prepare(
        "INSERT INTO remember_tokens
         (user_id, selector, token_hash, expires_at)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([
        $userId,
        $selector,
        $tokenHash,
        date("Y-m-d H:i:s", $expires)
    ]);

    setcookie("remember_me", $selector . ":" . $token, [
        "expires" => $expires,
        "path" => "/25IT076(PHP)/Practical10/",
        "secure" => !empty($_SERVER["HTTPS"])
            && $_SERVER["HTTPS"] !== "off",
        "httponly" => true,
        "samesite" => "Lax"
    ]);
}

function restoreRememberedLogin(PDO $pdo): bool {
    $value = $_COOKIE["remember_me"] ?? "";

    if (!preg_match('/^([a-f0-9]{24}):([a-f0-9]{64})$/', $value, $m)) {
        return false;
    }

    $selector = $m[1];
    $token = $m[2];

    $stmt = $pdo->prepare(
        "SELECT rt.*, u.username, u.role
         FROM remember_tokens rt
         JOIN users u ON u.id = rt.user_id
         WHERE rt.selector = ? AND rt.expires_at > NOW()
         LIMIT 1"
    );
    $stmt->execute([$selector]);
    $row = $stmt->fetch();

    if (!$row || !hash_equals($row["token_hash"], hash("sha256", $token))) {
        return false;
    }

    // Remove the old token so it cannot be reused.
    $delete = $pdo->prepare(
        "DELETE FROM remember_tokens WHERE id = ?"
    );
    $delete->execute([$row["id"]]);

    session_regenerate_id(true);

    $_SESSION["user_id"] = (int)$row["user_id"];
    $_SESSION["username"] = $row["username"];
    $_SESSION["role"] = $row["role"];
    $_SESSION["last_activity"] = time();

    // Rotate the token.
    createRememberToken($pdo, (int)$row["user_id"]);

    return true;
}

function clearRememberToken(PDO $pdo): void {
    $value = $_COOKIE["remember_me"] ?? "";

    if (preg_match('/^([a-f0-9]{24}):([a-f0-9]{64})$/', $value, $m)) {
        $stmt = $pdo->prepare(
            "DELETE FROM remember_tokens WHERE selector = ?"
        );
        $stmt->execute([$m[1]]);
    }

    setcookie("remember_me", "", [
        "expires" => time() - 3600,
        "path" => "/25IT076(PHP)/Practical10/",
        "secure" => !empty($_SERVER["HTTPS"])
            && $_SERVER["HTTPS"] !== "off",
        "httponly" => true,
        "samesite" => "Lax"
    ]);
}
?>
