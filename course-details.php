<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$message = "";

if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header("Location: courses.php");
    exit();
}

$slug = trim($_GET['slug']);
$courseResult = getCourseBySlug($conn, $slug);

if (!$courseResult || $courseResult->num_rows === 0) {
    include 'includes/header.php';
    echo "<section class='page-banner'><div class='container'><h1>Course Not Found</h1></div></section>";
    include 'includes/footer.php';
    exit();
}

$course = $courseResult->fetch_assoc();
$lessons = getLessonsByCourse($conn, $course['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_course'])) {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    if (isEnrolled($conn, $user_id, $course['id'])) {
        $message = "You are already enrolled in this course.";
    } else {
        if (enrollCourse($conn, $user_id, $course['id'])) {
            $message = "Successfully enrolled in this course.";
        } else {
            $message = "Enrollment failed.";
        }
    }
}

include 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1><?php echo htmlspecialchars($course['title']); ?></h1>
        <p><?php echo htmlspecialchars($course['short_desc']); ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if(!empty($message)): ?>
            <div class="form-card mb-4">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <div class="learn-layout">
            <div>
                <div class="course-card mb-4">
                    <img class="course-thumb" src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1200&auto=format&fit=crop" alt="Course Thumbnail">

                    <span class="badge"><?php echo htmlspecialchars($course['category_name'] ?? 'General'); ?></span>
                    <h2 class="mt-3 mb-2"><?php echo htmlspecialchars($course['title']); ?></h2>

                    <div class="meta">
                        <span>👨‍🏫 <?php echo htmlspecialchars($course['instructor']); ?></span>
                        <span>📘 <?php echo htmlspecialchars($course['level']); ?></span>
                        <span>⏱ <?php echo htmlspecialchars($course['duration']); ?></span>
                        <span>📚 <?php echo (int)$course['total_lessons']; ?> Lessons</span>
                    </div>

                    <div class="price">৳<?php echo number_format($course['price'], 2); ?></div>

                    <p class="text-muted mb-3"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>

                    <?php if(isset($_SESSION['user_id']) && isEnrolled($conn, $_SESSION['user_id'], $course['id'])): ?>
                        <a href="learn.php?course=<?php echo $course['id']; ?>" class="btn btn-primary">Continue Learning</a>
                    <?php else: ?>
                        <form method="POST">
                            <button type="submit" name="enroll_course" class="btn btn-primary">Enroll Now</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="course-card">
                    <h3 class="mb-3">Course Lessons</h3>

                    <div class="lesson-list">
                        <?php if($lessons && $lessons->num_rows > 0): ?>
                            <?php while($lesson = $lessons->fetch_assoc()): ?>
                                <div class="lesson-item">
                                    <div class="lesson-top">
                                        <div>
                                            <h4><?php echo (int)$lesson['lesson_order']; ?>. <?php echo htmlspecialchars($lesson['title']); ?></h4>
                                            <p class="text-muted"><?php echo htmlspecialchars($lesson['duration']); ?></p>
                                        </div>
                                        <?php if((int)$lesson['is_preview'] === 1): ?>
                                            <span class="badge">Preview</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No lessons available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>