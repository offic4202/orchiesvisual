<?php
/**
 * Add New Product Page
 * Form to create new products with image upload
 */

require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/csrf.php';

require_auth();

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST)) {
        $error = 'Invalid form submission.';
    } else {
        $product_name = trim($_POST['product_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $category = $_POST['category'] ?? '';
        $status = $_POST['status'] ?? 'active';

        // Validation
        if (empty($product_name)) {
            $error = 'Product name is required.';
        } elseif (empty($category)) {
            $error = 'Please select a category.';
        } elseif ($price < 0) {
            $error = 'Price must be a positive number.';
        } else {
            // Handle file upload
            $product_image = '';
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
                $max_size = 2 * 1024 * 1024; // 2MB

                $file = $_FILES['product_image'];

                if (!in_array($file['type'], $allowed_types)) {
                    $error = 'Invalid file type. Only JPG, PNG, and WebP are allowed.';
                } elseif ($file['size'] > $max_size) {
                    $error = 'File size must be less than 2MB.';
                } else {
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $new_filename = uniqid() . '_' . time() . '.' . $extension;
                    $upload_path = '../uploads/products/' . $new_filename;

                    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                        $product_image = $new_filename;
                    } else {
                        $error = 'Failed to upload image. Please try again.';
                    }
                }
            }

            if (empty($error)) {
                // Insert product
                $stmt = $pdo->prepare("INSERT INTO products (product_name, description, price, category, product_image, status) 
                                       VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$product_name, $description, $price, $category, $product_image, $status]);

                $_SESSION['success'] = 'Product added successfully!';
                header('Location: index.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Orchies Visual Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-logo">ORCHIES<span>VISUAL</span></div>
            <ul class="sidebar-nav">
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="index.php">Products</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h1>Add New Product</h1>
                <a href="index.php" class="btn btn-secondary">← Back to Products</a>
            </div>

            <div class="form-container">
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <?php echo csrf_token_input(); ?>

                    <div class="form-group">
                        <label for="product_name">Product Name *</label>
                        <input type="text" id="product_name" name="product_name" required value="<?php echo htmlspecialchars($_POST['product_name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Price (₦) *</label>
                            <input type="number" id="price" name="price" step="0.01" min="0" required value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
                            <small style="color: var(--text-muted);">For rentals: price per day</small>
                        </div>

                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="rentals" <?php echo (isset($_POST['category']) && $_POST['category'] === 'rentals') ? 'selected' : ''; ?>>Rentals</option>
                                <option value="sales" <?php echo (isset($_POST['category']) && $_POST['category'] === 'sales') ? 'selected' : ''; ?>>Sales</option>
                                <option value="ebooks" <?php echo (isset($_POST['category']) && $_POST['category'] === 'ebooks') ? 'selected' : ''; ?>>E-Books</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="product_image">Product Image</label>
                            <input type="file" id="product_image" name="product_image" accept="image/jpeg,image/png,image/webp">
                            <small style="color: var(--text-muted);">Max size: 2MB. Formats: JPG, PNG, WebP</small>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">Add Product</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
