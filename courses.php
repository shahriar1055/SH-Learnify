<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

$courses = getCourses($conn);
?>

<section class="page-banner">
    <div class="container">
        <h1>All Courses</h1>
        <p>Explore all available courses on SH Learnify and find the best skill track for you.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="search-bar">
            <input type="text" id="courseSearch" placeholder="Search by course title, instructor or category...">
        </div>

        <div class="grid-3">
            <?php if($courses && $courses->num_rows > 0): ?>
                <?php while($course = $courses->fetch_assoc()): ?>
                    <div class="course-card"
                         data-title="<?php echo htmlspecialchars($course['title']); ?>"
                         data-instructor="<?php echo htmlspecialchars($course['instructor']); ?>"
                         data-category="<?php echo htmlspecialchars($course['category_name'] ?? ''); ?>">

                        <img class="course-thumb" src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1200&auto=format&fit=crop" alt="Course Thumbnail">

                        <span class="badge"><?php echo htmlspecialchars($course['category_name'] ?? 'General'); ?></span>
                        <h3 class="mt-3 mb-2"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p class="text-muted"><?php echo htmlspecialchars($course['short_desc']); ?></p>

                        <div class="meta">
                            <span>👨‍🏫 <?php echo htmlspecialchars($course['instructor']); ?></span>
                            <span>📘 <?php echo htmlspecialchars($course['level']); ?></span>
                            <span>⏱ <?php echo htmlspecialchars($course['duration']); ?></span>
                        </div>

                        <div class="price">৳<?php echo number_format($course['price'], 2); ?></div>

                        <a href="course-details.php?slug=<?php echo urlencode($course['slug']); ?>" class="btn btn-primary">View Details</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No courses found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>