<?php
require_once 'db.php';
require_once 'layout.php';
require_login();

$error = "";
$d = ['student_id' => '', 'student_name' => '', 'attendance_date' => date('Y-m-d'), 'status' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $d = validate_attendance($_POST, $error);
    if (!$error) {
        $stmt = mysqli_prepare($conn, "INSERT INTO attendance (student_id, student_name, attendance_date, status) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $d['student_id'], $d['student_name'], $d['attendance_date'], $d['status']);
        if (mysqli_stmt_execute($stmt)) {
            flash("Attendance record added.");
            header("Location: index.php");
            exit();
        }
        $error = "Error adding attendance record.";
    }
}
page_head("Add Attendance");
attendance_form($d, "Add Attendance", $error);
page_foot();
