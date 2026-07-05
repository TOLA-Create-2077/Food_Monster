<?php
/**
 * get_branches.php
 * Returns available store branches with support for structural fallback configurations.
 */
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/config.php';

$sql = "SELECT id, title, description, image, cover, phone FROM branches WHERE status = 'ACTIVE' ORDER BY id ASC";
$result = $conn->query($sql);

$data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "id" => (int)$row['id'],
            "title" => $row['title'] ?? "",
            "description" => $row['description'] ?? "",
            "image" => $row['image'] ?? "",
            "cover" => $row['cover'] ?? "",
            "phone" => $row['phone'] ?? ""
        ];
    }
}

echo json_encode([
    "success" => true,
    "message" => "Branches retrieved successfully",
    "data" => $data
]);