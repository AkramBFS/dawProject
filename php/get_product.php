<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception("Product ID is required");
    }

    $productId = (int)$_GET['id'];
    
    // Get product details
    $productQuery = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($productQuery);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $productResult = $stmt->get_result();
    
    if ($productResult->num_rows === 0) {
        throw new Exception("Product not found");
    }
    
    $product = $productResult->fetch_assoc();
    
    // Get product variants
    $variantQuery = "SELECT * FROM product_variants WHERE product_id = ?";
    $stmt = $conn->prepare($variantQuery);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $variantResult = $stmt->get_result();
    
    $variants = [];
    $totalStock = 0;
    while ($variant = $variantResult->fetch_assoc()) {
        $variants[] = $variant;
        $totalStock += $variant['stock'];
    }
    
    // Add variants and stock to product data
    $product['variants'] = $variants;
    $product['total_stock'] = $totalStock;
    $product['price'] = (float)$product['price'];
    
    echo json_encode([
        'success' => true,
        'data' => $product
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?>