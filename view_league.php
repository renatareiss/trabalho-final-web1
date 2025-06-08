<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

require_login();

$user_id = $_SESSION['user_id'];
$league_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$league_id) {
    // Redirect to leagues page or show error if ID is missing or invalid
    header('Location: leagues.php');
    exit;
}

// Fetch league details
$stmt_league = $conn->prepare("SELECT nome, palavra_chave FROM ligas WHERE id = ?");
$stmt_league->bind_param("i", $league_id);
$stmt_league->execute();
$result_league = $stmt_league->get_result();
$league_details = $result_league->fetch_assoc();
$stmt_league->close();

if (!$league_details) {
    // League not found
    $_SESSION['feedback_message'] = ['type' => 'error', 'message' => 'League not found.']; // Using session for feedback on redirect
    header('Location: leagues.php');
    exit;
}

// Verify current user is a member of this league
$stmt_check_member = $conn->prepare("SELECT id FROM ligas_usuarios WHERE liga_id = ? AND usuario_id = ?");
$stmt_check_member->bind_param("ii", $league_id, $user_id);
$stmt_check_member->execute();
$stmt_check_member->store_result();
$is_member = $stmt_check_member->num_rows > 0;
$stmt_check_member->close();

if (!$is_member) {
    // User is not a member of this league
    $_SESSION['feedback_message'] = ['type' => 'error', 'message' => 'You are not a member of this league.'];
    header('Location: leagues.php');
    exit;
}

$pageTitle = "View League: " . htmlspecialchars($league_details['nome']);

// Fetch League Members
$league_members = [];
$sql_members = "SELECT u.nome FROM usuarios u JOIN ligas_usuarios lu ON u.id = lu.usuario_id WHERE lu.liga_id = ? ORDER BY u.nome";
$stmt_members = $conn->prepare($sql_members);
$stmt_members->bind_param("i", $league_id);
$stmt_members->execute();
$result_members = $stmt_members->get_result();
while ($row = $result_members->fetch_assoc()) {
    $league_members[] = $row;
}
$stmt_members->close();

// Fetch League-Specific Rankings
$league_rankings = [];
$sql_rankings = "SELECT u.nome, p.pontos, p.accuracy, p.data_partida
                 FROM pontuacoes p
                 JOIN usuarios u ON p.usuario_id = u.id
                 WHERE p.liga_id = ?
                 ORDER BY p.pontos DESC, p.accuracy DESC, p.data_partida DESC
                 LIMIT 25"; // Limit to top 25 for this league
$stmt_rankings = $conn->prepare($sql_rankings);
$stmt_rankings->bind_param("i", $league_id);
$stmt_rankings->execute();
$result_rankings = $stmt_rankings->get_result();
while ($row = $result_rankings->fetch_assoc()) {
    $league_rankings[] = $row;
}
$stmt_rankings->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Typing Game</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container view-league-container">
        <header>
            <h1><?php echo $pageTitle; ?></h1>
            <p>Keyword: <strong><?php echo htmlspecialchars($league_details['palavra_chave']); ?></strong></p>
            <nav>
                <a href="dashboard.php">Dashboard</a> |
                <a href="leagues.php">Your Leagues</a> |
                <a href="game.php">Play Game</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <section class="league-members-section">
                <h2>League Members (<?php echo count($league_members); ?>)</h2>
                <?php if (!empty($league_members)): ?>
                    <ul>
                        <?php foreach ($league_members as $member): ?>
                            <li><?php echo htmlspecialchars($member['nome']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>This league has no members yet (which is odd, as you should be one!).</p>
                <?php endif; ?>
            </section>

            <section class="league-rankings-section">
                <h2>League Rankings</h2>
                <?php if (!empty($league_rankings)): ?>
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
                            <?php foreach ($league_rankings as $index => $score): ?>
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
                    <p>No scores recorded for this league yet. Be the first!</p>
                <?php endif; ?>
            </section>
        </main>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> Typing Game</p>
        </footer>
    </div>
</body>
</html>
