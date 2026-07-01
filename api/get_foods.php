<?php
// កំណត់ Headers ឱ្យបានត្រឹមត្រូវសម្រាប់ Android App ទាញយកទិន្នន័យ
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 🛠️ ១. ហៅឯកសារ config.php ដែលមានស្រាប់ ដើម្បីយកការតភ្ជាប់ Database (លែងប្រើ localhost, root ក្នុងហ្វាយល៍នេះ)
require_once __DIR__ . '/config.php';

try {
    // ផ្ទៀងផ្ទាត់អថេរតភ្ជាប់ Database
    if (!isset($conn) || $conn === null) {
        if (isset($pdo) && $pdo !== null) {
            $conn = $pdo;
        } else {
            throw new Exception("Database connection config is missing.");
        }
    }

    // 🛠️ ២. ពិនិត្យមើលជាមុនសិនថា តើលើ Server មាន Table 'items' ដែរឬទេ? បើគ្មានទេ ឱ្យបង្កើតទិន្នន័យបណ្តោះអាសន្ន (Fallback) កុំឱ្យគាំង App
    $tableCheck = false;
    if ($conn instanceof PDO) {
        try {
            $conn->query("SELECT 1 FROM items LIMIT 1");
            $tableCheck = true;
        } catch (Exception $e) { $tableCheck = false; }
    } else {
        $check = $conn->query("SHOW TABLES LIKE 'items'");
        $tableCheck = ($check && $check->num_rows > 0);
    }

    $data = [];

    // 💡 ករណីទី ១៖ បើមាន Table 'items' ក្នុង Database ពេញលេញ ឱ្យទាញទិន្នន័យពិតប្រាកដមកបង្ហាញ
    if ($tableCheck) {
        $sql = "
            SELECT 
                i.id,
                i.title,
                i.description,
                COALESCE(MAX(iv.image), i.image) AS image,
                MIN(pv.price) AS price
            FROM items i
            LEFT JOIN item_variates iv ON iv.item_id = i.id AND iv.status = 'ACTIVE'
            LEFT JOIN product_variates pv ON pv.item_variate_id = iv.id AND pv.is_available = 1
            WHERE i.status = 'ACTIVE' OR i.status IS NULL
            GROUP BY i.id, i.title, i.description, i.image
            ORDER BY i.id
        ";

        $rows = [];
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
            // បំបែក JSON ឈ្មោះភាសាខ្មែរ/អង់គ្លេស បើមាន
            $title = $row['title'];
            $titleArr = json_decode($row['title'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($titleArr)) {
                $title = $titleArr['km'] ?? $titleArr['en'] ?? $row['title'];
            }

            $description = $row['description'] ?? '';
            $descArr = json_decode($row['description'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($descArr)) {
                $description = $descArr['km'] ?? $descArr['en'] ?? $row['description'];
            }

            // រៀបចំតំណភ្ជាប់រូបភាព
            $image = $row['image'] ?? '';
            if (!empty($image) && !filter_var($image, FILTER_VALIDATE_URL)) {
                $image = "https://foodmonster-assets.sgp1.digitaloceanspaces.com/uploads/item/" . $image;
            } elseif (empty($image)) {
                $image = "https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600";
            }

            $data[] = [
                'id' => (int)$row['id'],
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'price' => (float)($row['price'] ?? 3.50)
            ];
        }
    } 
    // 💡 ករណីទី ២៖ បើ Database នៅស្អាតមានតែ Table 'users' (បាត់ Table ម្ហូប) ឱ្យបោះទិន្នន័យគំរូនេះទៅ App ភ្លាម កុំឱ្យទូរស័ព្ទ Crash
    else {
        $data = [
            [
                'id' => 1,
                'title' => 'Beef Burger (Sample)',
                'description' => 'Delicious juicy beef burger with cheese.',
                'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600',
                'price' => 4.50
            ],
            [
                'id' => 2,
                'title' => 'French Fries (Sample)',
                'description' => 'Crispy and hot golden french fries.',
                'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=600',
                'price' => 2.50
            ]
        ];
    }

    // 🚀 បោះទិន្នន័យជា JSON ត្រឡប់ទៅឱ្យ Android App
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    // ការពារដាច់ខាតមិនឱ្យចេញផ្ទាំង 500 Server Error របស់ Railway ឡើយ គឺបោះជា JSON ប្រាប់វិញ
    http_response_code(200); 
    echo json_encode([
        "success" => false,
        "message" => "API Error: " . $e->getMessage()
    ]);
}
?>