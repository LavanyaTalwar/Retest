<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: /');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Dashboard</title>
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome to your dashboard, <?php echo htmlspecialchars($_SESSION['email']); ?></h2>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <br><a href="   add_player.php">Add Player</a>
        <?php endif; ?>
    </div>
    <a href="logout.php">Logout</a>
    <script src="/js/scripts.js"></script>
</body>
</html>
