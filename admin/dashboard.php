<?php
/**
 * Admin Dashboard
 * Overview of products and stats
 */

require_once 'includes/db.php';
require_once 'includes/auth.php';

require_auth();

// Get stats
$total_products = $pdo->query("SELECT COUNT(*) FROM products WHERE status != 'deleted'")->fetchColumn();
$active_products = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn();
$rentals_count = $pdo->query("SELECT COUNT(*) FROM products WHERE category = 'rentals' AND status = 'active'")->fetchColumn();
$sales_count = $pdo->query("SELECT COUNT(*) FROM products WHERE category = 'sales' AND status = 'active'")->fetchColumn();
$ebooks_count = $pdo->query("SELECT COUNT(*) FROM products WHERE category = 'ebooks' AND status = 'active'")->fetchColumn();

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Orchies Visual Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-logo">ORCHIES<span>VISUAL</span></div>
            <ul class="sidebar-nav">
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="products/index.php">Products</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h1>Welcome, <?php echo htmlspecialchars($admin_name); ?></h1>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo number_format($total_products); ?></h3>
                    <p>Total Products</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo number_format($active_products); ?></h3>
                    <p>Active Products</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo number_format($rentals_count); ?></h3>
                    <p>Rentals</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo number_format($sales_count); ?></h3>
                    <p>For Sale</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo number_format($ebooks_count); ?></h3>
                    <p>E-Books</p>
                </div>
            </div>

            <div class="data-table-container">
                <div class="page-header" style="padding: 20px 20px 0;">
                    <h2 style="font-size: 1.3rem;">Quick Actions</h2>
                </div>
                <div style="padding: 20px;">
                    <a href="products/add.php" class="btn btn-primary" style="margin-right: 10px;">+ Add New Product</a>
                    <a href="products/index.php" class="btn btn-secondary">View All Products</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
