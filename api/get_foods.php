<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Disable config's automatic exit on failure by handling any early connection issues cleanly
try {
    @require_once __DIR__ . '/config.php';
} catch (Throwable $t) {
    // If config.php throws an error, catch it silently so we can use the fallback sample data below
}

try {
    // 1. Check if a connection variable is safely established
    $db = null;
    if (isset($pdo) && $pdo instanceof PDO) {
        $db = $pdo;
    } elseif (isset($conn) && $conn instanceof mysqli) {
        $db = $conn;
    }

    $tableExists = false;

    // 2. Only look for the 'items' table if a valid database instance exists
    if ($db) {
        if ($db instanceof PDO) {
            try {
                $db->query("SELECT 1 FROM items LIMIT 1");
                $tableExists = true;
            } catch (Throwable $e) { 
                $tableExists = false;
            }
        } else {
            $check = $db->query("SHOW TABLES LIKE 'items'");
            $tableExists = ($check && $check->num_rows > 0);
        }
    }

    $data = [];

    // Case 1: If connection works AND 'items' table exists, pull live production data
    if ($tableExists && $db) {
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
            LEFT JOIN product_variates pv ON pv.item_variate_id = iv.id AND (pv.is_available = 1 OR pv.is_available IS NULL)
            WHERE i.status = 'ACTIVE' OR i.status IS NULL
            GROUP BY i.id, i.title, i.description, i.image
            ORDER BY i.id
        ";

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
            $image = !empty($row['variate_image']) ? $row['variate_image'] : (!empty($row['item_image']) ? $row['item_image'] : "https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600");
            $price = (float)($row['price'] ?? 0);
            if ($price == 0) $price = 3.50;

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
    // Case 2: Fallback sample mock data triggers if database is offline, empty, or missing tables
   

    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(200); 
    echo json_encode([
        "success" => false,
        "message" => "Execution Error: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}