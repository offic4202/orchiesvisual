<?php
/**
 * Single Product API Endpoint
 * Returns a single product by ID as JSON
 */

header('Content-Type: application/json');

require_once '../admin/includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid product ID'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, product_name, description, price, category, product_image, status, created_at FROM products WHERE id = ? AND status = 'active'");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        echo json_encode([
            'success' => true,
            'data' => $product
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Product not found'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch product'
    ]);
}
