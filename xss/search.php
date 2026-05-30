<?php
$pageTitle = 'Search – The Riyadh Dispatch';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/db.php';

$query = $_GET['q'] ?? '';

// Log the raw query to the DB (also vulnerable — stored without sanitization)
if ($query !== '') {
    $db = getDB();
    $db->prepare('INSERT INTO search_log (query) VALUES (?)')->execute([$query]);
}
?>

<main class="main-content page-narrow">
  <div class="page-header">
    <h1>Search Posts</h1>
  </div>

  <div class="search-box">
    <form class="search-form" method="GET" action="/xss/search.php">
      <input type="text" name="q" id="searchInput" class="search-input"
             placeholder="e.g. Riyadh, kabsa, jeddah…"
             value="<?= htmlspecialchars($query) ?>"/>
      <button type="submit" class="search-btn">Search</button>
    </form>
  </div>

  <?php if ($query !== ''): ?>
  <div class="search-results" id="search-results">
    <?php
    // ⚠️ VULNERABLE: $query echoed raw into the page (Reflected XSS)
    echo "Showing results for: <strong>" . $query . "</strong>";
    ?>
  </div>
  <?php endif; ?>

</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
