<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$mysqli = mysqli_connect("localhost", "root", "", "mma_tournament");
if (!$mysqli) {
    die("Database connection failed: " . mysqli_connect_error());
}

$username = $_SESSION['username'] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Home - UNITEN Fight Club</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/user_home.css">
    <link rel="icon" type="image/png" href="../images/fight_club.png">
</head>
<body>

    <nav class="navbar">
        <div class="nav-logo">
            <a href="../index.html">
                <img src="../images/fight_club.png" alt="Fight Club Logo">
            </a>
        </div>
        <div class="nav-links">
            <a class="active" href="user_home.php">Home</a>
            <a href="division.php">Divisions</a>
            <a href="profile.php">My Profile</a>
        </div>
        <div class="nav-user">
            <span class="nav-username">
                Logged in user:
                <?php echo htmlspecialchars($username); ?>
            </span>
            <a class="btn-logout" href="user_logout.php">Logout</a>
        </div>
    </nav>


    <section class="user-home-section">
    <div class="user-home-container">

        <div class="welcome-block">
            <img src="../images/fight_club.png" alt="Fight Club Logo" class="welcome-logo">

            <h1>Welcome to UNITEN's Fight Club</h1>
            <p>Register, manage your divisions, and get ready for the next MMA tournament.</p>
        </div>

        <div class="user-home-actions">
            <a href="division.php" class="btn-primary">Register / Edit Division</a>
            <a href="profile.php" class="btn-secondary">View My Profile</a>
        </div>

    </div>
</section>

</body>
</html>
