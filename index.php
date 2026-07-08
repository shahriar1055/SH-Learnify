<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

$categories = getCategories($conn);
$featuredCourses = getCourses($conn, 6);

$totalCourses = $conn->query("SELECT COUNT(*) as total FROM courses")->fetch_assoc()['total'] ?? 0;
$totalStudents = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='student'")->fetch_assoc()['total'] ?? 0;
$totalCategories = $conn->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc()['total'] ?? 0;
?>

<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <span class="badge">Professional E-Learning Platform</span>
            <h1 class="mt-3">Upgrade Your Skills With <span class="gradient-text">SH Learnify</span></h1>
            <p>
                Learn Web Development, Programming, UI/UX Design, Digital Marketing and more
                through a clean, modern and professional learning platform built for students.
            </p>

            <div class="hero-actions">
                <a href="courses.php" class="btn btn-primary">Browse Courses</a>
                <a href="register.php" class="btn">Get Started</a>
            </div>

            <div class="stats-grid">
                <div class="stat-box">
                    <h3><?php echo $totalCourses; ?>+</h3>
                    <p>Courses</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $totalStudents; ?>+</h3>
                    <p>Students</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $totalCategories; ?>+</h3>
                    <p>Categories</p>
                </div>
            </div>
        </div>

        <div class="hero-card">
            <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1200&auto=format&fit=crop" alt="Learning">
            <h3 class="mb-2">Learn Smarter, Build Faster</h3>
            <p class="text-muted">
                SH Learnify gives you structured courses, category browsing, lesson-based learning,
                progress tracking, certificates and a full student dashboard experience.
            </p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Platform Categories</h2>
                <p>Explore learning paths by category and find the skill track that matches your goal.</p>
            </div>
        </div>

        <div class="grid-3">
            <?php if($categories && $categories->num_rows > 0): ?>
                <?php while($cat = $categories->fetch_assoc()): ?>
                    <div class="category-card">
                        <div class="category-icon"><?php echo htmlspecialchars($cat['icon']); ?></div>
                        <h3 class="mb-2"><?php echo htmlspecialchars($cat['name']); ?></h3>
                        <p class="text-muted mb-3"><?php echo htmlspecialchars($cat['description']); ?></p>
                        <a href="category.php?slug=<?php echo urlencode($cat['slug']); ?>" class="btn btn-sm">Explore Category</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No categories found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Featured Courses</h2>
                <p>Start with our most popular and practical courses designed for modern learners.</p>
            </div>
            <a href="courses.php" class="btn btn-sm">View All Courses</a>
        </div>

        <div class="grid-3">
            <?php if($featuredCourses && $featuredCourses->num_rows > 0): ?>
                <?php while($course = $featuredCourses->fetch_assoc()): ?>
                    <div class="course-card"
                         data-title="<?php echo htmlspecialchars($course['title']); ?>"
                         data-instructor="<?php echo htmlspecialchars($course['instructor']); ?>"
                         data-category="<?php echo htmlspecialchars($course['category_name'] ?? ''); ?>">

                        <img class="course-thumb" src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=1200&auto=format&fit=crop" alt="Course Thumbnail">

                        <span class="badge"><?php echo htmlspecialchars($course['category_name'] ?? 'General'); ?></span>
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
                <p>No courses found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <h2>Main Features</h2>
                <p>Everything a modern e-learning platform needs for students and admins.</p>
            </div>
        </div>

        <div class="grid-4">
            <div class="feature-card">
                <h3 class="mb-2">📚 Category Based Learning</h3>
                <p class="text-muted">Organized course browsing by platform categories for easier discovery.</p>
            </div>

            <div class="feature-card">
                <h3 class="mb-2">🎥 Lesson Video System</h3>
                <p class="text-muted">Each course contains structured lessons with video links and durations.</p>
            </div>

            <div class="feature-card">
                <h3 class="mb-2">📈 Progress Tracking</h3>
                <p class="text-muted">Students can mark lessons complete and track course progress visually.</p>
            </div>

            <div class="feature-card">
                <h3 class="mb-2">🏆 Certificates</h3>
                <p class="text-muted">Automatic certificate generation after completing all lessons of a course.</p>
            </div>

            <div class="feature-card">
                <h3 class="mb-2">👤 Student Dashboard</h3>
                <p class="text-muted">A personal dashboard to view enrolled courses, progress and certificates.</p>
            </div>

            <div class="feature-card">
                <h3 class="mb-2">🔐 Login & Registration</h3>
                <p class="text-muted">Secure authentication system with separate admin and student roles.</p>
            </div>

            <div class="feature-card">
                <h3 class="mb-2">🛠 Admin Panel</h3>
                <p class="text-muted">Manage categories, courses, lessons, users and contact messages easily.</p>
            </div>

            <div class="feature-card">
                <h3 class="mb-2">🔎 Search Experience</h3>
                <p class="text-muted">Live course filtering with JavaScript for a better browsing experience.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>