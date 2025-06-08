<?php
// No direct db connection needed for index, but auth.php might use it for session validation if complex.
// For now, auth.php is mainly for session_start() and is_logged_in().
require_once 'includes/auth.php'; // Handles session_start()

$logged_in = is_logged_in();
$pageTitle = "Welcome to TypeMaster!";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container index-container"> <!-- General container, can add specific class if needed -->
        <header class="main-header">
            <div class="logo">
                <h1>TypeMaster</h1>
            </div>
            <nav class="main-nav">
                <?php if ($logged_in): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="game.php">Play Game</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </nav>
        </header>

        <main class="index-main">
            <section class="hero-section">
                <h2>Test Your Typing Speed and Accuracy!</h2>
                <p>Welcome to TypeMaster, the ultimate platform to practice your typing skills, compete with others, and track your progress. Improve your words per minute (WPM) and aim for perfect accuracy.</p>

                <div class="cta-buttons">
                    <?php if ($logged_in): ?>
                        <a href="game.php" class="cta-button primary-cta">Start Playing Now</a>
                    <?php else: ?>
                        <a href="register.php" class="cta-button primary-cta">Get Started - Register</a>
                        <a href="login.php" class="cta-button secondary-cta">Already a Member? Login</a>
                    <?php endif; ?>
                </div>
            </section>

            <section class="features-section">
                <h3>Why TypeMaster?</h3>
                <ul>
                    <li>📝 Practice with a variety of texts.</li>
                    <li>⏱️ Timed tests to challenge yourself.</li>
                    <li>📊 Track your WPM and accuracy over time.</li>
                    <li>🏆 Compete on the global leaderboard.</li>
                    <li>👥 Join leagues and compete with friends (Coming Soon!).</li>
                </ul>
            </section>
        </main>

        <footer class="main-footer">
            <p>&copy; <?php echo date("Y"); ?> TypeMaster. All rights reserved.</p>
            <!-- <p><a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p> -->
        </footer>
    </div>
</body>
</html>
