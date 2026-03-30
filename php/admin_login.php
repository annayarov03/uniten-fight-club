<?php
session_start();

$mysqli = mysqli_connect("localhost", "root", "", "mma_tournament");
if (!$mysqli) {
    die("Database connection failed: " . mysqli_connect_error());
}

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? "";
    $password = $_POST['password'] ?? "";

    if ($username == "" || $password == "") {
        $error_msg = "Please fill in all fields.";
    } else {

        $username = mysqli_real_escape_string($mysqli, $username);
        $password = mysqli_real_escape_string($mysqli, $password);

        $sql = "SELECT admin_id, password FROM admin WHERE username='$username'";
        $result = mysqli_query($mysqli, $sql);

        if ($result && mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            
            if ($password == $row['password']) {
                $_SESSION['admin_id'] = $row['admin_id'];
                $_SESSION['admin_username'] = $username;

                header("Location: admin_home.php");
                exit;
            } else {
                $error_msg = "Incorrect password.";
            }
        } else {
            $error_msg = "Admin user not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - UNITEN Fight Club</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin_login.css">
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
            <a href="user_login.php">User Login</a>
            <a class="active" href="admin_login.php">Admin Login</a>
        </div>
    </nav>

    <section class="login-section">
        <div class="login-container">
            <h1>Admin Login</h1>
            <p>Login to manage categories and participants.</p>

            <?php
            if ($error_msg != "") {
                echo "<p class=\"error-message\">" . htmlspecialchars($error_msg) . "</p>";
            }
            ?>

            <form class="login-form" method="post" action="admin_login.php">
                <label for="username">Username</label><br>
                <input type="text" name="username" id="username"
                       value="<?php if (isset($username)) echo htmlspecialchars($username); ?>"><br><br>

                <label for="password">Password</label><br>
                <input type="password" name="password" id="password"><br><br>

                <input type="submit" value="Login" class="btn-primary">
            </form>

            <p class="login-extra">
                <a href="../index.html">Back to Home</a>
            </p>
        </div>
    </section>

</body>
</html>
