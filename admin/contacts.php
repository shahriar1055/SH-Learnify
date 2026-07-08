<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireAdmin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM contacts WHERE id=$id");
    header("Location: contacts.php");
    exit();
}

$contacts = $conn->query("SELECT * FROM contacts ORDER BY id DESC");

include '../includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>Contact Messages</h1>
        <p>See messages sent by users from the contact page.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="table-box">
            <h2 class="mb-3">All Contact Messages</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>

                <?php if($contacts && $contacts->num_rows > 0): ?>
                    <?php while($msg = $contacts->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $msg['id']; ?></td>
                            <td><?php echo htmlspecialchars($msg['name']); ?></td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($msg['message'])); ?></td>
                            <td><?php echo $msg['created_at']; ?></td>
                            <td>
                                <a href="contacts.php?delete=<?php echo $msg['id']; ?>" onclick="return confirm('Delete this message?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No contact messages found.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>