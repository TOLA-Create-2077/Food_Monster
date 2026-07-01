<?php
// កំណត់ Headers ឱ្យបានត្រឹមត្រូវសម្រាប់សាធារណៈ (Public)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ហៅហ្វាយល៍ config.php ចុងក្រោយដែលយើងបានជួសជុលរួចរាល់
require_once __DIR__ . '/config.php';

try {
    // 🛠️ ដំណោះស្រាយ៖ បង្ខំឱ្យអថេរ $conn ស្គាល់ទម្រង់តភ្ជាប់ ទោះបីជាចេញពី PDO ឬ mysqli ក៏ដោយ
    if (!isset($conn) || $conn === null) {
        if (isset($pdo) && $pdo !== null) {
            $conn = $pdo;
        } else {
            throw new Exception("Database connection variables ($conn and $pdo) are both null.");
        }
    }

    // 🛠️ FIXED SQL: កែសម្រួល GROUP BY ឱ្យត្រូវតាមស្តង់ដារ Strict Mode របស់ Aiven Cloud MySQL
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
    $rows = [];

    // 🛠️ ដំណើរការ Query ទៅតាម Driver ដែលមានស្រាប់ដោយសុវត្ថិភាព
    if ($conn instanceof PDO) {
        $stmt = $conn->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $result = $conn->query($sql);
        if ($result) {
            while ($r = $result->fetch_assoc()) {
                $rows[] = $r;
            }
            $result->close();
        }
    }

    foreach ($rows as $row) {
        // 🛠️ ដំណោះស្រាយ៖ បំបែក (Decode) ទិន្នន័យ JSON ភាសាខ្មែរ/អង់គ្លេស ឱ្យទៅជាអក្សរធម្មតា
        $title = 'Unnamed Item';
        if (!empty($row['title'])) {
            $titleArr = json_decode($row['title'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($titleArr)) {
                $title = $titleArr['km'] ?? $titleArr['en'] ?? $row['title'];
            } else {
                $title = $row['title'];
            }
        }

        $description = '';
        if (!empty($row['description'])) {
            $descArr = json_decode($row['description'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($descArr)) {
                $description = $descArr['km'] ?? $descArr['en'] ?? $row['description'];
            } else {
                $description = $row['description'];
            }
        }

        // 🛠️ ដំណោះស្រាយរូបភាព៖ បំពេញ Domain របស់ DigitalOcean Space ឱ្យបានត្រឹមត្រូវ
        $image = "";
        if (!empty($row['variate_image'])) {
            $image = $row['variate_image'];
        } elseif (!empty($row['item_image'])) {
            $image = $row['item_image'];
        }

        if (empty($image)) {
            $image = "https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600";
        } elseif (!filter_var($image, FILTER_VALIDATE_URL)) {
            $image = "https://foodmonster-assets.sgp1.digitaloceanspaces.com/uploads/item/" . $image;
        }

        $price = (float)($row['price'] ?? 0);
        if ($price == 0) {
            $price = 3.50; // តម្លៃចាស់បម្រុងទុក (Fallback Price)
        }

        $data[] = [
            'id' => (int)$row['id'],
            'title' => $title, 
            'description' => $description,
            'image' => $image,
            'price' => $price,
            'type' => 'Fast Food',
            'branch_id' => 1
        ];
    }

    // 🚀 បោះទិន្នន័យចេញជា JSON ស្អាតស្អំ
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    // ទោះបីជាខុសក៏មិនឱ្យចេញផ្ទាំង 500 របស់ Server ដែរ គឺចេញជាសារ JSON ប្រាប់ចំៗតែម្តង
    http_response_code(200); 
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}