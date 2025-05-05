<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    // Get products with their variants
    $query = "SELECT p.*, 
                     GROUP_CONCAT(DISTINCT pv.size ORDER BY pv.size) AS sizes,
                     SUM(pv.stock) AS total_stock
              FROM products p
              LEFT JOIN product_variants pv ON p.id = pv.product_id
              GROUP BY p.id";
    
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Database error: " . $conn->error);
    }

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $row['price'] = (float)$row['price'];
        $row['total_stock'] = (int)$row['total_stock'];
        $row['sizes'] = $row['sizes'] ? explode(',', $row['sizes']) : [];
        $row['image_path'] = $row['image_path'] ?? 'images/placeholder.jpg';
        $products[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $products
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?>