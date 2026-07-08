<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireAdmin();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_lesson'])) {
    $course_id = (int)($_POST['course_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $video_url = trim($_POST['video_url'] ?? '');
    $lesson_order = (int)($_POST['lesson_order'] ?? 1);
    $duration = trim($_POST['duration'] ?? '10 min');
    $is_preview = isset($_POST['is_preview']) ? 1 : 0;

    if ($course_id > 0 && $title !== '') {
        $stmt = $conn->prepare("INSERT INTO lessons(course_id, title, video_url, lesson_order, duration, is_preview)
                                VALUES(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issisi", $course_id, $title, $video_url, $lesson_order, $duration, $is_preview);

        if ($stmt->execute()) {
            $message = "Lesson added successfully.";
        } else {
            $message = "Failed to add lesson.";
        }
    } else {
        $message = "Course and lesson title are required.";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM lessons WHERE id=$id");
    header("Location: lessons.php");
    exit();
}

$courses = $conn->query("SELECT id, title FROM courses ORDER BY title ASC");
$lessons = $conn->query("SELECT lessons.*, courses.title AS course_title
                         FROM lessons
                         JOIN courses ON lessons.course_id = courses.id
                         ORDER BY lessons.course_id ASC, lessons.lesson_order ASC");

include '../includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>Manage Lessons</h1>
        <p>Add lessons to courses with video URLs and lesson order.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if($message): ?>
            <div class="info-card mb-4"><p><?php echo htmlspecialchars($message); ?></p></div>
        <?php endif; ?>

        <div class="form-card mb-4">
            <h2 class="mb-3">Add New Lesson</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Course</label>
                    <select name="course_id" required>
                        <option value="">Select Course</option>
                        <?php while($course = $courses->fetch_assoc()): ?>
                            <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Lesson Title</label>
                    <input type="text" name="title" required>
                </div>

                <div class="form-group">
                    <label>Video URL (Embed URL)</label>
                    <input type="text" name="video_url" placeholder="https://www.youtube.com/embed/...">
                </div>

                <div class="form-group">
                    <label>Lesson Order</label>
                    <input type="number" name="lesson_order" value="1">
                </div>

                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" name="duration" value="10 min">
                </div>

                <div class="form-group">
                    <label><input type="checkbox" name="is_preview"> Mark as Preview</label>
                </div>

                <button type="submit" name="add_lesson" class="btn btn-primary">Add Lesson</button>
            </form>
        </div>

        <div class="table-box">
            <h2 class="mb-3">All Lessons</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Course</th>
                    <th>Title</th>
                    <th>Order</th>
                    <th>Duration</th>
                    <th>Preview</th>
                    <th>Action</th>
                </tr>

                <?php if($lessons && $lessons->num_rows > 0): ?>
                    <?php while($lesson = $lessons->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $lesson['id']; ?></td>
                            <td><?php echo htmlspecialchars($lesson['course_title']); ?></td>
                            <td><?php echo htmlspecialchars($lesson['title']); ?></td>
                            <td><?php echo (int)$lesson['lesson_order']; ?></td>
                            <td><?php echo htmlspecialchars($lesson['duration']); ?></td>
                            <td><?php echo $lesson['is_preview'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a href="lessons.php?delete=<?php echo $lesson['id']; ?>" onclick="return confirm('Delete this lesson?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No lessons found.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>