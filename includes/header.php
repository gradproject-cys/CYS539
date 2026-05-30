<?php
// includes/header.php
require_once __DIR__ . '/auth.php';
startSecureSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($pageTitle ?? 'The Riyadh Dispatch') ?></title>
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
      <li><a href="/xss/search.php">Search</a></li>
      <li><a href="/xss/dom.php">Travel</a></li>
      <?php if (isLoggedIn()): ?>
        <?php if (isAdmin()): ?>
          <li><a href="/admin/index.php">Admin</a></li>
        <?php endif; ?>
        <li><a href="/logout.php">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a></li>
      <?php else: ?>
        <li><a href="/login.php">Login</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>
