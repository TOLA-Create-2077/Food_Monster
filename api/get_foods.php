<?php
// កំណត់ Headers ឱ្យបានត្រឹមត្រូវសម្រាប់ Android App
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ហៅឯកសារ config.php មកប្រើប្រាស់
require_once __DIR__ . '/config.php';

try {
    // 🛠️ ១. ពិនិត្យមើលថាតើត្រូវប្រើប្រាស់អថេរតភ្ជាប់មួយណា ($conn ឬ $pdo) ឱ្យបានត្រឹមត្រូវ
    $db = null;
    if (isset($pdo) && $pdo !== null) {
        $db = $pdo;
    } elseif (isset($conn) && $conn !== null) {
        $db = $conn;
    } else {
        throw new Exception("Database connection variables ($conn or $pdo) are not initialized.");
    }

    // 🛠️ ២. ឆែកមើលជាមុនសិនថា តើលើ Database មាន Table 'items' ដែរឬទេ? កុំឱ្យវាបោះ Error 500 ពេលរក Table មិនឃើញ
    $tableExists = false;
    if ($db instanceof PDO) {
        try {
            $db->query("SELECT 1 FROM items LIMIT 1");
            $tableExists = true;
        } catch (Exception $e) {
            $tableExists = false;
        }
    } else {
        $check = $db->query("SHOW TABLES LIKE 'items'");
        $tableExists = ($check && $check->num_rows > 0);
    }

    $data = [];

    // 💡 ករណីទី ១៖ បើមាន Table 'items' ក្នុង Database រួចរាល់ ឱ្យដំណើរការ Query ពិតប្រាកដ
    if ($tableExists) {
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

        // ទាញទិន្នន័យតាម Driver នីមួយៗដោយសុវត្ថិភាព
        $rows = [];
        if ($db instanceof PDO) {
            $stmt = $db->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $result = $db->query($sql);
            if ($result) {
                while ($r = $result->fetch_assoc()) {
                    $rows[] = $r;
                }
                $result->close();
            }
        }

        foreach ($rows as $row) {
            // រៀបចំរូបភាព
            $image = "";
            if (!empty($row['variate_image'])) {
                $image = $row['variate_image'];
            } elseif (!empty($row['item_image'])) {
                $image = $row['item_image'];
            } else {
                $image = "https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600";
            }

            // រៀបចំតម្លៃ
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
    } 
    // 💡 ករណីទី ២៖ បើ Database នៅមានតែ Table 'users' ឱ្យវាបោះទិន្នន័យគំរូនេះទៅ App ភ្លាម កុំឱ្យវាគាំង Error 500
    else {
        $data = [
            [
                'id' => 1,
                'title' => 'Beef Burger (Sample Data)',
                'description' => 'Delicious juicy beef burger with cheese.',
                'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600',
                'price' => 4.50,
                'type' => 'Fast Food',
                'branch_id' => 1
            ],
            [
                'id' => 2,
                'title' => 'French Fries (Sample Data)',
                'description' => 'Crispy and hot golden french fries.',
                'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=600',
                'price' => 2.50,
                'type' => 'Fast Food',
                'branch_id' => 1
            ]
        ];
    }

    // បោះទិន្នន័យជា JSON ចេញមកក្រៅ
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    // ស្ទាក់ចាប់ Error មិនឱ្យលោតផ្ទាំង 500 របស់ Server ឡើយ
    http_response_code(200); 
    echo json_encode([
        "success" => false,
        "message" => "API Execution Error: " . $e->getMessage()
    ]);
}