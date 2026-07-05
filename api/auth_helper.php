<?php
/**
 * auth_helper.php
 * Small helper for issuing and validating real session tokens.
 *
 * The previous login.php returned "MOCK_SESSION_TOKEN_..." — a random string
 * that was never stored anywhere, so no endpoint could ever verify it later.
 * That means every "authenticated" request after login had no real auth check.
 *
 * This reuses the existing `remember_token` column on `users` (no schema change
 * needed): we store a HASH of the token server-side and hand the RAW token to
 * the client. The client sends the raw token back as:
 *   Authorization: Bearer <token>
 * and any protected endpoint looks up the user by hashing the incoming token
 * and matching it against `remember_token`.
 */

function issue_auth_token(mysqli $conn, int $userId): string {
    $rawToken = bin2hex(random_bytes(32));           // sent to the client
    $tokenHash = hash('sha256', $rawToken);          // stored in DB

    $stmt = $conn->prepare("UPDATE users SET remember_token = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $tokenHash, $userId);
    $stmt->execute();
    $stmt->close();

    return $rawToken;
}

/**
 * Looks up the current user from an Authorization: Bearer <token> header.
 * Returns the user row (assoc array) or null if invalid/missing.
 */
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

    $stmt = $conn->prepare("SELECT id, name, phone, email, status FROM users WHERE remember_token = ? LIMIT 1");
    $stmt->bind_param("s", $tokenHash);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc() ?: null;
    $stmt->close();

    if ($user && $user['status'] !== 'ACTIVE') {
        return null;
    }

    return $user;
}
