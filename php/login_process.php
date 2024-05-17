<?php
/**
 * @file
 * UserAuthentication class handles user authentication.
 */

// Start session.
session_start();

require_once 'functions.php';

class UserAuthentication {
    /**
     * Authenticates user.
     *
     * @param string $email
     *   The email provided by the user.
     * @param string $password
     *   The password provided by the user.
     *
     * @return bool
     *   TRUE if authentication is successful, FALSE otherwise.
     */
    public function authenticateUser(string $email, string $password): bool {
        // Get user details by email.
        $user = get_user_by_email($email);

        // Check if user exists and password matches.
        if ($user && $password === $user['password']) {
            // Set session variables.
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $user['role'];
            return true;
        }

        return false;
    }
}

// Check if the request method is POST.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get email and password from the POST data.
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Create a new instance of the UserAuthentication class.
    $authenticator = new UserAuthentication();

    // Authenticate the user.
    if ($authenticator->authenticateUser($email, $password)) {
        // Redirect based on user role.
        if ($_SESSION['role'] == 'admin') {
            header('Location: dashboard.php');
        } else {
            header('Location: player_list.php');
        }
        exit();
    } else {
        // Invalid credentials.
        echo "Invalid credentials";
    }
} else {
    // Redirect to home page if not a POST request.
    header('Location:/');
    exit();
}
?>