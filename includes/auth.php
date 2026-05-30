<?php
// includes/auth.php

require_once __DIR__ . '/db.php';

function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => false,   // set true in production over HTTPS
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

function isLoggedIn(): bool {
    startSecureSession();
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    startSecureSession();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        http_response_code(403);
        die('<h2>403 — Forbidden. Admins only.</h2><a href="/index.php">Go home</a>');
    }
}

function loginUser(string $username, string $password): bool {
    // Server-side validation
    $username = trim($username);
    if ($username === '' || $password === '') return false;
    if (strlen($username) > 50)               return false;

    $db   = getDB();
    // Prepared statement — no SQL injection possible
    $stmt = $db->prepare('SELECT id, password, role FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) return false;
    if (!password_verify($password, $user['password'])) return false;

    // Regenerate session ID on login (session fixation defence)
    session_regenerate_id(true);

    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $username;
    $_SESSION['role']     = $user['role'];

    return true;
}

function logoutUser(): void {
    startSecureSession();
    $_SESSION = [];
    session_destroy();
    header('Location: /login.php');
    exit;
}
