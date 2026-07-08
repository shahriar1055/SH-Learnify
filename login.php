<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = MD5(?) LIMIT 1");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}

include 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>Login</h1>
        <p>Access your SH Learnify account and continue your learning journey.</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:700px;">
        <div class="form-card">
            <h2 class="mb-3">Welcome Back</h2>

            <?php if($error): ?>
                <div class="info-card mb-3">
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
            </form>

            <p class="mt-3 text-muted">
                Don’t have an account? <a href="register.php">Create one</a>
            </p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>