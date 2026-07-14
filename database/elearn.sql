CREATE DATABASE IF NOT EXISTS sh_learnify;
USE sh_learnify;

-- USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','admin') DEFAULT 'student',
    phone VARCHAR(30) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT 'default-avatar.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. CATEGORIES TABLE
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. COURSES TABLE
CREATE TABLE `courses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `thumbnail` VARCHAR(255) NULL,
  `price` DECIMAL(10,2) DEFAULT 0.00,
  `category_id` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. LESSON PROGRESS TABLE
CREATE TABLE `lesson_progress` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `course_id` INT NOT NULL,
  `lesson_id` INT NOT NULL,
  `is_completed` TINYINT(1) DEFAULT 0,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`lesson_id`) REFERENCES `lessons`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY `user_lesson_unique` (`user_id`, `lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. CERTIFICATES TABLE
CREATE TABLE `certificates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `course_id` INT NOT NULL,
  `certificate_code` VARCHAR(100) NOT NULL UNIQUE,
  `issued_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- LESSON PROGRESS
CREATE TABLE lesson_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    lesson_id INT NOT NULL,
    completed TINYINT(1) DEFAULT 1,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_progress (user_id, lesson_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
);

-- CERTIFICATES
CREATE TABLE certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_certificate (user_id, course_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- CONTACTS
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- DEMO ADMIN
INSERT INTO users (name, email, password, role, phone, bio, avatar)
VALUES (
    'Admin',
    'admin@shlearnify.com',
    MD5('admin123'),
    'admin',
    '01700000000',
    'Platform administrator',
    'default-avatar.png'
);

-- DEMO CATEGORIES
INSERT INTO categories (name, slug, description, icon) VALUES
('Web Development', 'web-development', 'Learn frontend and backend web development.', '💻'),
('Programming', 'programming', 'Master coding with C, C++, Python, JavaScript and more.', '🧠'),
('UI/UX Design', 'ui-ux-design', 'Design beautiful and user-friendly interfaces.', '🎨'),
('Digital Marketing', 'digital-marketing', 'Grow your marketing skills for modern businesses.', '📈'),
('Graphic Design', 'graphic-design', 'Create logos, posters, social graphics and branding.', '🖌️');

-- DEMO COURSES
INSERT INTO courses (category_id, title, slug, instructor, thumbnail, short_desc, description, price, level, duration, total_lessons) VALUES
(1, 'Complete Web Development Bootcamp', 'complete-web-development-bootcamp', 'Sarah Ahmed', 'web.jpg', 'Learn HTML, CSS, JavaScript, PHP and MySQL from scratch.', 'A complete web development course covering frontend and backend fundamentals, responsive design, JavaScript interactions, PHP backend and MySQL database integration.', 1999.00, 'Beginner', '30h', 4),

(2, 'C Programming for Beginners', 'c-programming-for-beginners', 'Tanvir Hasan', 'c-course.jpg', 'Start coding with C programming from zero.', 'Learn variables, loops, functions, arrays, pointers and problem-solving using C language with beginner friendly examples.', 999.00, 'Beginner', '18h', 3),

(3, 'UI UX Design Masterclass', 'ui-ux-design-masterclass', 'Nusrat Jahan', 'design.jpg', 'Learn modern UI/UX principles and Figma workflow.', 'Understand user research, wireframing, visual hierarchy, design systems, prototyping and complete mobile/web UI design workflow.', 1499.00, 'Intermediate', '24h', 3);

-- DEMO LESSONS
INSERT INTO lessons (course_id, title, video_url, lesson_order, duration, is_preview) VALUES
(1, 'Introduction to Web Development', 'https://www.youtube.com/embed/qz0aGYrrlhU', 1, '12 min', 1),
(1, 'HTML & CSS Basics', 'https://www.youtube.com/embed/mU6anWqZJcc', 2, '18 min', 0),
(1, 'JavaScript Fundamentals', 'https://www.youtube.com/embed/W6NZfCO5SIk', 3, '20 min', 0),
(1, 'PHP & MySQL Setup', 'https://www.youtube.com/embed/OK_JCtrrv-c', 4, '22 min', 0),

(2, 'Getting Started with C', 'https://www.youtube.com/embed/KJgsSFOSQv0', 1, '15 min', 1),
(2, 'Variables, Loops and Conditions', 'https://www.youtube.com/embed/irqbmMNs2Bo', 2, '20 min', 0),
(2, 'Functions and Arrays', 'https://www.youtube.com/embed/Bz4MxDeEM6k', 3, '19 min', 0),

(3, 'UI UX Basics', 'https://www.youtube.com/embed/c9Wg6Cb_YlU', 1, '14 min', 1),
(3, 'Wireframing & User Flow', 'https://www.youtube.com/embed/FTFaQWZBqQ8', 2, '17 min', 0),
(3, 'Design Systems in Figma', 'https://www.youtube.com/embed/1pW_sk-2y40', 3, '21 min', 0);
