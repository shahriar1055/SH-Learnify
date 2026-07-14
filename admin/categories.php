<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

redirect_if_not_admin();

// Form processing for insertion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        $stmt->execute([$name, $slug]);
        set_flash_message('success', 'Category added successfully!');
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories - SH_LearnifyV2</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="dashboard-style-helper.css" style="display:none;"> <style>
        .admin-container { display: table; width: 100%; height: 100vh; }
        .sidebar { width: 20%; display: table-cell; background: #1c1d1f; color: #fff; vertical-align: top; padding: 20px; }
        .sidebar a { display: block; color: #d1d7dc; padding: 12px; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .sidebar a:hover { background: #3e4145; color: #fff; }
        .main-content { width: 80%; display: table-cell; vertical-align: top; padding: 40px; background: #f4f6f9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th, td { padding: 12px; border: 1px solid #d1d7dc; text-align: left; }
        th { background: #f7f9fa; }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="sidebar">
        <h3 style="color:#a435f0; margin-bottom:30px;">SH_Learnify Panel</h3>
        <a href="dashboard.php">Dashboard</a>
        <a href="categories.php" style="background:#3e4145; font-weight:bold;">Manage Categories</a>
        <a href="courses.php">Manage Courses</a>
        <a href="lessons.php">Manage Lessons</a>
        <a href="users.php">Manage Users</a>
        <a href="<?php echo BASE_URL; ?>logout.php" style="color:#ff5252; margin-top:50px;">Logout</a>
    </div>

    <div class="main-content">
        <h1 style="font-size:24px; margin-bottom:20px;">Course Categories</h1>
        <?php display_flash_message(); ?>

        <div style="background:#fff; padding:20px; border-radius:6px; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom:30px;">
            <form method="POST" style="overflow:hidden;">
                <div style="width:70%; float:left; padding-right:20px;">
                    <input type="text" name="name" placeholder="Category Name (e.g., Web Development)" required style="width:100%; padding:11px; border:1px solid #d1d7dc; border-radius:4px;">
                </div>
                <div style="width:30%; float:left;">
                    <button type="submit" name="add_category" style="width:100%; padding:11px; background:#a435f0; color:#fff; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">Add Category</button>
                </div>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Slug URL Reference</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($categories as $cat): ?>
                <tr>
                    <td><?php echo $cat['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
