<?php
header('Content-Type: application/json');
require '../db_connect.php';

$student_no = $_GET['student_no'];
$date = $_GET['date'];

$stmt = $conn->prepare("SELECT class_no AS subject, 
                                  COUNT(CASE WHEN status = 'Present' THEN 1 END) AS present, 
                                  COUNT(CASE WHEN status = 'Absent' THEN 1 END) AS absent 
                           FROM attendance 
                           WHERE student_no = ? AND date = ? 
                           GROUP BY class_no");
$stmt->bind_param("ss", $student_no, $date);
$stmt->execute();
$result = $stmt->get_result();

$data = [['Subject', 'Present', 'Absent']]; 
while ($row = $result->fetch_assoc()) {
    $data[] = [
        $row['subject'],
        (int) $row['present'],
        (int) $row['absent']  
    ];
}

echo json_encode($data);
?>
