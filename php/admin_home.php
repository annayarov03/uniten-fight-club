<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$admin_username = $_SESSION['admin_username'] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Home - UNITEN Fight Club</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin_home.css">
    <link rel="icon" type="image/png" href="../images/fight_club.png">
</head>
<body>

    <nav class="navbar">
    <div class="nav-logo">
        <a href="admin_home.php">
            <img src="../images/fight_club.png" alt="Fight Club Logo">
        </a>
    </div>

    <div class="nav-links">
        <a href="admin_home.php">Admin Home</a>
        <a class="active" href="admin_dashboard.php">Dashboard</a>
    </div>

    <div class="nav-user">
        <span class="nav-username">
            Admin: <?php echo htmlspecialchars($admin_username); ?>
        </span>
        <a class="btn-logout" href="admin_logout.php">Logout</a>
    </div>
</nav>


    <section class="admin-home-section">
        <div class="admin-home-container">
            <h1>Admin Control Center</h1>
            <p>Manage fighters, divisions, and tournament registrations for UNITEN's Fight Club.</p>
            <p>From here admins can review registered users, check division registrations, and prepare matchups.</p>

            <div class="admin-home-actions">
                <a href="admin_dashboard.php" class="btn-primary">Go to Dashboard</a>
                <a href="../index.html" class="btn-secondary">Back to Main Site</a>
            </div>
        </div>
    </section>

</body>
</html>
