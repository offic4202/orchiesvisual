<?php
/**
 * ONE-TIME SETUP SCRIPT
 * Run this ONCE to create database tables and admin account
 * DELETE after use!
 */

$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'orchiesvisual_db';
$dbuser = getenv('DB_USER') ?: 'root';
$dbpass = getenv('DB_PASSWORD') ?: '';

$message = '';
$error = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE $dbname");

    // Create products table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_name VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            category ENUM('rentals', 'sales', 'ebooks') NOT NULL,
            product_image VARCHAR(255),
            status ENUM('active', 'inactive', 'deleted') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_category (category),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Create admin_users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(255),
            status ENUM('active', 'inactive') DEFAULT 'active',
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Create login_attempts table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            attempts INT DEFAULT 0,
            last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            locked_until TIMESTAMP NULL,
            INDEX idx_ip (ip_address),
            INDEX idx_locked (locked_until)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
    $stmt->execute(['admin']);
    $admin_exists = $stmt->fetch();

    if ($admin_exists) {
        $message = '✓ Database created successfully!<br>
                    ✓ All tables created!<br>
                    ✓ Admin account already exists!<br><br>
                    <strong>Username:</strong> admin<br>
                    <strong>Password:</strong> admin123<br><br>
                    <em>Delete this file (setup.php) for security!</em>';
    } else {
        // Create admin user
        $password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, email, password, full_name, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@orchiesvisual.com', $password_hash, 'System Administrator', 'active']);
        
        $message = '✓ Database created successfully!<br>
                    ✓ All tables created!<br>
                    ✓ Admin account created!<br><br>
                    <strong>Username:</strong> admin<br>
                    <strong>Password:</strong> admin123<br><br>
                    <em>Delete this file (setup.php) for security!</em>';
    }

} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage() . '<br><br>
              Make sure Docker containers are running and database is accessible.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup | Orchies Visual Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .setup-box {
            background: #1a1a1a;
            border: 1px solid #333;
            padding: 40px;
            border-radius: 12px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        h1 {
            color: #fff;
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        h1 span { color: #ff6b35; }
        .subtitle { color: #888; margin-bottom: 30px; }
        .success {
            background: rgba(76, 175, 80, 0.1);
            border: 1px solid #4CAF50;
            color: #4CAF50;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            line-height: 1.8;
        }
        .error {
            background: rgba(244, 67, 54, 0.1);
            border: 1px solid #f44336;
            color: #f44336;
            padding: 20px;
            border-radius: 8px;
        }
        .warning {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid #ffc107;
            color: #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 0.9rem;
        }
        .btn {
            display: inline-block;
            background: #ff6b35;
            color: #fff;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 20px;
            font-weight: 600;
        }
        .btn:hover { background: #ff8c5a; }
    </style>
</head>
<body>
    <div class="setup-box">
        <h1>ORCHIES<span>VISUAL</span></h1>
        <p class="subtitle">Admin Setup</p>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php elseif ($message): ?>
            <div class="success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="warning">
            ⚠️ <strong>SECURITY WARNING:</strong><br>
            Delete <code>admin/setup.php</code> after setup to prevent unauthorized access.
        </div>

        <a href="login.php" class="btn">Go to Login →</a>
    </div>
</body>
</html>
