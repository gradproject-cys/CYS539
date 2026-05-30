<?php
$pageTitle = 'The Riyadh Dispatch';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// Read posts from DB using prepared statement
$stmt = getDB()->prepare(
    'SELECT p.id, p.title, p.body, p.created_at, u.username
     FROM posts p
     JOIN users u ON u.id = p.author_id
     ORDER BY p.created_at DESC'
);
$stmt->execute();
$posts = $stmt->fetchAll();
?>

<header class="hero">
  <div class="hero-inner">
    <span class="hero-tag">Personal Blog</span>
    <h1>Stories from the<br/><em>Heart of Arabia</em></h1>
    <p class="hero-sub">Exploring Saudi Arabia's cities, culture, food, and the quiet moments in between.</p>
  </div>
</header>

<main class="main-content">
  <section class="posts-grid">
    <?php foreach ($posts as $post): ?>
    <article class="post-card">
      <div class="post-card-meta">
        <span class="post-date"><?= htmlspecialchars(date('M j, Y', strtotime($post['created_at']))) ?></span>
        <span class="post-tag">Culture</span>
      </div>
      <h2><a href="/post.php?id=<?= (int)$post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
      <p><?= htmlspecialchars(substr($post['body'], 0, 160)) ?>…</p>
      <a class="read-more" href="/post.php?id=<?= (int)$post['id'] ?>">Read more →</a>
    </article>
    <?php endforeach; ?>
  </section>

  <section class="about-strip">
    <div class="about-avatar">🌴</div>
    <div class="about-text">
      <h3>About this blog</h3>
      <p>Personal dispatches from Riyadh and beyond. Writing about Saudi culture, travel within the Kingdom, food, and the everyday beauty of life here.</p>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
