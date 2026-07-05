<?php
/**
 * auth_helper.php
 * Session Authentication Engine: Issues and cryptographically matches secure authorization vectors.
 */

function issue_auth_token(mysqli $conn, int $userId): string {
    $rawToken = bin2hex(random_bytes(32)); 
    $tokenHash = hash('sha256', $rawToken); 

    $stmt = $conn->prepare("UPDATE users SET remember_token = ?, updated_at = NOW() WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $tokenHash, $userId);
        $stmt->execute();
        $stmt->close();
    }
    return $rawToken;
}

function get_authenticated_user(mysqli $conn): ?array {
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');

    if (!$authHeader || stripos($authHeader, 'Bearer ') !== 0) {
        return null;
    }

    $rawToken = trim(substr($authHeader, 7));
    if ($rawToken === '') {
        return null;
    }

    $tokenHash = hash('sha256', $rawToken);

    $stmt = $conn->prepare("SELECT id, name, phone, email, status FROM users WHERE remember_token = ? AND status = 'ACTIVE' LIMIT 1");
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("s", $tokenHash);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    return $user ?: null;
}