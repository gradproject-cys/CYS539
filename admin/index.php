<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
requireAdmin();   // redirects to login if not admin, 403 if not admin role

$db = getDB();

// Handle delete comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $cid = (int)$_POST['delete_comment'];
    $db->prepare('DELETE FROM comments WHERE id = ?')->execute([$cid]);
    header('Location: /admin/index.php?msg=deleted');
    exit;
}

// Handle delete post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $pid = (int)$_POST['delete_post'];
    $db->prepare('DELETE FROM posts WHERE id = ?')->execute([$pid]);
    header('Location: /admin/index.php?msg=post_deleted');
    exit;
}

// Fetch stats
$userCount    = $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
$postCount    = $db->query('SELECT COUNT(*) FROM posts')->fetchColumn();
$commentCount = $db->query('SELECT COUNT(*) FROM comments')->fetchColumn();

// Fetch all users
$users = $db->query('SELECT id, username, role, created_at FROM users ORDER BY id')->fetchAll();

// Fetch all posts
$posts = $db->query(
    'SELECT p.id, p.title, p.created_at, u.username
     FROM posts p JOIN users u ON u.id = p.author_id
     ORDER BY p.created_at DESC'
)->fetchAll();

// Fetch all comments
$comments = $db->query(
    'SELECT c.id, c.author, c.body, c.created_at, p.title AS post_title
     FROM comments c JOIN posts p ON p.id = c.post_id
     ORDER BY c.created_at DESC'
)->fetchAll();

$pageTitle = 'Admin Panel – The Riyadh Dispatch';
require_once __DIR__ . '/../../includes/header.php';
?>

<main class="main-content">
  <div class="page-header">
    <h1>⚙️ Admin Panel</h1>
    <p class="page-desc">Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> (admin)</p>
  </div>

  <?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">Action completed successfully.</div>
  <?php endif; ?>

  <!-- Stats -->
  <div class="admin-stats">
    <div class="stat-card"><span class="stat-num"><?= $userCount ?></span><span class="stat-label">Users</span></div>
    <div class="stat-card"><span class="stat-num"><?= $postCount ?></span><span class="stat-label">Posts</span></div>
    <div class="stat-card"><span class="stat-num"><?= $commentCount ?></span><span class="stat-label">Comments</span></div>
  </div>

  <!-- Users table -->
  <section class="admin-section">
    <h2>Users</h2>
    <table class="admin-table">
      <thead><tr><th>ID</th><th>Username</th><th>Role</th><th>Joined</th></tr></thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><?= (int)$u['id'] ?></td>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><span class="role-badge <?= $u['role'] ?>"><?= htmlspecialchars($u['role']) ?></span></td>
          <td><?= htmlspecialchars($u['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <!-- Posts table -->
  <section class="admin-section">
    <h2>Posts</h2>
    <table class="admin-table">
      <thead><tr><th>ID</th><th>Title</th><th>Author</th><th>Date</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($posts as $p): ?>
        <tr>
          <td><?= (int)$p['id'] ?></td>
          <td><a href="/post.php?id=<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['title']) ?></a></td>
          <td><?= htmlspecialchars($p['username']) ?></td>
          <td><?= htmlspecialchars($p['created_at']) ?></td>
          <td>
            <form method="POST" onsubmit="return confirm('Delete this post?')">
              <input type="hidden" name="delete_post" value="<?= (int)$p['id'] ?>"/>
              <button class="btn-danger">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <!-- Comments table -->
  <section class="admin-section">
    <h2>Comments</h2>
    <table class="admin-table">
      <thead><tr><th>ID</th><th>Author</th><th>Comment</th><th>Post</th><th>Date</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($comments as $c): ?>
        <tr>
          <td><?= (int)$c['id'] ?></td>
          <td><?= htmlspecialchars($c['author']) ?></td>
          <!-- Admin sees raw body so they can spot injected content -->
          <td class="comment-preview"><?= htmlspecialchars($c['body']) ?></td>
          <td><?= htmlspecialchars($c['post_title']) ?></td>
          <td><?= htmlspecialchars($c['created_at']) ?></td>
          <td>
            <form method="POST" onsubmit="return confirm('Delete this comment?')">
              <input type="hidden" name="delete_comment" value="<?= (int)$c['id'] ?>"/>
              <button class="btn-danger">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
