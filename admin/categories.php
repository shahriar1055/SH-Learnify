<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireAdmin();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? '📘');

    if ($name !== '' && $slug !== '') {
        $stmt = $conn->prepare("INSERT INTO categories(name, slug, description, icon) VALUES(?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $slug, $description, $icon);

        if ($stmt->execute()) {
            $message = "Category added successfully.";
        } else {
            $message = "Failed to add category.";
        }
    } else {
        $message = "Name and slug are required.";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM categories WHERE id=$id");
    header("Location: categories.php");
    exit();
}

$categories = $conn->query("SELECT * FROM categories ORDER BY id DESC");

include '../includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>Manage Categories</h1>
        <p>Add, view and delete platform categories.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if($message): ?>
            <div class="info-card mb-4"><p><?php echo htmlspecialchars($message); ?></p></div>
        <?php endif; ?>

        <div class="form-card mb-4">
            <h2 class="mb-3">Add New Category</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label>Icon</label>
                    <input type="text" name="icon" value="📘">
                </div>

                <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
            </form>
        </div>

        <div class="table-box">
            <h2 class="mb-3">All Categories</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>

                <?php if($categories && $categories->num_rows > 0): ?>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $cat['id']; ?></td>
                            <td><?php echo htmlspecialchars($cat['icon']); ?></td>
                            <td><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                            <td><?php echo htmlspecialchars($cat['description']); ?></td>
                            <td>
                                <a href="categories.php?delete=<?php echo $cat['id']; ?>" onclick="return confirm('Delete this category?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No categories found.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>