<?php
require_once 'db.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Get the raw POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!$input) {
        throw new Exception("Invalid input data");
    }

    // Required fields validation
    $requiredFields = ['shipping_address', 'total', 'items'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Validate items array
    if (!is_array($input['items']) || count($input['items']) === 0) {
        throw new Exception("Invalid or empty items list");
    }

    // Validate each item
    foreach ($input['items'] as $item) {
        if (!isset($item['product_variant_id'])) {
            throw new Exception("Missing product_variant_id in item");
        }
        if (!isset($item['quantity']) || $item['quantity'] <= 0) {
            throw new Exception("Invalid quantity in item");
        }
        if (!isset($item['price']) || $item['price'] <= 0) {
            throw new Exception("Invalid price in item");
        }
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // For demo purposes, we'll use a hardcoded user_id
        // In a real app, you would get this from the session
        $user_id = 1; // Replace with actual user ID from session
        
        // Create order
        $orderQuery = "INSERT INTO orders (user_id, total_amount, shipping_address, status) 
                      VALUES (?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($orderQuery);
        $stmt->bind_param("ids", $user_id, $input['total'], $input['shipping_address']);
        $stmt->execute();
        $orderId = $conn->insert_id;

        // Add order items
        foreach ($input['items'] as $item) {
            // Check stock first
            $stockCheck = "SELECT stock FROM product_variants WHERE id = ?";
            $stmt = $conn->prepare($stockCheck);
            $stmt->bind_param("i", $item['product_variant_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $variant = $result->fetch_assoc();

            if (!$variant || $variant['stock'] < $item['quantity']) {
                throw new Exception("Insufficient stock for item with variant ID: " . $item['product_variant_id']);
            }

            // Add to order items
            $itemQuery = "INSERT INTO order_items (order_id, product_variant_id, quantity, price)
                         VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($itemQuery);
            $stmt->bind_param("iiid", $orderId, $item['product_variant_id'], $item['quantity'], $item['price']);
            $stmt->execute();

            // Update stock
            $updateStock = "UPDATE product_variants SET stock = stock - ? WHERE id = ?";
            $stmt = $conn->prepare($updateStock);
            $stmt->bind_param("ii", $item['quantity'], $item['product_variant_id']);
            $stmt->execute();
        }

        $conn->commit();

        // Return success response
        echo json_encode([
            'success' => true,
            'order_id' => $orderId,
            'message' => 'Order created successfully'
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    // Log the error
    error_log("Payment processing error: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?>