<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireAdmin();

include '../includes/header.php';

$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'] ?? 0;
$totalStudents = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='student'")->fetch_assoc()['total'] ?? 0;
$totalCourses = $conn->query("SELECT COUNT(*) as total FROM courses")->fetch_assoc()['total'] ?? 0;
$totalCategories = $conn->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc()['total'] ?? 0;
$totalLessons = $conn->query("SELECT COUNT(*) as total FROM lessons")->fetch_assoc()['total'] ?? 0;
$totalContacts = $conn->query("SELECT COUNT(*) as total FROM contacts")->fetch_assoc()['total'] ?? 0;
?>

<section class="page-banner">
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Manage the SH Learnify platform from one place.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Total Users</h3>
                <p><?php echo $totalUsers; ?></p>
            </div>

            <div class="dashboard-card">
                <h3>Total Students</h3>
                <p><?php echo $totalStudents; ?></p>
            </div>

            <div class="dashboard-card">
                <h3>Total Courses</h3>
                <p><?php echo $totalCourses; ?></p>
            </div>

            <div class="dashboard-card">
                <h3>Total Categories</h3>
                <p><?php echo $totalCategories; ?></p>
            </div>

            <div class="dashboard-card">
                <h3>Total Lessons</h3>
                <p><?php echo $totalLessons; ?></p>
            </div>

            <div class="dashboard-card">
                <h3>Contact Messages</h3>
                <p><?php echo $totalContacts; ?></p>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>