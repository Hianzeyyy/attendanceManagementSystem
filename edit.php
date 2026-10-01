<?php

include 'db.php';

$error = "";


// Get record ID

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location: index.php");

    exit();

}

$id = (int) $_GET['id'];


// Get existing record

$sql = "SELECT * FROM attendance WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$record = mysqli_fetch_assoc($result);


// Check if record exists

if (!$record) {

    header("Location: index.php");

    exit();

}


// Update record

if (isset($_POST['update'])) {

    $student_id = trim($_POST['student_id']);
    $student_name = trim($_POST['student_name']);
    $attendance_date = $_POST['attendance_date'];
    $status = $_POST['status'];


    // Validation

    if (
        empty($student_id) ||
        empty($student_name) ||
        empty($attendance_date) ||
        empty($status)
    ) {

        $error = "Please fill in all fields.";

    } elseif (!in_array($status, ["Present", "Absent", "Late"])) {

        $error = "Invalid attendance status.";

    } else {

        // Update database

        $sql = "UPDATE attendance
                SET student_id = ?,
                    student_name = ?,
                    attendance_date = ?,
                    status = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $student_id,
            $student_name,
            $attendance_date,
            $status,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {

            header("Location: index.php");

            exit();

        } else {

            $error = "Error updating attendance record.";

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Attendance</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="form-container">

    <h1>Edit Attendance</h1>

    <p class="subtitle">
        Update the student's attendance information.
    </p>


    <?php if (!empty($error)): ?>

        <div class="error-message">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <form method="POST" action="edit.php?id=<?php echo $id; ?>">


        <!-- Student ID -->

        <label for="student_id">
            Student ID
        </label>

        <input
            type="text"
            id="student_id"
            name="student_id"
            value="<?php echo htmlspecialchars($record['student_id']); ?>"
            required
        >


        <!-- Student Name -->

        <label for="student_name">
            Student Name
        </label>

        <input
            type="text"
            id="student_name"
            name="student_name"
            value="<?php echo htmlspecialchars($record['student_name']); ?>"
            required
        >


        <!-- Attendance Date -->

        <label for="attendance_date">
            Attendance Date
        </label>

        <input
            type="date"
            id="attendance_date"
            name="attendance_date"
            value="<?php echo htmlspecialchars($record['attendance_date']); ?>"
            required
        >


        <!-- Status -->

        <label for="status">
            Status
        </label>

        <select id="status" name="status" required>

            <option value="Present"
                <?php echo ($record['status'] == "Present") ? "selected" : ""; ?>>
                Present
            </option>

            <option value="Absent"
                <?php echo ($record['status'] == "Absent") ? "selected" : ""; ?>>
                Absent
            </option>

            <option value="Late"
                <?php echo ($record['status'] == "Late") ? "selected" : ""; ?>>
                Late
            </option>

        </select>


        <!-- Buttons -->

        <div class="form-buttons">

            <button
                type="submit"
                name="update"
                class="save-button"
            >
                Update Attendance
            </button>

            <a
                href="index.php"
                class="cancel-button"
            >
                Cancel
            </a>

        </div>

    </form>

</div>

</body>

</html>

<?php

mysqli_close($conn);

?>
