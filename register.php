<?php
require_once 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        $error = "Please fill in all required fields.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {
            $error = "This email is already registered.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users(name, email, password, role) VALUES(?, ?, MD5(?), 'student')");
            $stmt->bind_param("sss", $name, $email, $password);

            if ($stmt->execute()) {
                $success = "Registration successful. You can login now.";
            } else {
                $error = "Registration failed.";
            }
        }
    }
}

include 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>Register</h1>
        <p>Create your SH Learnify account and start learning today.</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:700px;">
        <div class="form-card">
            <h2 class="mb-3">Create Account</h2>

            <?php if($error): ?>
                <div class="info-card mb-3">
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="info-card mb-3">
                    <p><?php echo htmlspecialchars($success); ?></p>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Create Account</button>
            </form>

            <p class="mt-3 text-muted">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>