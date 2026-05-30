<?php
require_once __DIR__ . '/../includes/auth.php';
startSecureSession();

if (isLoggedIn()) { header('Location: /index.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Server-side validation
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Both fields are required.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        $error = 'Invalid username format.';
    } elseif (loginUser($username, $password)) {
        header('Location: /index.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}

$pageTitle = 'Login – The Riyadh Dispatch';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $pageTitle ?></title>
  <link rel="stylesheet" href="/css/style.css"/>
</head>
<body>

<div class="warning-banner">
  ⚠️ <strong>TRAINING ENVIRONMENT ONLY</strong> — Do <u>not</u> deploy publicly.
</div>

<nav class="navbar">
  <div class="nav-inner">
    <a class="nav-logo" href="/index.php">The Riyadh Dispatch</a>
    <ul class="nav-links">
      <li><a href="/index.php">Home</a></li>
      <li><a href="/login.php" class="active">Login</a></li>
    </ul>
  </div>
</nav>

<main class="main-content page-narrow" style="max-width:480px;">
  <div class="page-header">
    <h1>Sign In</h1>
    <p class="page-desc">Welcome back. Please log in to continue.</p>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="card-form">
    <form method="POST" action="/login.php" novalidate id="loginForm">
      <div class="form-group">
        <label for="username">Username</label>
        <!-- Client-side validation: pattern + required -->
        <input
          type="text" id="username" name="username"
          class="form-input"
          placeholder="Your username"
          required
          minlength="3" maxlength="50"
          pattern="[a-zA-Z0-9_]+"
          title="Letters, numbers and underscores only"
          value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
        />
        <span class="field-hint">Letters, numbers and underscores only.</span>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input
          type="password" id="password" name="password"
          class="form-input"
          placeholder="Your password"
          required minlength="6" maxlength="100"
        />
      </div>
      <button type="submit" class="search-btn full-width">Login</button>
    </form>
  </div>

  <p class="hint-box">
    Demo credentials:<br/>
    Admin → <code>admin</code> / <code>Admin@1234</code><br/>
    User &nbsp;→ <code>Monther</code> / <code>User@1234</code>
  </p>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
// Client-side validation
document.getElementById('loginForm').addEventListener('submit', function(e) {
  const u = document.getElementById('username').value.trim();
  const p = document.getElementById('password').value;
  if (!u || !p) { e.preventDefault(); alert('Both fields are required.'); return; }
  if (!/^[a-zA-Z0-9_]{3,50}$/.test(u)) { e.preventDefault(); alert('Invalid username format.'); return; }
  if (p.length < 6) { e.preventDefault(); alert('Password must be at least 6 characters.'); }
});
</script>
</body>
</html>
