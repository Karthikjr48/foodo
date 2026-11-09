<?php
require_once 'includes/admin_check.php';
require_once '../includes/db_connect.php';

$pageTitle = 'Messages';
$activePage = 'messages';

$messages = [];
$res = $conn->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC");
while ($row = $res->fetch_assoc()) { $messages[] = $row; }
$conn->close();

require_once 'includes/admin_header.php';
?>

<div class="admin-topbar">
    <h1>Contact Messages</h1>
</div>

<div class="admin-card">
    <?php if (count($messages) === 0): ?>
        <p style="color: var(--color-muted); text-align: center; padding: 20px 0;">No messages received yet.</p>
    <?php else: ?>
        <?php foreach ($messages as $msg): ?>
            <div class="message-card">
                <div class="message-card-top">
                    <div>
                        <strong><?php echo htmlspecialchars($msg['name']); ?></strong>
                        <span class="cat-tag"><?php echo htmlspecialchars($msg['email']); ?></span>
                    </div>
                    <span class="cat-tag"><?php echo date('j M Y, g:i A', strtotime($msg['submitted_at'])); ?></span>
                </div>
                <p class="message-subject"><?php echo htmlspecialchars($msg['subject']); ?></p>
                <p class="message-body"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
