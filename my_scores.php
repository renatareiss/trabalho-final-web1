<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

require_login(); // Ensures user is logged in

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User'; // Fallback if name not in session for some reason
$scores = [];

// Fetch scores for the current user
$sql = "SELECT pontos, accuracy, data_partida
        FROM pontuacoes
        WHERE usuario_id = ?
        ORDER BY data_partida DESC";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $scores[] = $row;
    }
    $stmt->close();
} else {
    // Handle error
    error_log("Error preparing statement for my_scores: " . $conn->error);
}

$pageTitle = "My Scores";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Typing Game</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container scores-container"> <!-- Added scores-container for specific styling -->
        <header>
            <h1><?php echo htmlspecialchars($pageTitle) . " for " . htmlspecialchars($user_name); ?></h1>
            <nav>
                <a href="dashboard.php">Dashboard</a> |
                <a href="game.php">Play Game</a> |
                <a href="rankings.php">Global Rankings</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <?php if (!empty($scores)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>WPM</th>
                            <th>Accuracy</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scores as $index => $score): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($score['pontos']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($score['accuracy'], 2)); ?>%</td>
                                <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($score['data_partida']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You haven't recorded any scores yet. <a href="game.php">Play a game</a> to see your scores here!</p>
            <?php endif; ?>
            <?php if ($conn->error && !$stmt): // Show error if statement preparation failed ?>
                 <p class="errors">Could not retrieve your scores due to a database error. Please try again later.</p>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> Typing Game</p>
        </footer>
    </div>
</body>
</html>
