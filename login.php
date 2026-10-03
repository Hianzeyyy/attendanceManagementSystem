<?php
require_once 'db.php';
require_once 'layout.php';

if (is_logged_in()) { header("Location: index.php"); exit(); }

$error = "";
$email = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $error = "Please enter your email and password.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, full_name, password_hash FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($user && password_verify($pass, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            header("Location: index.php");
            exit();
        }
        $error = "Incorrect email or password.";
    }
}
page_head("Login", "auth-page"); ?>
<main class="auth-wrap">
  <div class="auth-card">
    <div class="form-icon">✓</div>
    <h1>Welcome back</h1>
    <p class="muted">Log in to open the attendance dashboard.</p>
    <?php show_flash(); ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="POST" class="attendance-form">
      <?= csrf_field() ?>
      <div class="form-group"><label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required autofocus></div>
      <div class="form-group"><label for="password">Password</label>
        <div class="pw-wrap"><input type="password" id="password" name="password" required>
        <button type="button" class="pw-toggle" data-toggle-password="password">Show</button></div></div>
      <button type="submit" class="btn btn-primary btn-block">Log In</button>
    </form>
    <p class="auth-switch">No account yet? <a href="signup.php">Sign up</a></p>
  </div>
</main>
<?php page_foot();
