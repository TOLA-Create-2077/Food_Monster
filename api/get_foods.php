<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Credentials now come from config.php (env vars), not hardcoded here.
require_once __DIR__ . '/config.php';

try {
    $sql = "
        SELECT 
            i.id,
            i.title,
            i.description,
            i.image AS item_image,
            MIN(iv.image) AS variate_image,
            MIN(pv.price) AS price
        FROM items i
        LEFT JOIN item_variates iv ON iv.item_id = i.id AND (iv.status = 'ACTIVE' OR iv.status IS NULL)
        LEFT JOIN product_variates pv ON pv.item_variate_id = iv.id AND (pv.is_available = 1 OR pv.is_available IS NULL OR pv.is_available = 0)
        WHERE i.status = 'ACTIVE' OR i.status IS NULL OR i.status = '1'
        GROUP BY i.id, i.title, i.description, i.image
        ORDER BY i.id
    ";

    $result = $conn->query($sql);
    $data = [];

    while ($row = $result->fetch_assoc()) {

        // ត្រួតពិនិត្យរូបភាព៖ បើនៅក្នុង item_variates គ្មានរូបភាព ត្រូវយករូបភាពដើមពី items
        $image = "";
        if (!empty($row['variate_image'])) {
            $image = $row['variate_image'];
        } elseif (!empty($row['item_image'])) {
            $image = $row['item_image'];
        } else {
            $image = "https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600";
        }

        // ត្រួតពិនិត្យតម្លៃលុយ៖ ប្រសិនបើទាញបានតម្លៃ 0 ឬ NULL ត្រូវផ្តល់តម្លៃបម្រុងដើម្បីកុំឱ្យខូច Layout App
        $price = (float)($row['price'] ?? 0);
        if ($price == 0) {
            $price = 3.50;
        }

        $data[] = [
            'id' => (int)$row['id'],
            'title' => $row['title'] ?? 'Unnamed Item',
            'description' => $row['description'] ?? '',
            'image' => $image,
            'price' => $price,
            'type' => 'Fast Food',
            'branch_id' => 1
        ];
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    $result->close();
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error."
    ]);
}
