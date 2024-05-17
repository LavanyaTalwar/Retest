<?php

/**
 * @file
 * Display selected players for the logged-in user
 */

session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['email'])) {
    header('Location: /index.html');
    exit();
}

require_once 'functions.php';

/**
 * 
 * Manages the retrieval and display of the user's selected players
 */
class UserTeam
{
    private $userEmail;
    private $selectedPlayers = [];
    private $totalPoints = 0;

    /**
     * UserTeam constructor
     * 
     * @param string $email
     *   email of the logged-in user
     */
    public function __construct($email)
    {
        $this->userEmail = $email;
        $this->loadTeam();
    }

    /**
     * loads the team for the user.
     */
    private function loadTeam()
    {
        $savedTeam = $this->getUserTeam($this->userEmail);
        $selectedPlayerIds = $savedTeam ? json_decode($savedTeam['player_ids'], TRUE) : [];

        foreach ($selectedPlayerIds as $playerId) {
            $player = $this->getPlayerById($playerId);
            if ($player) {
                $this->selectedPlayers[] = $player;
                $this->totalPoints += $player['points'];
            }
        }
    }

    /**
     * retrieves the saved team for the user from the database
     * 
     * @param string $email
     *   the email of the user
     * 
     * @return array|null
     *   the saved team or NULL if not found.
     */
    private function getUserTeam($email)
    {
        return get_user_team($email);
    }

    /**
     * retrieves player details by ID.
     * 
     * @param int $playerId
     *   The ID of the player.
     * 
     * @return array|null
     *   thr player details or NULL if not found
     */
    private function getPlayerById($playerId)
    {
        return get_player_by_id($playerId);
    }

    /**
     * gets the selected players
     * 
     * @return array
     *   selected playees
     */
    public function getSelectedPlayers()
    {
        return $this->selectedPlayers;
    }

    /**
     * gets the total points used
     * 
     * @return int
     *   The total points used.
     */
    public function getTotalPoints()
    {
        return $this->totalPoints;
    }

    /**
     * Gets the remaining points.
     * 
     * @return int
     *   The remaining points.
     */
    public function getRemainingPoints()
    {
        return 100 - $this->totalPoints;
    }
}

// Instantiate UserTeam for the logged in user.
$userTeam = new UserTeam($_SESSION['email']);
$selectedPlayers = $userTeam->getSelectedPlayers();
$totalPoints = $userTeam->getTotalPoints();
$remainingPoints = $userTeam->getRemainingPoints();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selected Players</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body>
    <h1>Selected Players</h1>

    <table border="1">
        <tr>
            <th>Employee ID</th>
            <th>Employee Name</th>
            <th>Player Type</th>
            <th>Points</th>
        </tr>
        <?php foreach ($selectedPlayers as $player) : ?>
            <tr>
                <td><?php echo htmlspecialchars($player['employee_id']); ?></td>
                <td><?php echo htmlspecialchars($player['employee_name']); ?></td>
                <td><?php echo htmlspecialchars($player['player_type']); ?></td>
                <td><?php echo htmlspecialchars($player['points']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p>Total Points Used: <?php echo htmlspecialchars($totalPoints); ?></p>
    <p>Remaining Points: <?php echo htmlspecialchars($remainingPoints); ?></p>
    <a href="player_list.php">Edit Team</a>
    <script src="/js/scripts.js"></script>
</body>

</html>