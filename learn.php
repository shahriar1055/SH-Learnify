<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$course_id = isset($_GET['course']) ? (int)$_GET['course'] : 0;

if ($course_id <= 0) {
    header("Location: dashboard.php");
    exit();
}

if (!isEnrolled($conn, $user_id, $course_id)) {
    header("Location: course-details.php");
    exit();
}

$courseQuery = $conn->prepare("
    SELECT c.*, cat.name AS category_name
    FROM courses c
    LEFT JOIN categories cat ON c.category_id = cat.id
    WHERE c.id = ?
    LIMIT 1
");
$courseQuery->bind_param("i", $course_id);
$courseQuery->execute();
$courseResult = $courseQuery->get_result();

if (!$courseResult || $courseResult->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$course = $courseResult->fetch_assoc();

$lessons = getLessonsByCourse($conn, $course_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lesson_id'])) {
    $lesson_id = (int)$_POST['lesson_id'];

    $check = $conn->prepare("SELECT id FROM lesson_progress WHERE user_id = ? AND lesson_id = ?");
    $check->bind_param("ii", $user_id, $lesson_id);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO lesson_progress (user_id, lesson_id, completed_at) VALUES (?, ?, NOW())");
        $insert->bind_param("ii", $user_id, $lesson_id);
        $insert->execute();
    }

    header("Location: learn.php?course=" . $course_id);
    exit();
}

$currentLessonId = isset($_GET['lesson']) ? (int)$_GET['lesson'] : 0;

if ($currentLessonId <= 0) {
    $firstLesson = $conn->query("SELECT id FROM lessons WHERE course_id = $course_id ORDER BY lesson_order ASC LIMIT 1");
    if ($firstLesson && $firstLesson->num_rows > 0) {
        $currentLessonId = $firstLesson->fetch_assoc()['id'];
    }
}

$currentLessonQuery = $conn->prepare("SELECT * FROM lessons WHERE id = ? AND course_id = ? LIMIT 1");
$currentLessonQuery->bind_param("ii", $currentLessonId, $course_id);
$currentLessonQuery->execute();
$currentLessonResult = $currentLessonQuery->get_result();
$currentLesson = $currentLessonResult->fetch_assoc();

$progressQuery = $conn->prepare("
    SELECT COUNT(lp.id) as completed
    FROM lesson_progress lp
    INNER JOIN lessons l ON lp.lesson_id = l.id
    WHERE lp.user_id = ? AND l.course_id = ?
");
$progressQuery->bind_param("ii", $user_id, $course_id);
$progressQuery->execute();
$progressResult = $progressQuery->get_result()->fetch_assoc();

$completedLessons = (int)($progressResult['completed'] ?? 0);
$totalLessons = (int)$course['total_lessons'];
$progressPercent = ($totalLessons > 0) ? round(($completedLessons / $totalLessons) * 100) : 0;

include 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1><?php echo htmlspecialchars($course['title']); ?></h1>
        <p>Continue your course lessons and track your learning progress.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="learn-layout">

            <div>
                <div class="course-card mb-4">
                    <h2 class="mb-3"><?php echo htmlspecialchars($currentLesson['title'] ?? 'Lesson'); ?></h2>

                    <?php if(!empty($currentLesson['video_url'])): ?>
                        <div class="video-box mb-3">
                            <iframe width="100%" height="420" src="<?php echo htmlspecialchars($currentLesson['video_url']); ?>" frameborder="0" allowfullscreen></iframe>
                        </div>
                    <?php else: ?>
                        <div class="info-card mb-3">
                            <p>No video available for this lesson.</p>
                        </div>
                    <?php endif; ?>

                    <div class="meta mb-3">
                        <span>⏱ <?php echo htmlspecialchars($currentLesson['duration'] ?? ''); ?></span>
                        <span>📚 <?php echo $completedLessons; ?>/<?php echo $totalLessons; ?> Completed</span>
                        <span>📈 <?php echo $progressPercent; ?>% Progress</span>
                    </div>

                    <?php if($currentLesson): ?>
                        <?php
                        $lessonDoneCheck = $conn->prepare("SELECT id FROM lesson_progress WHERE user_id = ? AND lesson_id = ?");
                        $lessonDoneCheck->bind_param("ii", $user_id, $currentLesson['id']);
                        $lessonDoneCheck->execute();
                        $lessonDoneResult = $lessonDoneCheck->get_result();
                        ?>
                        
                        <?php if($lessonDoneResult->num_rows === 0): ?>
                            <form method="POST">
                                <input type="hidden" name="lesson_id" value="<?php echo $currentLesson['id']; ?>">
                                <button type="submit" class="btn btn-primary">Mark as Completed</button>
                            </form>
                        <?php else: ?>
                            <div class="info-card">
                                <p>✅ This lesson is already completed.</p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="course-card">
                    <h3 class="mb-3">Course Lessons</h3>

                    <div class="progress-box mb-4">
                        <div class="progress-label">
                            <span>Your Progress</span>
                            <span><?php echo $progressPercent; ?>%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $progressPercent; ?>%;"></div>
                        </div>
                    </div>

                    <div class="lesson-list">
                        <?php if($lessons && $lessons->num_rows > 0): ?>
                            <?php while($lesson = $lessons->fetch_assoc()): ?>
                                <?php
                                $checkDone = $conn->prepare("SELECT id FROM lesson_progress WHERE user_id = ? AND lesson_id = ?");
                                $checkDone->bind_param("ii", $user_id, $lesson['id']);
                                $checkDone->execute();
                                $doneRes = $checkDone->get_result();
                                $done = $doneRes->num_rows > 0;
                                ?>
                                <a href="learn.php?course=<?php echo $course_id; ?>&lesson=<?php echo $lesson['id']; ?>" class="lesson-link">
                                    <div class="lesson-item <?php echo ($currentLessonId == $lesson['id']) ? 'active-lesson' : ''; ?>">
                                        <div class="lesson-top">
                                            <div>
                                                <h4>
                                                    <?php echo (int)$lesson['lesson_order']; ?>.
                                                    <?php echo htmlspecialchars($lesson['title']); ?>
                                                </h4>
                                                <p class="text-muted"><?php echo htmlspecialchars($lesson['duration']); ?></p>
                                            </div>

                                            <div>
                                                <?php if($done): ?>
                                                    <span class="badge">Done</span>
                                                <?php else: ?>
                                                    <span class="badge">Pending</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No lessons found.</p>
                        <?php endif; ?>
                    </div>

                    <?php if($progressPercent >= 100): ?>
                        <div class="mt-4">
                            <a href="certificates.php" class="btn btn-primary">Get Certificate</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>