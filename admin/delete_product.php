<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

require_once '../php/db.php';
$conn = connect_to_db();

if (isset($_GET['id'])) {
    $product_id = $conn->real_escape_string($_GET['id']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete product variants first due to foreign key constraint
        $delete_variants = "DELETE FROM product_variants WHERE product_id = '$product_id'";
        if ($conn->query($delete_variants)) {
            // Then delete the product
            $delete_product = "DELETE FROM products WHERE id = '$product_id'";
            if ($conn->query($delete_product)) {
                $conn->commit();
                header('Location: products.php');
                exit();
            } else {
                throw new Exception("Error deleting product");
            }
        } else {
            throw new Exception("Error deleting product variants");
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    header('Location: products.php');
    exit();
}
?>