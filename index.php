<?php
require_once 'db.php';
require_once 'layout.php';
require_login();

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$date   = $_GET['date'] ?? '';

$where = []; $types = ''; $params = [];
if ($search !== '') {
    $where[] = "(student_id LIKE ? OR student_name LIKE ?)";
    $types .= 'ss'; $params[] = "%$search%"; $params[] = "%$search%";
}
if (in_array($status, ['Present', 'Late', 'Absent'], true)) {
    $where[] = "status = ?"; $types .= 's'; $params[] = $status;
} else { $status = ''; }
if ($date !== '' && DateTime::createFromFormat('Y-m-d', $date)) {
    $where[] = "attendance_date = ?"; $types .= 's'; $params[] = $date;
} else { $date = ''; }

$sql = "SELECT * FROM attendance" . ($where ? " WHERE " . implode(" AND ", $where) : "") . " ORDER BY attendance_date DESC, id DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($params) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

/* CSV export of the current (filtered) view */
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="attendance_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'Student ID', 'Student Name', 'Date', 'Status']);
    foreach ($rows as $r) fputcsv($out, [$r['id'], $r['student_id'], $r['student_name'], $r['attendance_date'], $r['status']]);
    exit();
}

$s = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total, SUM(status='Present') present, SUM(status='Late') late, SUM(status='Absent') absent FROM attendance"));
$total = (int)$s['total']; $present = (int)$s['present']; $late = (int)$s['late']; $absent = (int)$s['absent'];
$filtered = $search !== '' || $status !== '' || $date !== '';
$exportUrl = 'index.php?' . http_build_query(array_filter(['search' => $search, 'status' => $status, 'date' => $date]) + ['export' => 1]);

page_head("Dashboard"); ?>
<main class="main-container">
  <?php show_flash(); ?>
  <section class="page-heading">
    <div>
      <p class="small-heading">MANAGEMENT</p>
      <h1>Attendance Dashboard</h1>
      <p class="muted">Manage and monitor student attendance records.</p>
    </div>
    <div class="heading-actions">
      <a href="<?= e($exportUrl) ?>" class="btn btn-ghost">Export CSV</a>
      <a href="add.php" class="btn btn-primary">+ Add Attendance</a>
    </div>
  </section>

  <section class="statistics">
    <div class="stat-card"><div class="stat-icon total">#</div><div><p>Total Records</p><h2><?= $total ?></h2></div></div>
    <div class="stat-card"><div class="stat-icon present">✓</div><div><p>Present</p><h2><?= $present ?></h2></div></div>
    <div class="stat-card"><div class="stat-icon late">◷</div><div><p>Late</p><h2><?= $late ?></h2></div></div>
    <div class="stat-card"><div class="stat-icon absent">!</div><div><p>Absent</p><h2><?= $absent ?></h2></div></div>
  </section>

  <section class="card">
    <div class="card-head">
      <div><h2>Attendance Records</h2><p class="muted">Search, filter, edit or delete records.</p></div>
      <form method="GET" action="index.php" class="filters">
        <input type="text" name="search" placeholder="Search ID or name..." value="<?= e($search) ?>">
        <select name="status" data-autosubmit>
          <option value="">All status</option>
          <?php foreach (['Present', 'Late', 'Absent'] as $o): ?>
            <option <?= $status === $o ? 'selected' : '' ?>><?= $o ?></option>
          <?php endforeach; ?>
        </select>
        <input type="date" name="date" value="<?= e($date) ?>" data-autosubmit>
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($filtered): ?><a href="index.php" class="btn btn-ghost">Clear</a><?php endif; ?>
      </form>
    </div>

    <div class="table-container">
      <table>
        <thead><tr><th>ID</th><th>Student</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if ($rows): foreach ($rows as $r):
            $ts = strtotime($r['attendance_date']); ?>
          <tr>
            <td><span class="record-number">#<?= (int)$r['id'] ?></span></td>
            <td><div class="student">
              <div class="student-avatar"><?= e(strtoupper(substr($r['student_name'], 0, 1))) ?></div>
              <div><strong><?= e($r['student_name']) ?></strong><span class="sub">ID: <?= e($r['student_id']) ?></span></div>
            </div></td>
            <td><strong><?= date("M d, Y", $ts) ?></strong><span class="sub"><?= date("l", $ts) ?></span></td>
            <td><span class="status status-<?= strtolower(e($r['status'])) ?>"><span class="dot"></span><?= e($r['status']) ?></span></td>
            <td><div class="actions">
              <a href="edit.php?id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-edit">Edit</a>
              <form method="POST" action="delete.php" data-confirm="Delete the attendance record of <?= e($r['student_name']) ?>?">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button type="submit" class="btn btn-sm btn-delete">Delete</button>
              </form>
            </div></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="5" class="empty-state">
            <h3>No attendance records found</h3>
            <p class="muted"><?= $filtered ? 'Nothing matches your filters.' : 'There are currently no attendance records.' ?></p>
            <a href="add.php" class="btn btn-primary">+ Add Attendance</a>
          </td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="card-foot"><span>Showing <strong><?= count($rows) ?></strong> record(s)</span><span>Total: <strong><?= $total ?></strong></span></div>
  </section>

  <footer class="footer">&copy; <?= date("Y") ?> Student Attendance Management System &bull; PHP &bull; MySQL &bull; XAMPP</footer>
</main>

<div class="modal" id="confirm-modal">
  <div class="modal-box">
    <h3>Are you sure?</h3><p></p>
    <div class="form-buttons">
      <button type="button" class="btn btn-ghost" id="confirm-no">Cancel</button>
      <button type="button" class="btn btn-delete" id="confirm-yes">Yes, delete</button>
    </div>
  </div>
</div>
<?php page_foot();
