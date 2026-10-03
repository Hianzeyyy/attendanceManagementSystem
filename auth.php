<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function is_logged_in() { return !empty($_SESSION['user_id']); }
function require_login() {
    if (!is_logged_in()) { header("Location: login.php"); exit(); }
}
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_check() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403);
        die("Invalid request token. Go back, refresh, and try again.");
    }
}
function flash($msg, $type = 'success') { $_SESSION['flash'] = [$msg, $type]; }
function show_flash() {
    if (!empty($_SESSION['flash'])) {
        [$m, $t] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($t) . '" data-dismiss>' . e($m) . '</div>';
    }
}
