```php
<?php

include 'db.php';


// Check if ID exists

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location: index.php");

    exit();

}


$id = (int) $_GET['id'];


// Delete record

$sql = "DELETE FROM attendance WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);


// Return to main page

header("Location: index.php");

exit();

?>
```
