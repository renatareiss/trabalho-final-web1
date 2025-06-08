<?php
require_once 'includes/db.php'; // Ensures $conn is available
require_once 'includes/auth.php'; // For login_user() and session_start()

// session_start() is already called in auth.php, so it's not strictly needed here if auth.php is included first.
// However, it's good practice to ensure it's called.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to dashboard
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = login_user($email, $password, $conn);

    if ($result === true) {
        header('Location: dashboard.php');
        exit;
    } else {
        $errors = (array)$result; // Ensure $errors is always an array
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Typing Game</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST" id="loginForm" novalidate>
            <div class="form-field-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                <!-- Error span will be inserted here by JS -->
            </div>
            <div class="form-field-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <!-- Error span will be inserted here by JS -->
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <script src="js/validaForm.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loginForm = document.getElementById('loginForm');
            const emailField = document.getElementById('email');
            const passwordField = document.getElementById('password');

            loginForm.addEventListener('submit', function(event) {
                let isValid = true;

                // Clear all previous errors
                clearError(emailField);
                clearError(passwordField);

                if (!validateEmail(emailField)) {
                    isValid = false;
                }
                if (!validateNotEmpty(passwordField, 'Password')) { // Or validatePasswordLength if you want to enforce length here too
                    isValid = false;
                }

                if (!isValid) {
                    event.preventDefault(); // Stop submission if validation fails
                }
            });
        });
    </script>
</body>
</html>
