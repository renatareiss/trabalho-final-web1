<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $result = register_user($name, $email, $password, $confirm_password, $conn);

    if ($result === true) {
        header('Location: dashboard.php');
        exit;
    } else {
        $errors = (array)$result;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Typing Game</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="register.php" method="POST" id="registerForm" novalidate>
            <div class="form-field-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                <!-- Error span will be inserted here by JS -->
            </div>
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
            <div class="form-field-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <!-- Error span will be inserted here by JS -->
            </div>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <script src="js/validaForm.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const registerForm = document.getElementById('registerForm');
            const nameField = document.getElementById('name');
            const emailField = document.getElementById('email');
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm_password');

            registerForm.addEventListener('submit', function(event) {
                let isValid = true;

                // Clear all previous errors first
                clearError(nameField);
                clearError(emailField);
                clearError(passwordField);
                clearError(confirmPasswordField);

                if (!validateNotEmpty(nameField, 'Name')) {
                    isValid = false;
                }
                if (!validateEmail(emailField)) {
                    isValid = false;
                }
                if (!validatePasswordLength(passwordField, 8)) {
                    isValid = false;
                }
                if (!validatePasswordMatch(passwordField, confirmPasswordField)) {
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
