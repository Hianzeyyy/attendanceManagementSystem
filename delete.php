<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $stmt = mysqli_prepare($conn, "DELETE FROM attendance WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    flash("Attendance record deleted.");
}
header("Location: index.php");
exit();
