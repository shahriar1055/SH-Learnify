<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header("Location: courses.php");
    exit();
}

$slug = trim($_GET['slug']);
$categoryResult = getCategoryBySlug($conn, $slug);

if (!$categoryResult || $categoryResult->num_rows === 0) {
    echo "<section class='page-banner'><div class='container'><h1>Category Not Found</h1></div></section>";
    include 'includes/footer.php';
    exit();
}

$category = $categoryResult->fetch_assoc();
$courses = getCoursesByCategory($conn, $slug);
?>

<section class="page-banner">
    <div class="container">
        <h1><?php echo htmlspecialchars($category['name']); ?></h1>
        <p><?php echo htmlspecialchars($category['description']); ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Courses in <?php echo htmlspecialchars($category['name']); ?></h2>
                <p>Browse all courses available under this category.</p>
            </div>
        </div>

        <div class="grid-3">
            <?php if($courses && $courses->num_rows > 0): ?>
                <?php while($course = $courses->fetch_assoc()): ?>
                    <div class="course-card"
                         data-title="<?php echo htmlspecialchars($course['title']); ?>"
                         data-instructor="<?php echo htmlspecialchars($course['instructor']); ?>"
                         data-category="<?php echo htmlspecialchars($course['category_name'] ?? ''); ?>">

                        <img class="course-thumb" src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=1200&auto=format&fit=crop" alt="Course Thumbnail">

                        <span class="badge"><?php echo htmlspecialchars($course['category_name']); ?></span>
                        <h3 class="mt-3 mb-2"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p class="text-muted"><?php echo htmlspecialchars($course['short_desc']); ?></p>

                        <div class="meta">
                            <span>👨‍🏫 <?php echo htmlspecialchars($course['instructor']); ?></span>
                            <span>⏱ <?php echo htmlspecialchars($course['duration']); ?></span>
                            <span>📚 <?php echo (int)$course['total_lessons']; ?> Lessons</span>
                        </div>

                        <div class="price">৳<?php echo number_format($course['price'], 2); ?></div>
                        <a href="course-details.php?slug=<?php echo urlencode($course['slug']); ?>" class="btn btn-primary">View Details</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No courses found in this category.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>