<?php
session_start();
require_once 'db.php'; // db.php should be in the same 'includes' directory

function register_user($name, $email, $password, $confirm_password, $conn) {
    $errors = [];

    // Input validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (!empty($errors)) {
        return $errors;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    if (!$stmt) {
        $errors[] = "Database error (prepare failed): " . $conn->error;
        return $errors;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "Email already registered.";
    }
    $stmt->close();

    if (!empty($errors)) {
        return $errors;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    if ($hashed_password === false) {
        $errors[] = "Error hashing password.";
        return $errors;
    }

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
    if (!$stmt) {
        $errors[] = "Database error (prepare failed): " . $conn->error;
        return $errors;
    }
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['user_name'] = $name;
        $stmt->close();
        return true;
    } else {
        $errors[] = "Error creating user: " . $stmt->error;
        $stmt->close();
        return $errors;
    }
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function logout_user() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

function login_user($email, $password, $conn) {
    $errors = [];

    // Input validation
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (!empty($errors)) {
        return $errors;
    }

    // Fetch user from database
    $stmt = $conn->prepare("SELECT id, nombre, password FROM usuarios WHERE email = ?");
    if (!$stmt) {
        return ["Database error (prepare failed): " . $conn->error];
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $stmt->close();
            return true;
        } else {
            // Invalid password
            $stmt->close();
            return ["Invalid email or password."];
        }
    } else {
        // User not found
        $stmt->close();
        return ["Invalid email or password."];
    }
}
?>
