<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

requireLogin();

$user_id = $_SESSION['user_id'];

$enrolledCourses = $conn->prepare("
    SELECT e.*, c.title, c.slug, c.thumbnail, c.short_desc, c.total_lessons, c.duration, c.instructor
    FROM enrollments e
    INNER JOIN courses c ON e.course_id = c.id
    WHERE e.user_id = ?
    ORDER BY e.id DESC
");
$enrolledCourses->bind_param("i", $user_id);
$enrolledCourses->execute();
$enrolledResult = $enrolledCourses->get_result();

$totalEnrollmentsQuery = $conn->prepare("SELECT COUNT(*) as total FROM enrollments WHERE user_id = ?");
$totalEnrollmentsQuery->bind_param("i", $user_id);
$totalEnrollmentsQuery->execute();
$totalEnrollments = $totalEnrollmentsQuery->get_result()->fetch_assoc()['total'] ?? 0;

$totalCompletedLessonsQuery = $conn->prepare("SELECT COUNT(*) as total FROM lesson_progress WHERE user_id = ?");
$totalCompletedLessonsQuery->bind_param("i", $user_id);
$totalCompletedLessonsQuery->execute();
$totalCompletedLessons = $totalCompletedLessonsQuery->get_result()->fetch_assoc()['total'] ?? 0;

include 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>Student Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?> 👋</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="dashboard-grid mb-4">
            <div class="dashboard-card">
                <h3>Enrolled Courses</h3>
                <p><?php echo $totalEnrollments; ?></p>
            </div>

            <div class="dashboard-card">
                <h3>Completed Lessons</h3>
                <p><?php echo $totalCompletedLessons; ?></p>
            </div>

            <div class="dashboard-card">
                <h3>Certificates</h3>
                <p>
                    <?php
                    $certCount = $conn->query("SELECT COUNT(*) as total FROM certificates WHERE user_id = $user_id")->fetch_assoc()['total'] ?? 0;
                    echo $certCount;
                    ?>
                </p>
            </div>
        </div>

        <div class="section-head">
            <div>
                <h2>My Courses</h2>
                <p>Continue learning from your enrolled courses.</p>
            </div>
        </div>

        <div class="grid-3">
            <?php if($enrolledResult && $enrolledResult->num_rows > 0): ?>
                <?php while($course = $enrolledResult->fetch_assoc()): ?>
                    <?php
                    $courseId = $course['course_id'];

                    $completedCourseLessonsQuery = $conn->prepare("
                        SELECT COUNT(lp.id) as total
                        FROM lesson_progress lp
                        INNER JOIN lessons l ON lp.lesson_id = l.id
                        WHERE lp.user_id = ? AND l.course_id = ?
                    ");
                    $completedCourseLessonsQuery->bind_param("ii", $user_id, $courseId);
                    $completedCourseLessonsQuery->execute();
                    $completedCourseLessons = $completedCourseLessonsQuery->get_result()->fetch_assoc()['total'] ?? 0;

                    $courseProgress = ($course['total_lessons'] > 0)
                        ? round(($completedCourseLessons / $course['total_lessons']) * 100)
                        : 0;
                    ?>

                    <div class="course-card">
                        <img class="course-thumb" src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=1200&auto=format&fit=crop" alt="Course">

                        <h3 class="mt-3 mb-2"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p class="text-muted"><?php echo htmlspecialchars($course['short_desc']); ?></p>

                        <div class="meta">
                            <span>👨‍🏫 <?php echo htmlspecialchars($course['instructor']); ?></span>
                            <span>⏱ <?php echo htmlspecialchars($course['duration']); ?></span>
                            <span>📚 <?php echo (int)$course['total_lessons']; ?> Lessons</span>
                        </div>

                        <div class="progress-box mb-3">
                            <div class="progress-label">
                                <span>Progress</span>
                                <span><?php echo $courseProgress; ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $courseProgress; ?>%;"></div>
                            </div>
                        </div>

                        <a href="learn.php?course=<?php echo $courseId; ?>" class="btn btn-primary">Continue Course</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="info-card">
                    <p>You have not enrolled in any course yet. <a href="courses.php">Browse Courses</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>