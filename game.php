<?php
require_once 'includes/auth.php'; // Includes session_start() and other auth functions

require_login(); // Ensures user is logged in, redirects to login.php if not
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Game</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div id="game-container">
        <header>
            <h1>Typing Test</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a> |
                <a href="my_scores.php">My Scores</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <div id="text-to-type-area">
            <p>Click "Start Game" to begin!</p>
            <div id="text-to-type"></div>
        </div>

        <textarea id="user-input" rows="10" cols="50" placeholder="Start typing here when the game begins..." disabled></textarea>

        <div class="game-info">
            <div id="timer" class="info-area">Time: <span id="time-left">60</span>s</div>
            <div id="wpm" class="info-area">WPM: <span id="wpm-value">0</span></div>
            <div id="accuracy" class="info-area">Accuracy: <span id="accuracy-value">100</span>%</div>
        </div>

        <button id="start-button">Start Game</button>
        <button id="reset-button" style="display:none;">Reset Game</button> <!-- Initially hidden -->
    </div>

    <script src="game.js"></script>
</body>
</html>
