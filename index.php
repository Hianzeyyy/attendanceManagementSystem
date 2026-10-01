<?php

include 'db.php';

$search = "";

/* ==============================
   SEARCH
================================ */

if (isset($_GET['search'])) {

    $search = trim($_GET['search']);

    $sql = "SELECT * FROM attendance
            WHERE student_id LIKE ?
            OR student_name LIKE ?
            ORDER BY attendance_date DESC, id DESC";

    $stmt = mysqli_prepare($conn, $sql);

    $searchTerm = "%" . $search . "%";

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $searchTerm,
        $searchTerm
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

} else {

    $sql = "SELECT * FROM attendance
            ORDER BY attendance_date DESC, id DESC";

    $result = mysqli_query($conn, $sql);
}


/* ==============================
   STATISTICS
================================ */

$total = 0;
$present = 0;
$late = 0;
$absent = 0;

$statsSql = "SELECT
                COUNT(*) AS total,
                SUM(status = 'Present') AS present,
                SUM(status = 'Late') AS late,
                SUM(status = 'Absent') AS absent
             FROM attendance";

$statsResult = mysqli_query($conn, $statsSql);

if ($statsResult) {

    $stats = mysqli_fetch_assoc($statsResult);

    $total = (int)($stats['total'] ?? 0);
    $present = (int)($stats['present'] ?? 0);
    $late = (int)($stats['late'] ?? 0);
    $absent = (int)($stats['absent'] ?? 0);
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

    <title>Student Attendance System</title>

    <!-- External CSS -->
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
     MAIN CONTENT
================================ -->

<main class="main-container">


    <!-- PAGE TITLE -->

    <section class="page-heading">

        <div>

            <p class="small-heading">
                MANAGEMENT
            </p>

            <h1>
                Attendance Dashboard
            </h1>

            <p class="description">
                Manage and monitor student attendance records.
            </p>

        </div>


        <a
            href="add.php"
            class="add-button"
        >

            <span class="plus-icon">+</span>

            Add Attendance

        </a>

    </section>


    <!-- ==============================
         STATISTICS
    ================================= -->

    <section class="statistics">


        <!-- TOTAL -->

        <div class="stat-card">

            <div class="stat-icon total">
                #
            </div>

            <div>

                <p>Total Records</p>

                <h2>
                    <?php echo $total; ?>
                </h2>

            </div>

        </div>


        <!-- PRESENT -->

        <div class="stat-card">

            <div class="stat-icon present">
                ✓
            </div>

            <div>

                <p>Present</p>

                <h2>
                    <?php echo $present; ?>
                </h2>

            </div>

        </div>


        <!-- LATE -->

        <div class="stat-card">

            <div class="stat-icon late">
                ◷
            </div>

            <div>

                <p>Late</p>

                <h2>
                    <?php echo $late; ?>
                </h2>

            </div>

        </div>


        <!-- ABSENT -->

        <div class="stat-card">

            <div class="stat-icon absent">
                !
            </div>

            <div>

                <p>Absent</p>

                <h2>
                    <?php echo $absent; ?>
                </h2>

            </div>

        </div>


    </section>


    <!-- ==============================
         ATTENDANCE RECORDS
    ================================= -->

    <section class="attendance-card">


        <!-- CARD HEADER -->

        <div class="attendance-header">

            <div>

                <h2>
                    Attendance Records
                </h2>

                <p>
                    View and manage all student attendance.
                </p>

            </div>


            <!-- SEARCH -->

            <form
                method="GET"
                action="index.php"
                class="search-form"
            >

                <input
                    type="text"
                    name="search"
                    class="search-input"
                    placeholder="Search student ID or name..."
                    value="<?php echo htmlspecialchars($search); ?>"
                >


                <button
                    type="submit"
                    class="search-button"
                >
                    Search
                </button>


                <?php if (!empty($search)): ?>

                    <a
                        href="index.php"
                        class="clear-button"
                    >
                        Clear
                    </a>

                <?php endif; ?>

            </form>

        </div>


        <!-- TABLE -->

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Student</th>

                        <th>Date</th>

                        <th>Status</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if (mysqli_num_rows($result) > 0):

                    while ($row = mysqli_fetch_assoc($result)):

                        $initial = strtoupper(
                            substr(
                                $row['student_name'],
                                0,
                                1
                            )
                        );

                        $formattedDate = date(
                            "M d, Y",
                            strtotime(
                                $row['attendance_date']
                            )
                        );

                        $day = date(
                            "l",
                            strtotime(
                                $row['attendance_date']
                            )
                        );

                ?>


                    <tr>


                        <!-- ID -->

                        <td>

                            <span class="record-number">

                                #<?php echo $row['id']; ?>

                            </span>

                        </td>


                        <!-- STUDENT -->

                        <td>

                            <div class="student">

                                <div class="student-avatar">

                                    <?php echo $initial; ?>

                                </div>


                                <div>

                                    <strong class="student-name">

                                        <?php

                                        echo htmlspecialchars(
                                            $row['student_name']
                                        );

                                        ?>

                                    </strong>


                                    <span class="student-id">

                                        ID:
                                        <?php

                                        echo htmlspecialchars(
                                            $row['student_id']
                                        );

                                        ?>

                                    </span>

                                </div>

                            </div>

                        </td>


                        <!-- DATE -->

                        <td>

                            <strong class="date">

                                <?php echo $formattedDate; ?>

                            </strong>

                            <span class="day">

                                <?php echo $day; ?>

                            </span>

                        </td>


                        <!-- STATUS -->

                        <td>


                            <?php if ($row['status'] == "Present"): ?>

                                <span class="status status-present">

                                    <span class="status-dot"></span>

                                    Present

                                </span>


                            <?php elseif ($row['status'] == "Late"): ?>

                                <span class="status status-late">

                                    <span class="status-dot"></span>

                                    Late

                                </span>


                            <?php else: ?>

                                <span class="status status-absent">

                                    <span class="status-dot"></span>

                                    Absent

                                </span>

                            <?php endif; ?>


                        </td>


                        <!-- ACTIONS -->

                        <td>

                            <div class="actions">


                                <a
                                    href="edit.php?id=<?php echo $row['id']; ?>"
                                    class="edit-button"
                                    title="Edit"
                                >
                                    Edit
                                </a>


                                <a
                                    href="delete.php?id=<?php echo $row['id']; ?>"
                                    class="delete-button"
                                    title="Delete"
                                    onclick="return confirm('Are you sure you want to delete this attendance record?');"
                                >
                                    Delete
                                </a>


                            </div>

                        </td>


                    </tr>


                <?php

                    endwhile;

                else:

                ?>


                    <tr>

                        <td
                            colspan="5"
                            class="empty-state"
                        >

                            <div class="empty-icon">
                                ○
                            </div>

                            <h3>
                                No attendance records found
                            </h3>


                            <?php if (!empty($search)): ?>

                                <p>

                                    No records found for
                                    <strong>
                                        <?php echo htmlspecialchars($search); ?>
                                    </strong>

                                </p>

                            <?php else: ?>

                                <p>
                                    There are currently no attendance records.
                                </p>

                            <?php endif; ?>


                            <a
                                href="add.php"
                                class="empty-add-button"
                            >
                                + Add Attendance
                            </a>

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>

            </table>

        </div>


        <!-- CARD FOOTER -->

        <div class="attendance-footer">

            <span>

                Showing
                <strong>
                    <?php echo mysqli_num_rows($result); ?>
                </strong>
                record(s)

            </span>


            <span>

                Total Records:
                <strong>
                    <?php echo $total; ?>
                </strong>

            </span>

        </div>


    </section>


    <!-- FOOTER -->

  
  <!-- FOOTER -->
<footer class="footer">

    <div class="footer-content">

        <div class="footer-brand">
            <div class="footer-logo">✓</div>

            <div>
                <h3>Student Attendance</h3>
                <p>Attendance Management System</p>
            </div>
        </div>

        <div class="footer-info">
            <p>
                &copy; <?php echo date("Y"); ?> Student Attendance Management System
            </p>

            <p class="footer-tech">
                Powered by PHP &bull; MySQL &bull; XAMPP
            </p>
        </div>

    </div>

    <div class="footer-bottom">
        <span>Attendance Records Management</span>
        <span>All Rights Reserved.</span>
    </div>

</footer>

</main>


</body>

</html>


<?php

mysqli_close($conn);

?>
```
