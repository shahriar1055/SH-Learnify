<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireAdmin();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $category_id   = (int)($_POST['category_id'] ?? 0);
    $title         = trim($_POST['title'] ?? '');
    $slug          = trim($_POST['slug'] ?? '');
    $instructor    = trim($_POST['instructor'] ?? '');
    $thumbnail     = trim($_POST['thumbnail'] ?? 'course.jpg');
    $short_desc    = trim($_POST['short_desc'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $price         = (float)($_POST['price'] ?? 0);
    $level         = trim($_POST['level'] ?? 'Beginner');
    $duration      = trim($_POST['duration'] ?? '0h');
    $total_lessons = (int)($_POST['total_lessons'] ?? 0);

    if ($title !== '' && $slug !== '' && $instructor !== '') {
        $stmt = $conn->prepare("INSERT INTO courses(category_id, title, slug, instructor, thumbnail, short_desc, description, price, level, duration, total_lessons)
                                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssdssi", $category_id, $title, $slug, $instructor, $thumbnail, $short_desc, $description, $price, $level, $duration, $total_lessons);

        if ($stmt->execute()) {
            $message = "Course added successfully.";
        } else {
            $message = "Failed to add course.";
        }
    } else {
        $message = "Please fill required fields.";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM courses WHERE id=$id");
    header("Location: courses.php");
    exit();
}

$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$courses = $conn->query("SELECT courses.*, categories.name AS category_name
                         FROM courses
                         LEFT JOIN categories ON courses.category_id = categories.id
                         ORDER BY courses.id DESC");

include '../includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>Manage Courses</h1>
        <p>Create and manage courses on SH Learnify.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if($message): ?>
            <div class="info-card mb-4"><p><?php echo htmlspecialchars($message); ?></p></div>
        <?php endif; ?>

        <div class="form-card mb-4">
            <h2 class="mb-3">Add New Course</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" required>
                        <option value="">Select Category</option>
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Course Title</label>
                    <input type="text" name="title" required>
                </div>

                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug" required>
                </div>

                <div class="form-group">
                    <label>Instructor</label>
                    <input type="text" name="instructor" required>
                </div>

                <div class="form-group">
                    <label>Thumbnail Filename</label>
                    <input type="text" name="thumbnail" value="course.jpg">
                </div>

                <div class="form-group">
                    <label>Short Description</label>
                    <textarea name="short_desc" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label>Full Description</label>
                    <textarea name="description" rows="6"></textarea>
                </div>

                <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" value="0">
                </div>

                <div class="form-group">
                    <label>Level</label>
                    <input type="text" name="level" value="Beginner">
                </div>

                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" name="duration" value="10h">
                </div>

                <div class="form-group">
                    <label>Total Lessons</label>
                    <input type="number" name="total_lessons" value="0">
                </div>

                <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
            </form>
        </div>

        <div class="table-box">
            <h2 class="mb-3">All Courses</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Instructor</th>
                    <th>Price</th>
                    <th>Lessons</th>
                    <th>Action</th>
                </tr>

                <?php if($courses && $courses->num_rows > 0): ?>
                    <?php while($course = $courses->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $course['id']; ?></td>
                            <td><?php echo htmlspecialchars($course['title']); ?></td>
                            <td><?php echo htmlspecialchars($course['category_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($course['instructor']); ?></td>
                            <td>৳<?php echo number_format($course['price'], 2); ?></td>
                            <td><?php echo (int)$course['total_lessons']; ?></td>
                            <td>
                                <a href="courses.php?delete=<?php echo $course['id']; ?>" onclick="return confirm('Delete this course?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No courses found.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>