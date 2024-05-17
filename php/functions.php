<?php
require_once 'config.php';

function get_db_connection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function get_user_by_email($email) {
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $result;
}
function add_player($employee_id, $employee_name, $player_type, $points) {
    echo $player_type;
    $valid_player_types = array('batsman', 'bowler', 'all_rounder');
    if (!in_array($player_type, $valid_player_types)) {
        return false;
    }

    $conn = get_db_connection();
    $stmt = $conn->prepare("INSERT INTO player_details (employee_id, employee_name, player_type, points) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $employee_id, $employee_name, $player_type, $points);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

function get_all_players() {
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT * FROM player_details");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $result;
}

function get_player_by_id($player_id) {
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT * FROM player_details WHERE employee_id = ?");
    $stmt->bind_param("i", $player_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $result;
}

function get_user_team($user_email) {
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT player_ids FROM user_teams WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $result;
}

function save_user_team($user_email, $selected_player_ids) {
    $conn = get_db_connection();
    
    // Check if user already has a team saved
    $stmt = $conn->prepare("SELECT id FROM user_teams WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $team_exists = $result->num_rows > 0;
    $stmt->close();

    $player_ids_json = json_encode($selected_player_ids);

    if ($team_exists) {
        // Update existing team
        $stmt = $conn->prepare("UPDATE user_teams SET player_ids = ? WHERE email = ?");
        $stmt->bind_param("ss", $player_ids_json, $user_email);
    } else {
        // Insert new team
        $stmt = $conn->prepare("INSERT INTO user_teams (email, player_ids) VALUES (?, ?)");
        $stmt->bind_param("ss", $user_email, $player_ids_json);
    }
    
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}





