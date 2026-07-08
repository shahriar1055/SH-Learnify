<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireAdmin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== (int)$_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id=$id");
    }
    header("Location: users.php");
    exit();
}

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");

include '../includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>Manage Users</h1>
        <p>View all users registered on SH Learnify.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="table-box">
            <h2 class="mb-3">All Users</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>

                <?php if($users && $users->num_rows > 0): ?>
                    <?php while($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone'] ?? ''); ?></td>
                            <td><?php echo $user['created_at']; ?></td>
                            <td>
                                <?php if($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="users.php?delete=<?php echo $user['id']; ?>" onclick="return confirm('Delete this user?')">Delete</a>
                                <?php else: ?>
                                    Current Admin
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No users found.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>