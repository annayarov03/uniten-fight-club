<?php
session_start();

$mysqli = mysqli_connect("localhost", "root", "", "mma_tournament");
if (!$mysqli) {
    die("Database connection failed: " . mysqli_connect_error());
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? "";
    $password = $_POST['password'] ?? "";
    $confirm  = $_POST['confirm_password'] ?? "";
    $first    = $_POST['first_name'] ?? "";
    $last     = $_POST['last_name'] ?? "";
    $email    = $_POST['email'] ?? "";

    if ($username == "" || $password == "" || $confirm == "" || $first == "" || $last == "" || $email == "") {
        $error = "Please fill in all fields.";
    } elseif ($password != $confirm) {
        $error = "Passwords do not match.";
    } else {

        $username = mysqli_real_escape_string($mysqli, $username);
        $password = mysqli_real_escape_string($mysqli, $password);
        $first    = mysqli_real_escape_string($mysqli, $first);
        $last     = mysqli_real_escape_string($mysqli, $last);
        $email    = mysqli_real_escape_string($mysqli, $email);

        $checkQuery = "SELECT * FROM users WHERE username='$username' OR email='$email'";
        $checkResult = mysqli_query($mysqli, $checkQuery);

        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            $error = "Username or email already taken.";
        } else {
            $insertQuery = "INSERT INTO users (username, password, first_name, last_name, email)
                            VALUES ('$username','$password','$first','$last','$email')";
            if (mysqli_query($mysqli, $insertQuery)) {
                $success = "Registration successful. You may login.";
            } else {
                $error = "Error saving data.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration - UNITEN Fight Club</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/register.css">
    <link rel="icon" type="image/png" href="../images/fight_club.png">
    
</head>
<body>

   

    <nav class="navbar">
        <img src="../images/fight_club.png" alt="logo">
            <div class="nav-links">
                <a href="../index.html">About</a>
                <a href="../index.html#event">Events</a>
                <a href="../index.html">Home</a>
                <a href="../index.html#contact">Contact</a>
            </div>
        <div class="auth-buttons">
                <a href="user_login.php">Login</a>
                <a href="user_register.php">Register</a>
                <a href="admin_login.php">Admin Login</a>
        </div>
    </nav>

    <section class="auth-section">
    <div class="auth-card">
        <h1>Create Your Fighter Account</h1>
        <p class="auth-subtitle">
            Register as a user to join UNITEN's Fight Club tournaments.
        </p>

        <?php
        if ($error != "") {
            echo "<p class=\"error-message\">" . htmlspecialchars($error) . "</p>";
        }
        if ($success != "") {
            echo "<p class=\"success-message\">" . htmlspecialchars($success) . "</p>";
        }
        ?>

        <form class="auth-form" method="post" action="user_register.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   value="<?php if (isset($username)) echo htmlspecialchars($username); ?>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password">

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password">

            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name"
                   value="<?php if (isset($first)) echo htmlspecialchars($first); ?>">

            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name"
                   value="<?php if (isset($last)) echo htmlspecialchars($last); ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="<?php if (isset($email)) echo htmlspecialchars($email); ?>">

            <input type="submit" value="Register" class="btn-primary">
        </form>

        <p class="auth-footer">
            Already have an account?
            <a href="user_login.php">Login</a>
        </p>

        <p class="auth-footer">
            <a href="../index.html">Back to Home</a>
        </p>
    </div>
</section>


</body>
</html>
