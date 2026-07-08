<?php
require_once 'includes/db.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $msg = trim($_POST['message'] ?? '');

    if ($name !== '' && $email !== '' && $subject !== '' && $msg !== '') {
        $stmt = $conn->prepare("INSERT INTO contacts(name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $name, $email, $subject, $msg);

        if ($stmt->execute()) {
            $message = "Your message has been sent successfully.";
        } else {
            $message = "Failed to send message.";
        }
    } else {
        $message = "Please fill all fields.";
    }
}

include 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Have any questions? Send us a message anytime.</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:800px;">
        <?php if($message): ?>
            <div class="info-card mb-4">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <h2 class="mb-3">Send Message</h2>

            <form method="POST">
                <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Your Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" required>
                </div>

                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" rows="6" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>