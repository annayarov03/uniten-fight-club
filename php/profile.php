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

// Messages
$error_profile    = "";
$success_profile  = "";
$error_division   = "";
$success_division = "";
$error_password   = "";
$success_password = "";

// ---------- UPDATE BASIC PROFILE ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    $first = $_POST['first_name'] ?? "";
    $last  = $_POST['last_name'] ?? "";
    $email = $_POST['email'] ?? "";

    if ($first == "" || $last == "" || $email == "") {
        $error_profile = "Please fill in all profile fields.";
    } else {
        $first = mysqli_real_escape_string($mysqli, $first);
        $last  = mysqli_real_escape_string($mysqli, $last);
        $email = mysqli_real_escape_string($mysqli, $email);

        $update = "UPDATE users
                   SET first_name='$first', last_name='$last', email='$email'
                   WHERE user_id='$user_id'";
        if (mysqli_query($mysqli, $update)) {
            $success_profile = "Profile updated successfully.";
        } else {
            $error_profile = "Error updating profile.";
        }
    }
}

// ---------- CHANGE PASSWORD ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {

    $old_pass = $_POST['old_password'] ?? "";
    $new_pass = $_POST['new_password'] ?? "";
    $conf_pass = $_POST['confirm_password'] ?? "";

    if ($old_pass == "" || $new_pass == "" || $conf_pass == "") {
        $error_password = "Please fill in all password fields.";
    } elseif ($new_pass != $conf_pass) {
        $error_password = "New passwords do not match.";
    } else {

        $old_pass = mysqli_real_escape_string($mysqli, $old_pass);
        $new_pass = mysqli_real_escape_string($mysqli, $new_pass);

        $q = "SELECT password FROM users WHERE user_id='$user_id'";
        $r = mysqli_query($mysqli, $q);

        if ($r && mysqli_num_rows($r) == 1) {
            $row = mysqli_fetch_assoc($r);

            // plain text password check (same as your DB dump)
            if ($old_pass != $row['password']) {
                $error_password = "Old password is incorrect.";
            } else {
                $u = "UPDATE users SET password='$new_pass' WHERE user_id='$user_id'";
                if (mysqli_query($mysqli, $u)) {
                    $success_password = "Password changed successfully.";
                } else {
                    $error_password = "Error updating password.";
                }
            }
        } else {
            $error_password = "User not found.";
        }
    }
}

// ---------- UPDATE DIVISION ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_division'])) {

    $age_class    = $_POST['age_class'] ?? "";
    $height       = $_POST['height_cm'] ?? "";
    $weight_class = $_POST['weight_class'] ?? "";
    $level        = $_POST['level'] ?? "";

    if ($age_class == "" || $height == "" || $weight_class == "" || $level == "") {
        $error_division = "Please complete all division fields.";
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

        } else {

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
        }

        $success_division = "Division updated!";
    }
}

// ---------- LOAD CURRENT PROFILE ----------
$user_sql = "SELECT first_name, last_name, email FROM users WHERE user_id='$user_id'";
$user_res = mysqli_query($mysqli, $user_sql);
$user_row = $user_res ? mysqli_fetch_assoc($user_res) : null;

$first_val = $user_row['first_name'] ?? "";
$last_val  = $user_row['last_name'] ?? "";
$email_val = $user_row['email'] ?? "";

// ---------- LOAD CURRENT DIVISION ----------
$age_class = "";
$height_val = "";
$weight_val = "";
$level_val = "";

