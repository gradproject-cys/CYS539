<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: /index.php'); exit; }

$db = getDB();

// Fetch post — prepared statement
$stmt = $db->prepare(
    'SELECT p.*, u.username FROM posts p
     JOIN users u ON u.id = p.author_id
     WHERE p.id = ?'
);
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) { http_response_code(404); die('<h2>Post not found.</h2>'); }

$commentError = '';
$commentSuccess = '';

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_body'])) {
    $author = trim($_POST['comment_author'] ?? 'Anonymous');
    $body   = trim($_POST['comment_body']   ?? '');

    // ---- SERVER-SIDE VALIDATION ----
    if ($body === '') {
        $commentError = 'Comment cannot be empty.';
    } elseif (strlen($author) > 100) {
        $commentError = 'Name too long.';
    } elseif (strlen($body) > 2000) {
        $commentError = 'Comment too long (max 2000 chars).';
    } else {
        // ⚠️ VULNERABLE: body stored and rendered WITHOUT sanitization (Stored XSS demo)
        $ins = $db->prepare(
            'INSERT INTO comments (post_id, author, body) VALUES (?, ?, ?)'
        );
        $ins->execute([$id, $author, $body]);
        $commentSuccess = 'Comment posted.';
    }
}

// Fetch comments — prepared statement
$cstmt = $db->prepare(
    'SELECT author, body, created_at FROM comments WHERE post_id = ? ORDER BY created_at ASC'
);
$cstmt->execute([$id]);
$comments = $cstmt->fetchAll();

$pageTitle = htmlspecialchars($post['title']) . ' – The Riyadh Dispatch';
?>

<main class="main-content page-narrow">
  <article class="blog-post">
    <div class="post-meta">
      <span class="post-date"><?= htmlspecialchars(date('M j, Y', strtotime($post['created_at']))) ?></span>
      <span class="post-category">Culture</span>
    </div>
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <?php foreach (explode("\n", $post['body']) as $para): ?>
      <p><?= htmlspecialchars(trim($para)) ?></p>
    <?php endforeach; ?>
  </article>

  <!-- ===== STORED XSS VULNERABLE COMMENT SECTION ===== -->
  <section class="comments-section">
    <h2>Comments (<?= count($comments) ?>)</h2>

    <?php if ($commentError): ?>
      <div class="alert alert-error"><?= htmlspecialchars($commentError) ?></div>
    <?php endif; ?>
    <?php if ($commentSuccess): ?>
      <div class="alert alert-success"><?= htmlspecialchars($commentSuccess) ?></div>
    <?php endif; ?>

    <!-- Comment form — client-side validation only, body NOT sanitized server-side -->
    <div class="comment-form-wrap">
      <h3>Leave a Comment</h3>
      <form method="POST" id="commentForm">
        <div class="form-group">
          <label for="commentName">Name</label>
          <input type="text" id="commentName" name="comment_author"
                 class="form-input" placeholder="Your name"
                 maxlength="100"/>
        </div>
        <div class="form-group">
          <label for="commentBody">Comment</label>
          <textarea id="commentBody" name="comment_body"
                    class="form-textarea" rows="4"
                    placeholder="Share your thoughts…"
                    required maxlength="2000"></textarea>
        </div>
        <div class="form-actions">
          <button type="submit" class="search-btn">Post Comment</button>
          <a href="/index.php" class="btn-ghost">← Back</a>
        </div>
      </form>
    </div>

    <!-- ⚠️ VULNERABLE: comment body rendered with echo, no htmlspecialchars -->
    <div class="comment-list" id="comment-list">
      <?php if (empty($comments)): ?>
        <p class="no-comments">No comments yet. Be the first!</p>
      <?php else: ?>
        <?php foreach ($comments as $c): ?>
          <div class="comment-item">
            <div class="comment-author"><?= htmlspecialchars($c['author']) ?></div>
            <!-- ⚠️ VULNERABLE LINE BELOW — body echoed raw -->
            <div class="comment-text"><?= $c['body'] ?></div>
            <div class="comment-timestamp"><?= htmlspecialchars($c['created_at']) ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
document.getElementById('commentForm').addEventListener('submit', function(e) {
  const body = document.getElementById('commentBody').value.trim();
  if (!body) { e.preventDefault(); alert('Comment cannot be empty.'); }
});
</script>
