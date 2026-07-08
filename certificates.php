<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();

$user_id = $_SESSION['user_id'];

$courses = $conn->prepare("
    SELECT c.id, c.title, c.total_lessons
    FROM enrollments e
    INNER JOIN courses c ON e.course_id = c.id
    WHERE e.user_id = ?
");
$courses->bind_param("i", $user_id);
$courses->execute();
$coursesResult = $courses->get_result();

while($course = $coursesResult->fetch_assoc()){
    $course_id = $course['id'];
    $totalLessons = (int)$course['total_lessons'];

    $completedQ = $conn->prepare("
        SELECT COUNT(lp.id) as completed
        FROM lesson_progress lp
        INNER JOIN lessons l ON lp.lesson_id = l.id
        WHERE lp.user_id = ? AND l.course_id = ?
    ");
    $completedQ->bind_param("ii", $user_id, $course_id);
    $completedQ->execute();
    $completed = $completedQ->get_result()->fetch_assoc()['completed'] ?? 0;

    if($totalLessons > 0 && $completed >= $totalLessons){
        $checkCert = $conn->prepare("SELECT id FROM certificates WHERE user_id = ? AND course_id = ?");
        $checkCert->bind_param("ii", $user_id, $course_id);
        $checkCert->execute();
        $checkRes = $checkCert->get_result();

        if($checkRes->num_rows === 0){
            $insertCert = $conn->prepare("INSERT INTO certificates(user_id, course_id, certificate_code, issued_at) VALUES (?, ?, ?, NOW())");
            $code = 'CERT-' . strtoupper(substr(md5($user_id . $course_id . time()), 0, 10));
            $insertCert->bind_param("iis", $user_id, $course_id, $code);
            $insertCert->execute();
        }
    }
}

$certificates = $conn->prepare("
    SELECT cert.*, c.title AS course_title
    FROM certificates cert
    INNER JOIN courses c ON cert.course_id = c.id
    WHERE cert.user_id = ?
    ORDER BY cert.id DESC
");
$certificates->bind_param("i", $user_id);
$certificates->execute();
$certificateResult = $certificates->get_result();

include 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>My Certificates</h1>
        <p>View all certificates earned after course completion.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="table-box">
            <h2 class="mb-3">Earned Certificates</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Course</th>
                    <th>Certificate Code</th>
                    <th>Issued Date</th>
                </tr>

                <?php if($certificateResult && $certificateResult->num_rows > 0): ?>
                    <?php while($cert = $certificateResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $cert['id']; ?></td>
                            <td><?php echo htmlspecialchars($cert['course_title']); ?></td>
                            <td><?php echo htmlspecialchars($cert['certificate_code']); ?></td>
                            <td><?php echo htmlspecialchars($cert['issued_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No certificates yet. Complete a course first.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>