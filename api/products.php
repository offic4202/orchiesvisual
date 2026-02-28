<?php
/**
 * Products API Endpoint
 * Returns all active products as JSON for frontend integration
 */

header('Content-Type: application/json');

require_once '../admin/includes/db.php';

try {
    $stmt = $pdo->prepare("SELECT id, product_name, description, price, category, product_image, status, created_at FROM products WHERE status = 'active' ORDER BY created_at DESC");
    $stmt->execute();
    $products = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => $products,
        'count' => count($products)
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch products'
    ]);
}
