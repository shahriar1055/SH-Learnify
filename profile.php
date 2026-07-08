<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$message = "";

$userQuery = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$user = $userQuery->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name !== '') {
        $update = $conn->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
        $update->bind_param("ssi", $name, $phone, $user_id);

        if ($update->execute()) {
            $_SESSION['name'] = $name;
            $message = "Profile updated successfully.";
        } else {
            $message = "Failed to update profile.";
        }
    } else {
        $message = "Name cannot be empty.";
    }

    $userQuery->execute();
    $user = $userQuery->get_result()->fetch_assoc();
}

include 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>My Profile</h1>
        <p>Manage your personal information.</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:700px;">
        <?php if($message): ?>
            <div class="info-card mb-4">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <h2 class="mb-3">Profile Information</h2>

            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>