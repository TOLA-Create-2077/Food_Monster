<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Credentials now come from config.php (env vars), not hardcoded here.
require_once __DIR__ . '/config.php';

$sql = "
SELECT 
    id,
    title,
    description,
    image,
    cover,
    phone,
    email,
    telegram,
    opening_hour,
    close_hour,
    status
FROM branches
WHERE status = 'ACTIVE'
ORDER BY id ASC
";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Query failed."
    ]);
    exit;
}

$data = [];

while ($row = $result->fetch_assoc()) {
    $titleEn = "";
    $titleKm = "";

    $decodedTitle = json_decode($row['title'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedTitle)) {
        $titleEn = $decodedTitle['en'] ?? "";
        $titleKm = $decodedTitle['km'] ?? "";
    }

    $descriptionEn = "";
    $descriptionKm = "";
    $decodedDescription = json_decode($row['description'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedDescription)) {
        $descriptionEn = $decodedDescription['en'] ?? "";
        $descriptionKm = $decodedDescription['km'] ?? "";
    }

    $data[] = [
        "id" => (int)$row['id'],
        "title_en" => $titleEn,
        "title_km" => $titleKm,
        "description_en" => $descriptionEn,
        "description_km" => $descriptionKm,
        "image" => $row['image'] ?? "",
        "cover" => $row['cover'] ?? "",
        "phone" => $row['phone'] ?? "",
        "email" => $row['email'] ?? "",
        "telegram" => $row['telegram'] ?? "",
        "opening_hour" => $row['opening_hour'] ?? "",
        "close_hour" => $row['close_hour'] ?? "",
        "status" => $row['status'] ?? ""
    ];
}

echo json_encode([
    "success" => true,
    "branches" => $data
], JSON_PRETTY_PRINT);

$conn->close();
