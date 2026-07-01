<?php
// កំណត់ Headers ឱ្យបានត្រឹមត្រូវ (លុបផ្នែកជាន់គ្នាចេញ)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// ទាញយកការភ្ជាប់ទៅកាន់ Database ពី config.php (ឬ config_pdo.php ផ្អែកលើគម្រោងបង)
require_once __DIR__ . '/config.php';

try {
    // ត្រូវប្រាកដថាអថេរភ្ជាប់ Database ឈ្មោះ $conn ឬ $pdo មានដំណើរការ
    // ប្រសិនបើក្នុង config.php បងប្រើ $pdo សូមប្តូរកូដខាងក្រោមពី $conn ទៅ $pdo
    if (!isset($conn) && isset($pdo)) {
        $conn = $pdo;
    }

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

    $data = [];

    // 🛠️ ដំណោះស្រាយ៖ ការពារទាំងការប្រើប្រាស់ PDO និង MySQLi កុំឱ្យគាំងដាច់ខាត
    if ($conn instanceof PDO) {
        $stmt = $conn->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $result = $conn->query($sql);
        $rows = [];
        if ($result) {
            while ($r = $result->fetch_assoc()) {
                $rows[] = $r;
            }
            $result->close();
        }
    }

    foreach ($rows as $row) {
        // ត្រួតពិនិត្យរូបភាព៖ បើនៅក្នុង item_variates គ្មានរូបភាព ត្រូវយករូបភាពដើមពី items
        $image = "";
        if (!empty($row['variate_image'])) {
            $image = $row['variate_image'];
        } elseif (!empty($row['item_image'])) {
            $image = $row['item_image'];
        } else {
            $image = "https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600";
        }

        // ត្រួតពិនិត្យតម្លៃលុយ៖ ប្រសិនបើទាញបានតម្លៃ 0 ឬ NULL ត្រូវផ្តល់តម្លៃបម្រុង
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

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}