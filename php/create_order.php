<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!$input || !isset($input['user_id'])) {
        throw new Exception("Invalid input data");
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Create order
        $orderQuery = "INSERT INTO orders (user_id, total_amount, shipping_address, status) 
                      VALUES (?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($orderQuery);
        $stmt->bind_param("ids", $input['user_id'], $input['total_amount'], $input['address']);
        $stmt->execute();
        $orderId = $conn->insert_id;

        // Add order items
        foreach ($input['items'] as $item) {
            // Check stock first
            $stockCheck = "SELECT stock FROM product_variants WHERE id = ?";
            $stmt = $conn->prepare($stockCheck);
            $stmt->bind_param("i", $item['variant_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $variant = $result->fetch_assoc();

            if (!$variant || $variant['stock'] < $item['quantity']) {
                throw new Exception("Insufficient stock for item: " . $item['name']);
            }

            // Add to order items
            $itemQuery = "INSERT INTO order_items (order_id, product_variant_id, quantity, price)
                         VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($itemQuery);
            $stmt->bind_param("iiid", $orderId, $item['variant_id'], $item['quantity'], $item['price']);
            $stmt->execute();

            // Update stock
            $updateStock = "UPDATE product_variants SET stock = stock - ? WHERE id = ?";
            $stmt = $conn->prepare($updateStock);
            $stmt->bind_param("ii", $item['quantity'], $item['variant_id']);
            $stmt->execute();
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'order_id' => $orderId
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?>