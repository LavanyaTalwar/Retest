<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header('Location: /index.html');
    exit();
}

require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $employee_name = $_POST['employee_name'];
    $player_type = $_POST['player_type'];
    $points = $_POST['points'];

    // Insert player details into the new table
    if (add_player($employee_id, $employee_name, $player_type, $points)) {
        echo "Player added successfully!";
    } else {
        echo "Error adding player!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Add Player</title>
</head>
<body>
    <div class="add-player-container">
        <h2>Add Player</h2>
        <form action="add_player.php" method="POST">
            <label for="employee-id">Employee ID:</label>
            <input type="number" id="employee-id" name="employee_id" required>
            <label for="employee-name">Employee Name:</label>
            <input type="text" id="employee-name" name="employee_name" required>
            <label for="player-type">Player Type:</label>
            <select id="player-type" name="player_type" required>
                <option value="batsman">Batsman</option>
                <option value="bowler">Bowler</option>
                <option value="all_rounder">All Rounder</option>
            </select>
            <label for="points">Points:</label>
            <input type="number" id="points" name="points" min="2" max="10" required>
            <button type="submit">Add Player</button>
        </form>
    </div>
    <a href="dashboard.php">Back to Dashboard</a>
    <script src="/js/scripts.js"></script>
</body>
</html>
