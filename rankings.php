<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

require_login(); // Ensures user is logged in

// Fetch top scores
$limit = 25; // Show top 25 scores
$scores = [];

$sql = "SELECT u.nome, p.pontos, p.accuracy, p.data_partida
        FROM pontuacoes p
        JOIN usuarios u ON p.usuario_id = u.id
        ORDER BY p.pontos DESC, p.accuracy DESC, p.data_partida DESC
        LIMIT ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $scores[] = $row;
    }
    $stmt->close();
} else {
    // Handle error, e.g., log it or display a message
    error_log("Error preparing statement for rankings: " . $conn->error);
    // For user display, you might set an error message variable
}

$pageTitle = "Global Rankings";
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
    <div class="container rankings-container"> <!-- Added rankings-container for specific styling -->
        <header>
            <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
            <nav>
                <a href="dashboard.php">Dashboard</a> |
                <a href="game.php">Play Game</a> |
                <a href="my_scores.php">My Scores</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <?php if (!empty($scores)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Player Name</th>
                            <th>WPM</th>
                            <th>Accuracy</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scores as $index => $score): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($score['nome']); ?></td>
                                <td><?php echo htmlspecialchars($score['pontos']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($score['accuracy'], 2)); ?>%</td>
                                <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($score['data_partida']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No scores recorded yet. Be the first to set a record!</p>
            <?php endif; ?>
            <?php if ($conn->error): ?>
                 <p class="errors">Could not retrieve scores due to a database error. Please try again later.</p>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> Typing Game</p>
        </footer>
    </div>
</body>
</html>
