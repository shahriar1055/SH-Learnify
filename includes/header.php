<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isAdminPanel = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdminPanel ? '../' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SH Learnify</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">
    <script defer src="<?php echo $basePath; ?>assets/js/app.js"></script>
</head>
<body>

<header class="site-header">
    <div class="container nav-wrapper">
        <a href="<?php echo $basePath; ?>index.php" class="logo">SH <span>Learnify</span></a>

        <button class="menu-toggle" id="menuToggle">☰</button>

        <nav class="nav" id="navMenu">
            <?php if($isAdminPanel): ?>
                <a href="../index.php">Home</a>
                <a href="dashboard.php">Admin Dashboard</a>
                <a href="categories.php">Categories</a>
                <a href="courses.php">Courses</a>
                <a href="lessons.php">Lessons</a>
                <a href="users.php">Users</a>
                <a href="contacts.php">Contacts</a>
                <a href="../logout.php" class="btn btn-sm">Logout</a>
            <?php else: ?>
                <a href="index.php">Home</a>
                <a href="courses.php">Courses</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="profile.php">Profile</a>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <a href="admin/dashboard.php">Admin</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-sm">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm">Login</a>
                    <a href="register.php" class="btn btn-primary btn-sm">Register</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>
    </div>
</header>