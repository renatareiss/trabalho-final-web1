<?php
header('Content-Type: application/json'); // Ensure client expects JSON

require_once 'includes/db.php'; // Provides $conn
require_once 'includes/auth.php'; // Provides session_start() and is_logged_in()

// session_start() is called in auth.php, so it should be active.
// Explicitly check and start if not, for robustness.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

if (!is_logged_in()) {
    $response['message'] = 'User not logged in.';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data from the request body
    $input_data = json_decode(file_get_contents('php://input'), true);

    if ($input_data === null && json_last_error() !== JSON_ERROR_NONE) {
        $response['message'] = 'Invalid JSON data received. ' . json_last_error_msg();
        echo json_encode($response);
        exit;
    }

    $wpm = $input_data['wpm'] ?? null;
    $accuracy = $input_data['accuracy'] ?? null;
    $user_id = $_SESSION['user_id'];

    // Validate data
    if (!isset($wpm) || !is_numeric($wpm)) {
        $response['message'] = 'Invalid WPM value.';
        echo json_encode($response);
        exit;
    }
    if (!isset($accuracy) || !is_numeric($accuracy) || $accuracy < 0 || $accuracy > 100) {
        $response['message'] = 'Invalid accuracy value.';
        echo json_encode($response);
        exit;
    }

    // Prepare SQL statement
    // The 'pontuacoes' table will be updated to include 'accuracy' and 'data_partida'
    // For now, assuming it will have 'usuario_id', 'pontos' (for WPM), 'accuracy', 'data_partida'
    $stmt = $conn->prepare("INSERT INTO pontuacoes (usuario_id, pontos, accuracy, data_partida) VALUES (?, ?, ?, NOW())");

    if (!$stmt) {
        $response['message'] = 'Database error (prepare failed): ' . $conn->error;
        echo json_encode($response);
        exit;
    }

    // user_id (INT), pontos/wpm (INT), accuracy (DECIMAL/DOUBLE)
    $stmt->bind_param("iid", $user_id, $wpm, $accuracy);

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Score saved successfully!';
    } else {
        $response['message'] = 'Failed to save score: ' . $stmt->error;
    }
    $stmt->close();

} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