$division = mysqli_query($mysqli, "SELECT * FROM juvenile WHERE user_id='$user_id'");
if ($division && mysqli_num_rows($division) > 0) {
    $age_class = "juvenile";
    $div_data = mysqli_fetch_assoc($division);
    $height_val = $div_data["height_cm"] ?? "";
    $weight_val = $div_data["weight_class"] ?? "";
    $level_val  = $div_data["level"] ?? "";
} else {
    $division = mysqli_query($mysqli, "SELECT * FROM adult WHERE user_id='$user_id'");
    if ($division && mysqli_num_rows($division) > 0) {
        $age_class = "adult";
        $div_data = mysqli_fetch_assoc($division);
        $height_val = $div_data["height_cm"] ?? "";
        $weight_val = $div_data["weight_class"] ?? "";
        $level_val  = $div_data["level"] ?? "";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - UNITEN Fight Club</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/pofile.css">
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
            <a href="division.php">Divisions</a>
            <a class="active" href="profile.php">My Profile</a>
        </div>
        <div class="nav-user">
            <span class="nav-username">
                Logged in user: <?php echo htmlspecialchars($username); ?>
            </span>
            <a class="btn-logout" href="user_logout.php">Logout</a>
        </div>
    </nav>

    <section class="profile-section">
        <div class="profile-container">

            <!-- Basic profile card -->
            <div class="profile-card">
                <h1>My Profile</h1>
                <p>View and update your account details.</p>

                <?php
                if ($error_profile != "") {
                    echo "<p class=\"error-message\">" . htmlspecialchars($error_profile) . "</p>";
                }
                if ($success_profile != "") {
                    echo "<p class=\"success-message\">" . htmlspecialchars($success_profile) . "</p>";
                }
                ?>

                <form class="profile-form" method="post" action="profile.php">
                    <input type="hidden" name="update_profile" value="1">

                    <label>Username</label><br>
                    <input type="text" value="<?php echo htmlspecialchars($username); ?>" disabled><br><br>

                    <label for="first_name">First Name</label><br>
                    <input type="text" id="first_name" name="first_name"
                           value="<?php echo htmlspecialchars($first_val); ?>"><br><br>

                    <label for="last_name">Last Name</label><br>
                    <input type="text" id="last_name" name="last_name"
                           value="<?php echo htmlspecialchars($last_val); ?>"><br><br>

                    <label for="email">Email</label><br>
                    <input type="email" id="email" name="email"
                           value="<?php echo htmlspecialchars($email_val); ?>"><br><br>

                    <input type="submit" value="Update Profile" class="btn-primary">
                </form>
            </div>

            <!-- Change password card -->
            <div class="profile-card">
                <h2>Change Password</h2>
                <p>Update your account password.</p>

                <?php
                if ($error_password != "") {
                    echo "<p class=\"error-message\">" . htmlspecialchars($error_password) . "</p>";
                }
                if ($success_password != "") {
                    echo "<p class=\"success-message\">" . htmlspecialchars($success_password) . "</p>";
                }
                ?>

                <form class="profile-form" method="post" action="profile.php">
                    <input type="hidden" name="change_password" value="1">

                    <label for="old_password">Old Password</label><br>
                    <input type="password" id="old_password" name="old_password"><br><br>

                    <label for="new_password">New Password</label><br>
                    <input type="password" id="new_password" name="new_password"><br><br>

                    <label for="confirm_password">Confirm New Password</label><br>
                    <input type="password" id="confirm_password" name="confirm_password"><br><br>

                    <input type="submit" value="Change Password" class="btn-primary">
                </form>
            </div>

            <!-- Division card -->
            <div class="profile-card">
                <h2>Division Information</h2>
                <p>Update your age class and stats for the tournament.</p>

                <?php
                if ($error_division != "") {
                    echo "<p class=\"error-message\">" . htmlspecialchars($error_division) . "</p>";
                }
                if ($success_division != "") {
                    echo "<p class=\"success-message\">" . htmlspecialchars($success_division) . "</p>";
                }
                ?>

                <form class="division-form" method="post" action="profile.php">
                    <input type="hidden" name="update_division" value="1">

                    <label for="age_class">Age Class</label><br>
                    <select id="age_class" name="age_class">
                        <option value="">Select</option>
                        <option value="juvenile" <?php if ($age_class == "juvenile") echo "selected"; ?>>Juvenile</option>
                        <option value="adult" <?php if ($age_class == "adult") echo "selected"; ?>>Adult</option>
                    </select><br><br>

                    <label for="height_cm">Height (cm)</label><br>
                    <input type="number" step="0.01" id="height_cm" name="height_cm"
                           value="<?php echo htmlspecialchars($height_val); ?>"><br><br>

                    <label for="weight_class">Weight Class</label><br>
                    <select id="weight_class" name="weight_class">
                        <option value="">Select</option>
                        <option value="Flyweight (up to 56 kg)" <?php if ($weight_val == "Flyweight (up to 56 kg)") echo "selected"; ?>>Flyweight (up to 56 kg)</option>
                        <option value="Bantamweight (57-61 kg)" <?php if ($weight_val == "Bantamweight (57-61 kg)") echo "selected"; ?>>Bantamweight (57-61 kg)</option>
                        <option value="Featherweight (62-66 kg)" <?php if ($weight_val == "Featherweight (62-66 kg)") echo "selected"; ?>>Featherweight (62-66 kg)</option>
                        <option value="Lightweight (67-70 kg)" <?php if ($weight_val == "Lightweight (67-70 kg)") echo "selected"; ?>>Lightweight (67-70 kg)</option>
                        <option value="Welterweight (71-77 kg)" <?php if ($weight_val == "Welterweight (71-77 kg)") echo "selected"; ?>>Welterweight (71-77 kg)</option>
                        <option value="Middleweight (78-84 kg)" <?php if ($weight_val == "Middleweight (78-84 kg)") echo "selected"; ?>>Middleweight (78-84 kg)</option>
                        <option value="Light Heavyweight (85-93 kg)" <?php if ($weight_val == "Light Heavyweight (85-93 kg)") echo "selected"; ?>>Light Heavyweight (85-93 kg)</option>
                        <option value="Heavyweight (94-120 kg)" <?php if ($weight_val == "Heavyweight (94-120 kg)") echo "selected"; ?>>Heavyweight (94-120 kg)</option>
                    </select><br><br>

                    <label for="level">Level</label><br>
                    <select id="level" name="level">
                        <option value="">Select</option>
                        <option value="beginner" <?php if ($level_val == "beginner") echo "selected"; ?>>Beginner</option>
                        <option value="amateur" <?php if ($level_val == "amateur") echo "selected"; ?>>Amateur</option>
                        <option value="pro" <?php if ($level_val == "pro") echo "selected"; ?>>Pro Fighter</option>
                    </select><br><br>

                    <input type="submit" value="Update Division" class="btn-primary">
                </form>
            </div>

        </div>
    </section>

</body>
</html>
