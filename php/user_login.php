<?php
session_start();

$mysqli = mysqli_connect("localhost", "root", "", "mma_tournament");
if (!$mysqli) {
    die("Database connection failed: " . mysqli_connect_error());
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? "";
    $password = $_POST['password'] ?? "";

    if ($username == "" || $password == "") {
        $error = "Please fill in all fields.";
    } else {

        $username = mysqli_real_escape_string($mysqli, $username);
        $password = mysqli_real_escape_string($mysqli, $password);

        $sql = "SELECT user_id, password FROM users WHERE username='$username'";
        $result = mysqli_query($mysqli, $sql);

        if ($result && mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            // plain-text password as in mma_tournament.sql
            if ($password == $row['password']) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $username;

                header("Location: user_home.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "User not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Login - UNITEN Fight Club</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/user_login.css">
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
            <a href="../index.html">Home</a>
            <a href="user_register.php">Register</a>
            <a href="admin_login.php">Admin Login</a>
            <a class="active" href="user_login.php">User Login</a>
        </div>
    </nav>

    <section class="login-section">
        <div class="login-container">
            <h1>User Login</h1>
            <p>Login to manage your profile and tournament registrations.</p>

            <?php
            if ($error != "") {
                echo "<p class=\"error-message\">" . htmlspecialchars($error) . "</p>";
            }
            ?>

            <form class="login-form" method="post" action="user_login.php">
                <label for="username">Username</label><br>
                <input type="text" name="username" id="username"
                       value="<?php if (isset($username)) echo htmlspecialchars($username); ?>"><br><br>

                <label for="password">Password</label><br>
                <input type="password" name="password" id="password"><br><br>

                <input type="submit" value="Login" class="btn-primary">
            </form>

            <p class="login-extra">
                Don't have an account?
                <a href="user_register.php">Register</a>
            </p>

            <p class="login-extra">
                <a href="../index.html">Back to Home</a>
            </p>
        </div>
    </section>

</body>
</html>
