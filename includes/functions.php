<?php

function getCategories($conn) {
    $sql = "SELECT * FROM categories ORDER BY id DESC";
    return $conn->query($sql);
}

function getCourses($conn, $limit = null) {
    $sql = "SELECT courses.*, categories.name AS category_name, categories.slug AS category_slug
            FROM courses
            LEFT JOIN categories ON courses.category_id = categories.id
            ORDER BY courses.id DESC";

    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }

    return $conn->query($sql);
}

function getCourseBySlug($conn, $slug) {
    $stmt = $conn->prepare("SELECT courses.*, categories.name AS category_name
                            FROM courses
                            LEFT JOIN categories ON courses.category_id = categories.id
                            WHERE courses.slug = ? LIMIT 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    return $stmt->get_result();
}

function getCoursesByCategory($conn, $slug) {
    $stmt = $conn->prepare("SELECT courses.*, categories.name AS category_name
                            FROM courses
                            JOIN categories ON courses.category_id = categories.id
                            WHERE categories.slug = ?
                            ORDER BY courses.id DESC");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    return $stmt->get_result();
}

function getCategoryBySlug($conn, $slug) {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE slug=? LIMIT 1");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    return $stmt->get_result();
}

function getLessonsByCourse($conn, $course_id) {
    $stmt = $conn->prepare("SELECT * FROM lessons WHERE course_id=? ORDER BY lesson_order ASC");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    return $stmt->get_result();
}

function isEnrolled($conn, $user_id, $course_id) {
    $stmt = $conn->prepare("SELECT id FROM enrollments WHERE user_id=? AND course_id=? LIMIT 1");
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function enrollCourse($conn, $user_id, $course_id) {
    if (!isEnrolled($conn, $user_id, $course_id)) {
        $stmt = $conn->prepare("INSERT INTO enrollments(user_id, course_id) VALUES(?, ?)");
        $stmt->bind_param("ii", $user_id, $course_id);
        return $stmt->execute();
    }
    return false;
}

function getUserEnrollments($conn, $user_id) {
    $stmt = $conn->prepare("SELECT courses.*, enrollments.enrolled_at
                            FROM enrollments
                            JOIN courses ON enrollments.course_id = courses.id
                            WHERE enrollments.user_id=?
                            ORDER BY enrollments.id DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getCompletedLessonCount($conn, $user_id, $course_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM lesson_progress WHERE user_id=? AND course_id=? AND completed=1");
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? (int)$row['total'] : 0;
}

function markLessonComplete($conn, $user_id, $course_id, $lesson_id) {
    $stmt = $conn->prepare("INSERT INTO lesson_progress(user_id, course_id, lesson_id, completed)
                            VALUES(?, ?, ?, 1)
                            ON DUPLICATE KEY UPDATE completed=1, completed_at=NOW()");
    $stmt->bind_param("iii", $user_id, $course_id, $lesson_id);
    return $stmt->execute();
}

function isLessonCompleted($conn, $user_id, $lesson_id) {
    $stmt = $conn->prepare("SELECT id FROM lesson_progress WHERE user_id=? AND lesson_id=? AND completed=1 LIMIT 1");
    $stmt->bind_param("ii", $user_id, $lesson_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function issueCertificateIfCompleted($conn, $user_id, $course_id) {
    $courseStmt = $conn->prepare("SELECT total_lessons FROM courses WHERE id=? LIMIT 1");
    $courseStmt->bind_param("i", $course_id);
    $courseStmt->execute();
    $course = $courseStmt->get_result()->fetch_assoc();

    if (!$course) return false;

    $completed = getCompletedLessonCount($conn, $user_id, $course_id);

    if ($completed >= (int)$course['total_lessons'] && (int)$course['total_lessons'] > 0) {
        $stmt = $conn->prepare("INSERT IGNORE INTO certificates(user_id, course_id) VALUES(?, ?)");
        $stmt->bind_param("ii", $user_id, $course_id);
        $stmt->execute();
        return true;
    }

    return false;
}

function getUserCertificates($conn, $user_id) {
    $stmt = $conn->prepare("SELECT certificates.*, courses.title, courses.instructor
                            FROM certificates
                            JOIN courses ON certificates.course_id = courses.id
                            WHERE certificates.user_id=?
                            ORDER BY certificates.id DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}
?>