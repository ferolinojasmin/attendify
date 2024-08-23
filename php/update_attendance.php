<?php
require '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_no = $_POST['student_no'];
    $class_no = $_POST['class_no'];
    $status = $_POST['status'];

    if (empty($student_no) || empty($class_no) || empty($status)) {
        echo 'error';
        exit();
    }

    $stmt = $conn->prepare("
        UPDATE attendance
        SET status = ?
        WHERE student_no = ? AND class_no = ?
    ");
    $stmt->bind_param("sss", $status, $student_no, $class_no);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
