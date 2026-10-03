<?php
require_once 'db.php';
require_once 'layout.php';

if (is_logged_in()) { header("Location: index.php"); exit(); }

$error = "";
$name = $email = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name    = trim($_POST['full_name'] ?? '');
    $email   = strtolower(trim($_POST['email'] ?? ''));
    $pass    = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $pass === '' || $confirm === '') {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($pass) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($pass !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        if (mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0) {
            $error = "That email is already registered.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hash);
            if (mysqli_stmt_execute($stmt)) {
                flash("Account created. You can log in now.");
                header("Location: login.php");
                exit();
            }
            $error = "Could not create the account. Please try again.";
        }
    }
}
page_head("Sign Up", "auth-page"); ?>
<main class="auth-wrap">
  <div class="auth-card">
    <div class="form-icon">+</div>
    <h1>Create account</h1>
    <p class="muted">Sign up to manage student attendance.</p>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="POST" class="attendance-form">
      <?= csrf_field() ?>
      <div class="form-group"><label for="full_name">Full name</label>
        <input type="text" id="full_name" name="full_name" value="<?= e($name) ?>" required autofocus></div>
      <div class="form-group"><label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required></div>
      <div class="form-group"><label for="password">Password</label>
        <div class="pw-wrap"><input type="password" id="password" name="password" minlength="8" required>
        <button type="button" class="pw-toggle" data-toggle-password="password">Show</button></div>
        <small>At least 8 characters.</small></div>
      <div class="form-group"><label for="confirm_password">Confirm password</label>
        <div class="pw-wrap"><input type="password" id="confirm_password" name="confirm_password" required>
        <button type="button" class="pw-toggle" data-toggle-password="confirm_password">Show</button></div></div>
      <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
    </form>
    <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
  </div>
</main>
<?php page_foot();
