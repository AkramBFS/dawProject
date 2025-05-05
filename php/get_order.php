<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Order ID is required');
    }

    $order_id = $conn->real_escape_string($_GET['id']);
    
    // Get order details
    $order_query = "SELECT o.id, o.total_amount, o.shipping_address, o.status, o.created_at
                   FROM orders o
                   WHERE o.id = ?";
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order_result = $stmt->get_result();
    
    if (!$order_result) {
        throw new Exception('Database error: ' . $conn->error);
    }
    
    if ($order_result->num_rows === 0) {
        throw new Exception('Order not found');
    }
    
    $order = $order_result->fetch_assoc();
    
    // Get order items with product information
    $items_query = "SELECT p.name, pv.id as variant_id, oi.quantity, oi.price
                   FROM order_items oi
                   JOIN product_variants pv ON oi.product_variant_id = pv.id
                   JOIN products p ON pv.product_id = p.id
                   WHERE oi.order_id = ?";
    $stmt = $conn->prepare($items_query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items_result = $stmt->get_result();
    
    if (!$items_result) {
        throw new Exception('Database error while fetching items: ' . $conn->error);
    }
    
    $items = [];
    while ($item = $items_result->fetch_assoc()) {
        $items[] = $item;
    }
    
    $order['items'] = $items;
    
    echo json_encode([
        'success' => true,
        'order_id' => $order['id'],
        'total_amount' => $order['total_amount'],
        'shipping_address' => $order['shipping_address'],
        'status' => $order['status'],
        'created_at' => $order['created_at'],
        'items' => $items
    ]);

} catch (Exception $e) {
    error_log("Order fetch error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?>