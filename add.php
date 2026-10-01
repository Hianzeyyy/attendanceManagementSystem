
<?php

include 'db.php';

$error = "";

if (isset($_POST['save'])) {

    $student_id = trim($_POST['student_id']);
    $student_name = trim($_POST['student_name']);
    $attendance_date = $_POST['attendance_date'];
    $status = $_POST['status'];


    /* ==============================
       VALIDATION
    ================================= */

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


        /* ==============================
           INSERT RECORD
        ================================= */

        $sql = "INSERT INTO attendance
                (student_id, student_name, attendance_date, status)
                VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $student_id,
            $student_name,
            $attendance_date,
            $status
        );


        if (mysqli_stmt_execute($stmt)) {

            header("Location: index.php");

            exit();

        } else {

            $error = "Error adding attendance record.";

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Add Attendance</title>

    <!-- Same CSS used by index.php -->
    <link rel="stylesheet" href="style.css">

</head>


<body>


<!-- ==============================
     HEADER
================================ -->

<header class="top-header">

    <div class="brand">

        <div class="brand-logo">
            ✓
        </div>

        <div class="brand-text">

            <h2>Student Attendance</h2>

            <p>Attendance Management System</p>

        </div>

    </div>

</header>


<!-- ==============================
     MAIN
================================ -->

<main class="form-page">


    <div class="form-card">


        <!-- FORM HEADER -->

        <div class="form-header">

            <div class="form-icon">
                +
            </div>

            <div>

                <p class="small-heading">
                    ATTENDANCE
                </p>

                <h1>
                    Add Attendance
                </h1>

                <p>
                    Enter the student's attendance information.
                </p>

            </div>

        </div>


        <!-- ERROR MESSAGE -->

        <?php if (!empty($error)): ?>

            <div class="error-message">

                <span class="error-icon">
                    !
                </span>

                <span>
                    <?php echo htmlspecialchars($error); ?>
                </span>

            </div>

        <?php endif; ?>


        <!-- ==============================
             FORM
        ================================= -->

        <form
            method="POST"
            action="add.php"
            class="attendance-form"
        >


            <!-- STUDENT ID -->

            <div class="form-group">

                <label for="student_id">
                    Student ID
                </label>

                <input
                    type="text"
                    id="student_id"
                    name="student_id"
                    placeholder="e.g. 2026-00123"
                    value="<?php

                    echo isset($_POST['student_id'])
                        ? htmlspecialchars($_POST['student_id'])
                        : '';

                    ?>"
                    required
                >

                <small>
                    Enter the student's identification number.
                </small>

            </div>


            <!-- STUDENT NAME -->

            <div class="form-group">

                <label for="student_name">
                    Student Name
                </label>

                <input
                    type="text"
                    id="student_name"
                    name="student_name"
                    placeholder="e.g. Juan Dela Cruz"
                    value="<?php

                    echo isset($_POST['student_name'])
                        ? htmlspecialchars($_POST['student_name'])
                        : '';

                    ?>"
                    required
                >

                <small>
                    Enter the complete student name.
                </small>

            </div>


            <!-- DATE -->

            <div class="form-group">

                <label for="attendance_date">
                    Attendance Date
                </label>

                <input
                    type="date"
                    id="attendance_date"
                    name="attendance_date"
                    value="<?php

                    echo isset($_POST['attendance_date'])
                        ? htmlspecialchars($_POST['attendance_date'])
                        : date('Y-m-d');

                    ?>"
                    required
                >

                <small>
                    Select the date of attendance.
                </small>

            </div>


            <!-- STATUS -->

            <div class="form-group">

                <label for="status">
                    Attendance Status
                </label>

                <select
                    id="status"
                    name="status"
                    required
                >

                    <option value="">
                        Select attendance status
                    </option>

                    <option
                        value="Present"
                        <?php

                        if (
                            isset($_POST['status']) &&
                            $_POST['status'] == "Present"
                        ) {
                            echo "selected";
                        }

                        ?>
                    >
                        Present
                    </option>

                    <option
                        value="Absent"
                        <?php

                        if (
                            isset($_POST['status']) &&
                            $_POST['status'] == "Absent"
                        ) {
                            echo "selected";
                        }

                        ?>
                    >
                        Absent
                    </option>

                    <option
                        value="Late"
                        <?php

                        if (
                            isset($_POST['status']) &&
                            $_POST['status'] == "Late"
                        ) {
                            echo "selected";
                        }

                        ?>
                    >
                        Late
                    </option>

                </select>

                <small>
                    Select the student's attendance status.
                </small>

            </div>


            <!-- BUTTONS -->

            <div class="form-buttons">

                <a
                    href="index.php"
                    class="cancel-button"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    name="save"
                    class="save-button"
                >
                    Save Attendance
                </button>

            </div>


        </form>


    </div>


</main>


</body>

</html>


<?php

mysqli_close($conn);

?>

