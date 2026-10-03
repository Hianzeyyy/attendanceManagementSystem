<?php
require_once 'db.php';
require_once 'layout.php';
require_login();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM attendance WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$rec = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$rec) { flash("Record not found.", "error"); header("Location: index.php"); exit(); }

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $d = validate_attendance($_POST, $error);
    if (!$error) {
        $stmt = mysqli_prepare($conn, "UPDATE attendance SET student_id=?, student_name=?, attendance_date=?, status=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $d['student_id'], $d['student_name'], $d['attendance_date'], $d['status'], $id);
        if (mysqli_stmt_execute($stmt)) {
            flash("Attendance record updated.");
            header("Location: index.php");
            exit();
        }
        $error = "Error updating attendance record.";
    }
    $rec = $d;
}
page_head("Edit Attendance");
attendance_form($rec, "Edit Attendance", $error, $id);
page_foot();
