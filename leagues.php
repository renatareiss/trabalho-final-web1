<?php
require_once 'includes/db.php'; // Provides $conn
require_once 'includes/auth.php'; // Provides session_start(), is_logged_in(), require_login()

require_login();

$user_id = $_SESSION['user_id'];
$feedback_messages = []; // To store success/error messages

// --- Handle Create League ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_league'])) {
    $league_name = trim($_POST['league_name'] ?? '');
    $league_keyword = trim($_POST['league_keyword'] ?? '');

    if (empty($league_name) || empty($league_keyword)) {
        $feedback_messages[] = ['type' => 'error', 'message' => 'League Name and Keyword are required to create a league.'];
    } else {
        // Check if keyword is unique
        $stmt_check = $conn->prepare("SELECT id FROM ligas WHERE palavra_chave = ?");
        $stmt_check->bind_param("s", $league_keyword);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $feedback_messages[] = ['type' => 'error', 'message' => 'This keyword is already taken. Please choose another.'];
        } else {
            $conn->begin_transaction();
            try {
                // Insert new league
                $stmt_create = $conn->prepare("INSERT INTO ligas (nome, palavra_chave, criado_por) VALUES (?, ?, ?)");
                $stmt_create->bind_param("ssi", $league_name, $league_keyword, $user_id);
                $stmt_create->execute();
                $new_league_id = $stmt_create->insert_id;
                $stmt_create->close();

                // Add creator to the league
                $stmt_join = $conn->prepare("INSERT INTO ligas_usuarios (liga_id, usuario_id) VALUES (?, ?)");
                $stmt_join->bind_param("ii", $new_league_id, $user_id);
                $stmt_join->execute();
                $stmt_join->close();

                $conn->commit();
                $feedback_messages[] = ['type' => 'success', 'message' => 'League "' . htmlspecialchars($league_name) . '" created successfully!'];
            } catch (mysqli_sql_exception $exception) {
                $conn->rollback();
                $feedback_messages[] = ['type' => 'error', 'message' => 'Error creating league: ' . $exception->getMessage()];
            }
        }
        $stmt_check->close();
    }
}

// --- Handle Join League ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_league'])) {
    $join_keyword = trim($_POST['join_keyword'] ?? '');

    if (empty($join_keyword)) {
        $feedback_messages[] = ['type' => 'error', 'message' => 'Keyword is required to join a league.'];
    } else {
        $stmt_find = $conn->prepare("SELECT id, nome FROM ligas WHERE palavra_chave = ?");
        $stmt_find->bind_param("s", $join_keyword);
        $stmt_find->execute();
        $result_find = $stmt_find->get_result();

        if ($league_to_join = $result_find->fetch_assoc()) {
            $league_id_to_join = $league_to_join['id'];
            $league_name_to_join = $league_to_join['nome'];

            // Check if user is already a member
            $stmt_check_member = $conn->prepare("SELECT id FROM ligas_usuarios WHERE liga_id = ? AND usuario_id = ?");
            $stmt_check_member->bind_param("ii", $league_id_to_join, $user_id);
            $stmt_check_member->execute();
            $stmt_check_member->store_result();

            if ($stmt_check_member->num_rows > 0) {
                $feedback_messages[] = ['type' => 'info', 'message' => 'You are already a member of "' . htmlspecialchars($league_name_to_join) . '".'];
            } else {
                $stmt_do_join = $conn->prepare("INSERT INTO ligas_usuarios (liga_id, usuario_id) VALUES (?, ?)");
                $stmt_do_join->bind_param("ii", $league_id_to_join, $user_id);
                if ($stmt_do_join->execute()) {
                    $feedback_messages[] = ['type' => 'success', 'message' => 'Successfully joined league "' . htmlspecialchars($league_name_to_join) . '".'];
                } else {
                    $feedback_messages[] = ['type' => 'error', 'message' => 'Error joining league: ' . $stmt_do_join->error];
                }
                $stmt_do_join->close();
            }
            $stmt_check_member->close();
        } else {
            $feedback_messages[] = ['type' => 'error', 'message' => 'No league found with that keyword.'];
        }
        $stmt_find->close();
    }
}


