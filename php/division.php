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

$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? "";

$error_division   = "";
$success_division = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $age_class    = $_POST['age_class'] ?? "";
    $height       = $_POST['height_cm'] ?? "";
    $weight_class = $_POST['weight_class'] ?? "";
    $level        = $_POST['level'] ?? "";

    if ($age_class == "" || $height == "" || $weight_class == "" || $level == "") {
        $error_division = "Please complete all fields.";
    } else {

        $age_class    = mysqli_real_escape_string($mysqli, $age_class);
        $height       = mysqli_real_escape_string($mysqli, $height);
        $weight_class = mysqli_real_escape_string($mysqli, $weight_class);
        $level        = mysqli_real_escape_string($mysqli, $level);

        if ($age_class == "juvenile") {

            mysqli_query($mysqli, "DELETE FROM adult WHERE user_id='$user_id'");

            $check = mysqli_query($mysqli, "SELECT * FROM juvenile WHERE user_id='$user_id'");
            if ($check && mysqli_num_rows($check) > 0) {
                $update = "UPDATE juvenile
                           SET height_cm='$height', weight_class='$weight_class', level='$level'
                           WHERE user_id='$user_id'";
                mysqli_query($mysqli, $update);
            } else {
                $insert = "INSERT INTO juvenile (user_id, height_cm, weight_class, level)
                           VALUES ('$user_id','$height','$weight_class','$level')";
                mysqli_query($mysqli, $insert);
            }

            $success_division = "Division saved (Juvenile).";

        } else { // adult

            mysqli_query($mysqli, "DELETE FROM juvenile WHERE user_id='$user_id'");

            $check = mysqli_query($mysqli, "SELECT * FROM adult WHERE user_id='$user_id'");
            if ($check && mysqli_num_rows($check) > 0) {
                $update = "UPDATE adult
                           SET height_cm='$height', weight_class='$weight_class', level='$level'
                           WHERE user_id='$user_id'";
                mysqli_query($mysqli, $update);
            } else {
                $insert = "INSERT INTO adult (user_id, height_cm, weight_class, level)
                           VALUES ('$user_id','$height','$weight_class','$level')";
                mysqli_query($mysqli, $insert);
            }

            $success_division = "Division saved (Adult).";
        }
    }
}

// load current division
$current_age   = "";
$current_h     = "";
$current_w     = "";
$current_level = "";

$d = mysqli_query($mysqli, "SELECT * FROM juvenile WHERE user_id='$user_id'");
if ($d && mysqli_num_rows($d) > 0) {
    $current_age = "juvenile";
    $row = mysqli_fetch_assoc($d);
    $current_h     = $row['height_cm'];
    $current_w     = $row['weight_class'];
    $current_level = $row['level'];
} else {
    $d = mysqli_query($mysqli, "SELECT * FROM adult WHERE user_id='$user_id'");
    if ($d && mysqli_num_rows($d) > 0) {
        $current_age = "adult";
        $row = mysqli_fetch_assoc($d);
        $current_h     = $row['height_cm'];
        $current_w     = $row['weight_class'];
        $current_level = $row['level'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Division Registration - UNITEN Fight Club</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/division.css">
    <link rel="icon" type="image/png" href="../images/fight_club.png">
</head>
<body>

    <nav class="navbar">
        <div class="nav-logo">
            <a href="user_home.php">
                <img src="../images/fight_club.png" alt="Fight Club Logo">
            </a>
        </div>
        <div class="nav-links">
            <a href="user_home.php">Home</a>
            <a class="active" href="division.php">Divisions</a>
            <a href="profile.php">My Profile</a>
        </div>
        <div class="nav-user">
            <span class="nav-username">
                Logged in user: <?php echo htmlspecialchars($username); ?>
            </span>
            <a class="btn-logout" href="user_logout.php">Logout</a>
        </div>
    </nav>

    <section class="division-section">
        <div class="division-container">
            <h1>Register for a Division</h1>
            <p>Select your age group and fill in your details.</p>

            <?php
            if ($error_division != "") {
                echo "<p class=\"error-message\">" . htmlspecialchars($error_division) . "</p>";
            }
            if ($success_division != "") {
                echo "<p class=\"success-message\">" . htmlspecialchars($success_division) . "</p>";
            }
            ?>

            <form class="division-form" method="post" action="division.php">

                <label for="age_class">Age Class</label><br>
                <select id="age_class" name="age_class">
                    <option value="">Select</option>
                    <option value="juvenile" <?php if ($current_age == "juvenile") echo "selected"; ?>>Juvenile(14years-17years)</option>
                    <option value="adult" <?php if ($current_age == "adult") echo "selected"; ?>>Adult(18+)</option>
                </select><br><br>

                <label for="height_cm">Height (cm)</label><br>
                <input type="number" step="0.01" id="height_cm" name="height_cm"
                       value="<?php echo htmlspecialchars($current_h); ?>"><br><br>

                <label for="weight_class">Weight Class</label><br>
                <select id="weight_class" name="weight_class">
                    <option value="">Select</option>
                    <option value="Flyweight (up to 56 kg)" <?php if ($current_w == "Flyweight (up to 56 kg)") echo "selected"; ?>>Flyweight (up to 56 kg)</option>
                    <option value="Bantamweight (57-61 kg)" <?php if ($current_w == "Bantamweight (57-61 kg)") echo "selected"; ?>>Bantamweight (57-61 kg)</option>
                    <option value="Featherweight (62-66 kg)" <?php if ($current_w == "Featherweight (62-66 kg)") echo "selected"; ?>>Featherweight (62-66 kg)</option>
                    <option value="Lightweight (67-70 kg)" <?php if ($current_w == "Lightweight (67-70 kg)") echo "selected"; ?>>Lightweight (67-70 kg)</option>
                    <option value="Welterweight (71-77 kg)" <?php if ($current_w == "Welterweight (71-77 kg)") echo "selected"; ?>>Welterweight (71-77 kg)</option>
                    <option value="Middleweight (78-84 kg)" <?php if ($current_w == "Middleweight (78-84 kg)") echo "selected"; ?>>Middleweight (78-84 kg)</option>
                    <option value="Light Heavyweight (85-93 kg)" <?php if ($current_w == "Light Heavyweight (85-93 kg)") echo "selected"; ?>>Light Heavyweight (85-93 kg)</option>
                    <option value="Heavyweight (94-120 kg)" <?php if ($current_w == "Heavyweight (94-120 kg)") echo "selected"; ?>>Heavyweight (94-120 kg)</option>
                </select><br><br>

                <label for="level">Level</label><br>
                <select id="level" name="level">
                    <option value="">Select</option>
                    <option value="beginner" <?php if ($current_level == "beginner") echo "selected"; ?>>Beginner</option>
                    <option value="amateur" <?php if ($current_level == "amateur") echo "selected"; ?>>Amateur</option>
                    <option value="pro" <?php if ($current_level == "pro") echo "selected"; ?>>Pro Fighter</option>
                </select><br><br>

                <input type="submit" value="Save Division" class="btn-primary">
            </form>

            <p class="division-extra">
                <a href="user_home.php">Back to Home</a>
            </p>

        </div>
    </section>

</body>
</html>
