<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

redirect_if_not_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    
    // File upload logic for thumbnail image securely
    $thumbnail_name = '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), $allowed)) {
            $thumbnail_name = time() . '_' . $_FILES['thumbnail']['name'];
            move_uploaded_file($_FILES['thumbnail']['tmp_temp_name'] ?? $_FILES['thumbnail']['tmp_name'], '../assets/images/' . $thumbnail_name);
        }
    }

    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO courses (title, slug, description, thumbnail, price, category_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $slug, $description, $thumbnail_name, $price, $category_id]);
        set_flash_message('success', 'Course uploaded successfully!');
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$courses = $pdo->query("SELECT c.*, cat.name as cat_name FROM courses c LEFT JOIN categories cat ON c.category_id = cat.id ORDER BY c.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses - SH_LearnifyV2</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        .admin-container { display: table; width: 100%; height: 100vh; }
        .sidebar { width: 20%; display: table-cell; background: #1c1d1f; color: #fff; vertical-align: top; padding: 20px; }
        .sidebar a { display: block; color: #d1d7dc; padding: 12px; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .sidebar a:hover { background: #3e4145; color: #fff; }
        .main-content { width: 80%; display: table-cell; vertical-align: top; padding: 40px; background: #f4f6f9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th, td { padding: 12px; border: 1px solid #d1d7dc; text-align: left; font-size:14px; }
        th { background: #f7f9fa; }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="sidebar">
        <h3 style="color:#a435f0; margin-bottom:30px;">SH_Learnify Panel</h3>
        <a href="dashboard.php">Dashboard</a>
        <a href="categories.php">Manage Categories</a>
        <a href="courses.php" style="background:#3e4145; font-weight:bold;">Manage Courses</a>
        <a href="lessons.php">Manage Lessons</a>
        <a href="users.php">Manage Users</a>
        <a href="<?php echo BASE_URL; ?>logout.php" style="color:#ff5252; margin-top:50px;">Logout</a>
    </div>

    <div class="main-content">
        <h1 style="font-size:24px; margin-bottom:20px;">Courses Directory</h1>
        <?php display_flash_message(); ?>

        <div style="background:#fff; padding:25px; border-radius:6px; margin-bottom:30px;">
            <h3>Create New Course Module</h3><br>
            <form method="POST" enctype="multipart/form-data">
                <div style="margin-bottom:12px;">
                    <input type="text" name="title" placeholder="Course Title" required style="width:100%; padding:10px; border:1px solid #d1d7dc;">
                </div>
                <div style="margin-bottom:12px;">
                    <textarea name="description" placeholder="Course Description" rows="4" style="width:100%; padding:10px; border:1px solid #d1d7dc; font-family:inherit;"></textarea>
                </div>
                <div style="overflow:hidden; margin-bottom:12px;">
                    <div style="width:32%; float:left; margin-right:2%;">
                        <input type="number" step="0.01" name="price" placeholder="Price (0 for free)" required style="width:100%; padding:10px; border:1px solid #d1d7dc;">
                    </div>
                    <div style="width:32%; float:left; margin-right:2%;">
                        <select name="category_id" required style="width:100%; padding:10px; border:1px solid #d1d7dc; background:#fff;">
                            <option value="">Select Category</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="width:32%; float:left;">
                        <input type="file" name="thumbnail" style="width:100%; padding:8px; border:1px solid #d1d7dc; background:#fff;">
                    </div>
                </div>
                <button type="submit" name="add_course" style="background:#a435f0; color:#fff; padding:12px 24px; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">Publish Course</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Thumbnail</th>
                    <th>Course Title</th>
                    <th>Category</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($courses as $c): ?>
                <tr>
                    <td><img src="<?php echo !empty($c['thumbnail']) ? BASE_URL.'assets/images/'.$c['thumbnail'] : 'https://via.placeholder.com/60x30'; ?>" style="width:60px; height:35px; object-fit:cover; border-radius:2px;"></td>
                    <td><strong><?php echo htmlspecialchars($c['title']); ?></strong></td>
                    <td><?php echo htmlspecialchars($c['cat_name'] ?? 'Uncategorized'); ?></td>
                    <td><?php echo $c['price'] > 0 ? '$'.number_format($c['price'], 2) : 'Free'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
