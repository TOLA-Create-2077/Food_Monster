<?php
header("Content-Type: application/json; charset=UTF-8");

function present(string $key): bool {
    $v = getenv($key);
    if ($v === false || $v === '') $v = $_ENV[$key] ?? null;
    return $v !== null && $v !== '';
}

echo json_encode([
    "DB_HOST_set" => present('DB_HOST'),
    "DB_PORT_set" => present('DB_PORT'),
    "DB_NAME_or_DATABASE_set" => present('DB_NAME') || present('DB_DATABASE'),
    "DB_USER_or_USERNAME_set" => present('DB_USER') || present('DB_USERNAME'),
    "DB_PASS_or_PASSWORD_set" => present('DB_PASS') || present('DB_PASSWORD'),
]);