// --- Fetch User's Leagues ---
$user_leagues = [];
$sql_user_leagues = "SELECT l.id, l.nome, l.palavra_chave
                     FROM ligas l
                     JOIN ligas_usuarios lu ON l.id = lu.liga_id
                     WHERE lu.usuario_id = ?
                     ORDER BY l.nome";
$stmt_user_leagues = $conn->prepare($sql_user_leagues);
$stmt_user_leagues->bind_param("i", $user_id);
$stmt_user_leagues->execute();
$result_user_leagues = $stmt_user_leagues->get_result();
while ($row = $result_user_leagues->fetch_assoc()) {
    $user_leagues[] = $row;
}
$stmt_user_leagues->close();

$pageTitle = "Your Leagues";
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
    <div class="container leagues-container">
        <header>
            <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
            <nav>
                <a href="dashboard.php">Dashboard</a> |
                <a href="game.php">Play Game</a> |
                <a href="my_scores.php">My Scores</a> |
                <a href="rankings.php">Global Rankings</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <?php if (!empty($feedback_messages)): ?>
                <div class="feedback-messages">
                    <?php foreach ($feedback_messages as $fm): ?>
                        <p class="message-<?php echo htmlspecialchars($fm['type']); ?>"><?php echo htmlspecialchars($fm['message']); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <section class="user-leagues-section">
                <h2>Leagues You're In</h2>
                <?php if (!empty($user_leagues)): ?>
                    <ul>
                        <?php foreach ($user_leagues as $league): ?>
                            <li>
                                <a href="view_league.php?id=<?php echo $league['id']; ?>" class="league-link">
                                    <strong><?php echo htmlspecialchars($league['nome']); ?></strong>
                                </a>
                                (Keyword: <?php echo htmlspecialchars($league['palavra_chave']); ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>You are not currently a member of any leagues. Why not create or join one?</p>
                <?php endif; ?>
            </section>

            <section class="league-actions">
                <div class="create-league-form">
                    <h3>Create a New League</h3>
                    <form action="leagues.php" method="POST" id="createLeagueForm" novalidate>
                        <div class="form-field-group">
                            <label for="league_name">League Name:</label>
                            <input type="text" id="league_name" name="league_name" required>
                            <!-- Error span for league_name -->
                        </div>
                        <div class="form-field-group">
                            <label for="league_keyword">League Keyword (for joining):</label>
                            <input type="text" id="league_keyword" name="league_keyword" required>
                            <!-- Error span for league_keyword -->
                        </div>
                        <button type="submit" name="create_league">Create League</button>
                    </form>
                </div>

                <div class="join-league-form">
                    <h3>Join an Existing League</h3>
                    <form action="leagues.php" method="POST" id="joinLeagueForm" novalidate>
                        <div class="form-field-group">
                            <label for="join_keyword">League Keyword:</label>
                            <input type="text" id="join_keyword" name="join_keyword" required>
                            <!-- Error span for join_keyword -->
                        </div>
                        <button type="submit" name="join_league">Join League</button>
                    </form>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> Typing Game</p>
        </footer>
    </div>

    <script src="js/validaForm.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Validation for Create League Form
            const createLeagueForm = document.getElementById('createLeagueForm');
            if (createLeagueForm) {
                const leagueNameField = document.getElementById('league_name');
                const leagueKeywordField = document.getElementById('league_keyword');

                createLeagueForm.addEventListener('submit', function(event) {
                    let isValid = true;
                    clearError(leagueNameField);
                    clearError(leagueKeywordField);

                    if (!validateNotEmpty(leagueNameField, 'League Name')) {
                        isValid = false;
                    }
                    if (!validateNotEmpty(leagueKeywordField, 'League Keyword')) {
                        isValid = false;
                    }
                    // Add more specific validation for keyword format if needed (e.g., no spaces)

                    if (!isValid) {
                        event.preventDefault();
                    }
                });
            }

            // Validation for Join League Form
            const joinLeagueForm = document.getElementById('joinLeagueForm');
            if (joinLeagueForm) {
                const joinKeywordField = document.getElementById('join_keyword');

                joinLeagueForm.addEventListener('submit', function(event) {
                    let isValid = true;
                    clearError(joinKeywordField);

                    if (!validateNotEmpty(joinKeywordField, 'League Keyword')) {
                        isValid = false;
                    }

                    if (!isValid) {
                        event.preventDefault();
                    }
                });
            }
        });
    </script>
</body>
</html>
