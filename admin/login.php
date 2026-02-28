<?php
/**
 * Admin Login Page
 * Secure authentication with CSRF protection and rate limiting
 */

require_once 'includes/db.php';
require_once 'includes/csrf.php';

$error = '';
$success = '';

// Check if already logged in
if (isset($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST)) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate inputs
        $login_input = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($login_input) || empty($password)) {
            $error = 'Please enter username/email and password.';
        } else {
            // Check login attempts
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $stmt = $pdo->prepare("SELECT * FROM login_attempts WHERE ip_address = ? AND locked_until > NOW()");
            $stmt->execute([$ip_address]);
            $lockout = $stmt->fetch();

            if ($lockout) {
                $error = 'Too many failed attempts. Please try again in 15 minutes.';
            } else {
                // Fetch admin user by username OR email
                $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE (username = ? OR email = ?) AND status = 'active'");
                $stmt->execute([$login_input, $login_input]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password'])) {
                    // Login successful
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['admin_name'] = $admin['full_name'];
                    $_SESSION['last_activity'] = time();

                    // Reset login attempts
                    $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
                    $stmt->execute([$ip_address]);

                    redirect('dashboard.php');
                } else {
                    // Record failed attempt
                    $stmt = $pdo->prepare("INSERT INTO login_attempts (ip_address, attempts, last_attempt) 
                                           VALUES (?, 1, NOW()) 
                                           ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()");
                    $stmt->execute([$ip_address]);

                    // Check if should lock
                    $stmt = $pdo->prepare("SELECT attempts FROM login_attempts WHERE ip_address = ?");
                    $stmt->execute([$ip_address]);
                    $attempts = $stmt->fetch();

                    if ($attempts['attempts'] >= 3) {
                        $stmt = $pdo->prepare("UPDATE login_attempts SET locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE ip_address = ?");
                        $stmt->execute([$ip_address]);
                        $error = 'Too many failed attempts. Account locked for 15 minutes.';
                    } else {
                        $remaining = 3 - $attempts['attempts'];
                        $error = "Invalid credentials. {$remaining} attempts remaining.";
                    }
                }
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
    <title>Admin Login | Orchies Visual</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>ORCHIES<span style="color: var(--primary);">VISUAL</span></h1>
            <p>Admin Panel</p>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <?php echo csrf_token_input(); ?>

                <div class="form-group">
                    <label for="login">Username or Email</label>
                    <input type="text" id="login" name="login" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>
        </div>
    </div>
</body>
</html>
