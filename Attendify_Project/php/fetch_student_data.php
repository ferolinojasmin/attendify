<?php
header('Content-Type: application/json');
require '../db_connect.php';
$query = 'SELECT lastname, firstname, email, phone, gender, age FROM student';
$result = $mysqli->query($query);

$student = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Output data as JSON
echo json_encode($student);

$mysqli->close();
?>
