<?php

/**
 * @file
 * AddPlayer class handles adding players by admin.
 */

// Start session.
session_start();

class AddPlayer {
  /**
   * Add a new player.
   *
   * @param int $employee_id
   *   The employee ID of the player.
   * @param string $employee_name
   *   The name of the player.
   * @param string $type
   *   The type of the player (batsman, bowler, allrounder).
   * @param int $points
   *   The points allocated to the player.
   *
   * @return bool
   *   TRUE if player added successfully, FALSE otherwise.
   */
  public function addNewPlayer(int $employee_id, string $employee_name, string $type, int $points): bool {
    // Check if points are within the allowed range.
    if ($points < 2 || $points > 10) {
      echo "Points should be between 2 and 10";
      return false;
    }

    // Include the add_player function.
    require_once 'functions.php';

    // Call the add_player function.
    if (add_player($employee_id, $employee_name, $type, $points)) {
      echo "Player added successfully!";
      return true;
    } else {
      echo "Failed to add player";
      return false;
    }
  }
}

// Check if the user is authorized as admin.
if ($_SESSION['role'] !== 'admin') {
  echo "Unauthorized access";
  exit();
}

// Check if the request method is POST.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get input data from POST.
  $employee_id = $_POST['employee_id'];
  $employee_name = $_POST['employee_name'];
  $type = $_POST['type'];
  $points = $_POST['points'];

  // Create a new instance of the AddPlayer class.
  $playerAdd = new AddPlayer();

  // Add the player.
  $playerAdd->addNewPlayer($employee_id, $employee_name, $type, $points);
} else {
  echo "Invalid request";
}