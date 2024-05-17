<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: /index.html');
    exit();
}

require_once 'functions.php';

// Retrieve all players from the database
$players = get_all_players();

// Retrieve the user's selected players if they have a saved team
$user_email = $_SESSION['email'];
$saved_team = get_user_team($user_email);
$saved_player_ids = $saved_team ? json_decode($saved_team['player_ids'], true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_players = $_POST['selected_players'] ?? [];
    $total_points = 0;
    $batsmen_count = 0;
    $allrounders_count = 0;
    $bowlers_count = 0;

    foreach ($selected_players as $player_id) {
        // Calculate total points for selected players
        $player = get_player_by_id($player_id);
        $total_points += $player['points'];

        // Count the types of players selected
        switch ($player['player_type']) {
            case 'batsman':
                $batsmen_count++;
                break;
            case 'all_rounder':
                $allrounders_count++;
                break;
            case 'bowler':
                $bowlers_count++;
                break;
        }
    }

    // Validate the selections
    if (count($selected_players) > 11 || $total_points > 100 || $batsmen_count > 5 || $allrounders_count > 2 || $bowlers_count > 4) {
        $error_message = "You can select a maximum of 11 players, including up to 5 batsmen, 2 all-rounders, and 4 bowlers. The total points must not exceed 100.";
    } else {
        // Save selected players to session or database
        save_user_team($user_email, $selected_players);
        $_SESSION['selected_players'] = $selected_players;
        header('Location: selected_players.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player List</title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_players[]"]');
            const submitButton = document.getElementById('submit-button');
            const totalPointsElement = document.getElementById('total-points');
            const remainingPointsElement = document.getElementById('remaining-points');
            const errorMessageElement = document.getElementById('error-message');

            function validateSelection() {
                const selected = Array.from(checkboxes).filter(cb => cb.checked);
                const batsmen = selected.filter(cb => cb.dataset.type === 'batsman').length;
                const allrounders = selected.filter(cb => cb.dataset.type === 'all_rounder').length;
                const bowlers = selected.filter(cb => cb.dataset.type === 'bowler').length;
                const totalPoints = selected.reduce((sum, cb) => sum + parseInt(cb.dataset.points), 0);

                totalPointsElement.textContent = totalPoints;
                remainingPointsElement.textContent = 100 - totalPoints;

                let errorMessage = '';
                if (selected.length > 11) {
                    errorMessage = 'You can select a maximum of 11 players.';
                } else if (totalPoints > 100) {
                    errorMessage = 'The total points must not exceed 100.';
                } else if (batsmen > 5) {
                    errorMessage = 'You can select a maximum of 5 batsmen.';
                } else if (allrounders > 2) {
                    errorMessage = 'You can select a maximum of 2 all-rounders.';
                } else if (bowlers > 4) {
                    errorMessage = 'You can select a maximum of 4 bowlers.';
                }

                if (errorMessage) {
                    errorMessageElement.textContent = errorMessage;
                    submitButton.disabled = true;
                } else {
                    errorMessageElement.textContent = '';
                    submitButton.disabled = false;
                }
            }

            checkboxes.forEach(cb => cb.addEventListener('change', validateSelection));

            validateSelection(); // Initial validation check
        });
    </script>
</head>
<body>
    <h1>Player List</h1>
    <?php if (isset($error_message)): ?>
        <p id="error-message"><?php echo $error_message; ?></p>
    <?php else: ?>
        <p id="error-message"></p>
    <?php endif; ?>


    <form action="player_list.php" method="POST">
        <h2>Select Players</h2>
        <p>Total Points: <span id="total-points">0</span></p>
        <p>Remaining Points: <span id="remaining-points">100</span></p>
        <table border="1">
            <tr>
                <th>Select</th>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Player Type</th>
                <th>Points</th>
            </tr>
            <?php foreach ($players as $player): ?>
            <tr>
                <td>
                    <input type="checkbox" 
                           name="selected_players[]" 
                           value="<?php echo $player['employee_id']; ?>"
                           data-type="<?php echo $player['player_type']; ?>"
                           data-points="<?php echo $player['points']; ?>"
                           <?php echo in_array($player['employee_id'], $saved_player_ids) ? 'checked' : ''; ?>>
                </td>
                <td><?php echo $player['employee_id']; ?></td>
                <td><?php echo $player['employee_name']; ?></td>
                <td><?php echo $player['player_type']; ?></td>
                <td><?php echo $player['points']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <button type="submit" id="submit-button" disabled>Submit Selection</button>
    </form>
    <a href="logout.php">Logout</a>
</body>
</html>
