<?php
/**
 * Delete Product Page (Soft Delete)
 * Marks product as deleted instead of removing from database
 */

require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/csrf.php';

require_auth();

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $_SESSION['error'] = 'Invalid request method.';
    header('Location: index.php');
    exit;
}

// Check CSRF token
if (!csrf_validate($_GET)) {
    $_SESSION['error'] = 'Invalid form submission.';
    header('Location: index.php');
    exit;
}

// Get product ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['error'] = 'Invalid product ID.';
    header('Location: index.php');
    exit;
}

// Soft delete product (update status to 'deleted')
$stmt = $pdo->prepare("UPDATE products SET status = 'deleted' WHERE id = ? AND status != 'deleted'");
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $_SESSION['success'] = 'Product deleted successfully.';
} else {
    $_SESSION['error'] = 'Product not found or already deleted.';
}

header('Location: index.php');
exit;
