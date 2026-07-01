<?php
// កំណត់ Headers ឱ្យបានត្រឹមត្រូវសម្រាប់សាធារណៈ (Public)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/config.php';

try {
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
        // 🛠️ ដំណោះស្រាយ៖ បំបែក (Decode) ទិន្នន័យ JSON ភាសាខ្មែរ/អង់គ្លេស ឱ្យទៅជាអក្សរធម្មតាសម្រាប់ Android App អានដាច់
        $titleArr = json_decode($row['title'], true);
        $title = isset($titleArr['km']) ? $titleArr['km'] : (isset($titleArr['en']) ? $titleArr['en'] : ($row['title'] ?? 'Unnamed Item'));

        $descArr = json_decode($row['description'], true);
        $description = isset($descArr['km']) ? $descArr['km'] : (isset($descArr['en']) ? $descArr['en'] : ($row['description'] ?? ''));

        // 🛠️ ដំណោះស្រាយរូបភាព៖ បំពេញ Domain ឱ្យគ្រប់គ្រាន់ បើរូបភាពនោះជាឈ្មោះហ្វាយល៍ធម្មតា
        $image = "";
        if (!empty($row['variate_image'])) {
            $image = $row['variate_image'];
        } elseif (!empty($row['item_image'])) {
            $image = $row['item_image'];
        }

        if (empty($image)) {
            $image = "https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600";
        } elseif (!filter_var($image, FILTER_VALIDATE_URL)) {
            // បើគ្មាន http/https ទេ គឺវាជាហ្វាយល៍ក្នុង DigitalOcean របស់បង
            $image = "https://foodmonster-assets.sgp1.digitaloceanspaces.com/uploads/item/" . $image;
        }

        $price = (float)($row['price'] ?? 0);
        if ($price == 0) {
            $price = 3.50;
        }

        $data[] = [
            'id' => (int)$row['id'],
            'title' => $title, // ចេញជាអក្សរខ្មែរ ឬអង់គ្លេសស្អាតតែម្តង
            'description' => $description,
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