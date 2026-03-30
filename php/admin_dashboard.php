<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$mysqli = mysqli_connect("localhost", "root", "", "mma_tournament");
if (!$mysqli) {
    die("Database connection failed: " . mysqli_connect_error());
}

// DELETE handlers
if (isset($_GET['delete_user'])) {
    $uid = (int) $_GET['delete_user'];
    mysqli_query($mysqli, "DELETE FROM users WHERE user_id='$uid'");
}

if (isset($_GET['delete_juvenile'])) {
    $jid = (int) $_GET['delete_juvenile'];
    mysqli_query($mysqli, "DELETE FROM juvenile WHERE juvenile_id='$jid'");
}

if (isset($_GET['delete_adult'])) {
    $aid = (int) $_GET['delete_adult'];
    mysqli_query($mysqli, "DELETE FROM adult WHERE adult_id='$aid'");
}

// DATA QUERIES
$users_q = "SELECT user_id, username, first_name, last_name, email FROM users ORDER BY user_id";
$users_r = mysqli_query($mysqli, $users_q);

$juv_q = "SELECT j.juvenile_id, u.username, j.height_cm, j.weight_class, j.level
          FROM juvenile j
          JOIN users u ON j.user_id = u.user_id
          ORDER BY u.username";
$juv_r = mysqli_query($mysqli, $juv_q);

$adult_q = "SELECT a.adult_id, u.username, a.height_cm, a.weight_class, a.level
            FROM adult a
            JOIN users u ON a.user_id = u.user_id
            ORDER BY u.username";
$adult_r = mysqli_query($mysqli, $adult_q);

$admin_username = $_SESSION['admin_username'] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - UNITEN Fight Club</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin_dash.css">
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

    <section class="admin-dashboard-section">
        <div class="admin-dashboard-container">

            <h1>Admin Dashboard</h1>
            <p>Overview of registered users and division registrations.</p>

            <!-- Registered Users -->
            <div class="admin-card">
                <h2>Registered Users</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($users_r && mysqli_num_rows($users_r) > 0): ?>
                        <?php while ($u = mysqli_fetch_assoc($users_r)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($u['username']); ?></td>
                                <td><?php echo htmlspecialchars($u['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($u['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td>
                                    <a class="btn-danger"
                                       href="admin_dashboard.php?delete_user=<?php echo (int)$u['user_id']; ?>"
                                       onclick="return confirm('Delete this user? This will remove their divisions too.');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No users found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Juvenile Divisions -->
            <div class="admin-card">
                <h2>Juvenile Divisions</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Height (cm)</th>
                            <th>Weight Class</th>
                            <th>Level</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($juv_r && mysqli_num_rows($juv_r) > 0): ?>
                        <?php while ($j = mysqli_fetch_assoc($juv_r)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($j['username']); ?></td>
                                <td><?php echo htmlspecialchars($j['height_cm']); ?></td>
                                <td><?php echo htmlspecialchars($j['weight_class']); ?></td>
                                <td><?php echo htmlspecialchars($j['level']); ?></td>
                                <td>
                                    <a class="btn-danger"
                                       href="admin_dashboard.php?delete_juvenile=<?php echo (int)$j['juvenile_id']; ?>"
                                       onclick="return confirm('Delete this juvenile division record?');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No juvenile divisions found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Adult Divisions -->
            <div class="admin-card">
                <h2>Adult Divisions</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Height (cm)</th>
                            <th>Weight Class</th>
                            <th>Level</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($adult_r && mysqli_num_rows($adult_r) > 0): ?>
                        <?php while ($a = mysqli_fetch_assoc($adult_r)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($a['username']); ?></td>
                                <td><?php echo htmlspecialchars($a['height_cm']); ?></td>
                                <td><?php echo htmlspecialchars($a['weight_class']); ?></td>
                                <td><?php echo htmlspecialchars($a['level']); ?></td>
                                <td>
                                    <a class="btn-danger"
                                       href="admin_dashboard.php?delete_adult=<?php echo (int)$a['adult_id']; ?>"
                                       onclick="return confirm('Delete this adult division record?');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No adult divisions found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <p class="admin-extra">
                <a href="admin_home.php">Back to Admin Home</a>
            </p>

        </div>
    </section>

</body>
</html>
