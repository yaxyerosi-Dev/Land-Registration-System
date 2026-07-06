<?php
declare(strict_types=1);

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }
    static $user = null;
    if ($user === null) {
        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ? AND status = "active"');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    }
    return $user;
}

function isAdmin(): bool
{
    $user = currentUser();
    return $user && $user['role'] === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        flash('warning', __('login_required'));
        redirect(BASE_URL . '/index.php');
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        flash('danger', __('access_denied'));
        redirect(BASE_URL . '/user/dashboard.php');
    }
}

function requireUser(): void
{
    requireLogin();
    if (isAdmin()) {
        redirect(BASE_URL . '/admin/dashboard.php');
    }
}

function loginUser(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['full_name'];
    logActivity('Login', 'User logged in: ' . $user['email']);
}

function logoutUser(): void
{
    if (isLoggedIn()) {
        logActivity('Logout', 'User logged out');
    }
    session_unset();
    session_destroy();
}
