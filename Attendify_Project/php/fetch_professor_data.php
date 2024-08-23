<?php
header('Content-Type: application/json');
require '../db_connect.php';

$query = 'SELECT lastname, firstname, email, phone, gender, age FROM professor';
$result = $mysqli->query($query);

$professor = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $professor[] = $row;
    }
}

echo json_encode($professor);

$mysqli->close();
?>
