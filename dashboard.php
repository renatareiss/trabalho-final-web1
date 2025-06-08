<?php
require_once 'includes/auth.php'; // Includes session_start() and require_login()

require_login(); // Checks if user is logged in and redirects to login.php if not

$userName = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Typing Game</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Typing Game</h1>
            <nav>
                Welcome, <?php echo $userName; ?>! |
                <a href="game.php">Play Game</a> |
                <a href="my_scores.php">My Scores</a> |
                <a href="rankings.php">Rankings</a> |
                <a href="leagues.php">Leagues</a> |
                <a href="report.php">Reports</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <h2>Welcome, <?php echo $userName; ?>!</h2>
            <p>This is your dashboard. From here you can start a new game, view your scores, check rankings, or manage your leagues.</p>

            <h3>Quick Actions</h3>
            <ul>
                <li><a href="game.php">Start a New Game</a></li>
                <li><a href="my_scores.php">View Your Past Scores</a></li>
                <li><a href="rankings.php">See Global Rankings</a></li>
            </ul>
        </main>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> Typing Game</p>
        </footer>
    </div>
</body>
</html>
