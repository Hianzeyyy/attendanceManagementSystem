<?php
require_once 'auth.php';

function page_head($title, $class = '') { ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title) ?> | Student Attendance</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="<?= e($class) ?>">
<header class="top-header">
  <a class="brand" href="index.php">
    <div class="brand-logo">✓</div>
    <div class="brand-text"><h2>Student Attendance</h2><p>Attendance Management System</p></div>
  </a>
  <?php if (is_logged_in()): ?>
  <div class="user-box">
    <span class="user-avatar"><?= e(strtoupper(substr($_SESSION['user_name'], 0, 1))) ?></span>
    <span class="user-name"><?= e($_SESSION['user_name']) ?></span>
    <form method="POST" action="logout.php"><?= csrf_field() ?><button class="btn btn-ghost" type="submit">Logout</button></form>
  </div>
  <?php endif; ?>
</header>
<?php }

function page_foot() { echo '<script src="app.js"></script></body></html>'; }

function validate_attendance($p, &$error) {
    $d = [
        'student_id'      => trim($p['student_id'] ?? ''),
        'student_name'    => trim($p['student_name'] ?? ''),
        'attendance_date' => $p['attendance_date'] ?? '',
        'status'          => $p['status'] ?? '',
    ];
    if (in_array('', $d, true)) $error = "Please fill in all fields.";
    elseif (!in_array($d['status'], ['Present', 'Late', 'Absent'], true)) $error = "Invalid attendance status.";
    elseif (!DateTime::createFromFormat('Y-m-d', $d['attendance_date'])) $error = "Invalid date.";
    return $d;
}

function attendance_form($d, $label, $error = '', $id = 0) { ?>
<main class="form-page">
  <div class="form-card">
    <div class="form-header">
      <div class="form-icon"><?= $id ? '✎' : '+' ?></div>
      <div><p class="small-heading">ATTENDANCE</p><h1><?= e($label) ?></h1>
      <p>Enter the student's attendance information.</p></div>
    </div>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="POST" class="attendance-form">
      <?= csrf_field() ?>
      <?php if ($id): ?><input type="hidden" name="id" value="<?= (int)$id ?>"><?php endif; ?>
      <div class="form-group"><label for="student_id">Student ID</label>
        <input type="text" id="student_id" name="student_id" placeholder="e.g. 2026-00123" value="<?= e($d['student_id']) ?>" required></div>
      <div class="form-group"><label for="student_name">Student Name</label>
        <input type="text" id="student_name" name="student_name" placeholder="e.g. Juan Dela Cruz" value="<?= e($d['student_name']) ?>" required></div>
      <div class="form-group"><label for="attendance_date">Attendance Date</label>
        <input type="date" id="attendance_date" name="attendance_date" value="<?= e($d['attendance_date']) ?>" required></div>
      <div class="form-group"><label for="status">Status</label>
        <select id="status" name="status" required>
          <option value="">Select status</option>
          <?php foreach (['Present', 'Late', 'Absent'] as $s): ?>
            <option value="<?= $s ?>" <?= $d['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select></div>
      <div class="form-buttons">
        <a href="index.php" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary"><?= $id ? 'Update Attendance' : 'Save Attendance' ?></button>
      </div>
    </form>
  </div>
</main>
<?php }
