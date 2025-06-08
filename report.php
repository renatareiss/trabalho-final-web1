<?php
require_once 'includes/db.php'; // May not be strictly needed for a placeholder, but good for consistency
require_once 'includes/auth.php';

require_login();

$pageTitle = "Reports";
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
    <div class="container report-container">
        <header>
            <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
            <nav>
                <a href="dashboard.php">Dashboard</a> |
                <a href="game.php">Play Game</a> |
                <a href="my_scores.php">My Scores</a> |
                <a href="leagues.php">Leagues</a> |
                <a href="rankings.php">Global Rankings</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <p>Reporting functionality is under development. Please check back later.</p>
            <p>Future features might include detailed performance analysis, progress charts, and more.</p>
        </main>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> Typing Game</p>
        </footer>
    </div>
</body>
</html>
