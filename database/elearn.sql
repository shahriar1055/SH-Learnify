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

-- 8. CONTACTS TABLE
CREATE TABLE `contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('unread', 'read') DEFAULT 'unread',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 
INSERT IGNORE INTO `users` (`id`, `username`, `email`, `password`, `role`, `wallet_balance`) VALUES
(1, 'Super Admin', 'admin@learnify.com', '$2y$10$e0myL2uQCbH1D.7YVnfeoe71C35NnZ9b9SgY4LhBq5L7I1b6n6Uii', 'admin', 5500.00), -- pass: adminpassword
(2, 'Demo Creator', 'student@learnify.com', '$2y$10$e0myL2uQCbH1D.7YVnfeoe71C35NnZ9b9SgY4LhBq5L7I1b6n6Uii', 'student', 0.00);

INSERT IGNORE INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Travelling', 'travel'),
(2, 'Gaming', 'gaming'),
(3, 'Web Development', 'course');

-- Pre-populate Default Auto-Courses (Free Traveling/Gaming & Premium Courses)
INSERT IGNORE INTO `courses` (`id`, `title`, `description`, `url`, `category_id`, `user_id`, `price`, `is_paid`, `status`, `thumbnail`) VALUES
(1, 'Sajek Valley Scenic Vlog 4K', 'Experience the beautiful cloud kingdom of Bangladesh.', 'https://www.w3schools.com/html/mov_bbb.mp4', 1, 2, 0.00, 0, 'approved', 'travel_sajek.jpg'),
(2, 'GTA VI Gameplay Walkthrough Part 1', 'Pro gaming strategy and graphical showoff of GTA VI.', 'https://www.w3schools.com/html/movie.mp4', 2, 2, 0.00, 0, 'approved', 'gaming_gta.jpg'),
(3, 'Professional Full-Stack PHP & MySQL Masterclass', 'A-Z professional Laravel web application training program.', 'https://www.w3schools.com/html/mov_bbb.mp4', 3, 1, 4500.00, 1, 'approved', 'web_php.jpg'),
(4, 'NextJS Realtime Chat App Development', 'Premium course teaching WebSocket, NextJS & Tailwind.', 'https://www.w3schools.com/html/movie.mp4', 3, 1, 2500.00, 1, 'approved', 'web_nextjs.jpg');
