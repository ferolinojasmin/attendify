<?php
session_start();
require '../db_connect.php';

if (!isset($_SESSION['prof_id'])) {
    header("Location: ../works/log_in_form.html");
    exit;
}

$prof_id = $_SESSION['prof_id'];

function updateAttendance($conn, $student_no, $class_no, $status, $date)
{
    $stmt = $conn->prepare("UPDATE attendance SET status = ?, date = ? WHERE student_no = ? AND class_no = ?");
    $stmt->bind_param("ssss", $status, $date, $student_no, $class_no);

    if ($stmt->execute()) {
        return "success";
    } else {
        return "error: " . $stmt->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_no = $_POST['student_no'];
    $class_no = $_POST['class_no'];
    $status = $_POST['status'];
    $date = $_POST['date'];

    echo updateAttendance($conn, $student_no, $class_no, $status, $date);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("
        SELECT p.prof_id, CONCAT(p.firstname, ' ', p.lastname) AS prof_name, c.class_no, co.course_code
        FROM professor p
        JOIN class c ON p.prof_id = c.prof_id
        JOIN courses co ON c.courses_id = co.courses_id
        WHERE p.prof_id = ?
    ");
    $stmt->bind_param("s", $prof_id);
    $stmt->execute();
    $prof_result = $stmt->get_result();
    $prof_data = $prof_result->fetch_assoc();

    if ($prof_data) {
        $prof_name = $prof_data['prof_name'];
        $class_no = $prof_data['class_no'];
        $course_code = $prof_data['course_code'];

        $attendance_stmt = $conn->prepare("
            SELECT s.student_number AS student_no, s.firstname, s.lastname, a.status, a.date
            FROM attendance a
            JOIN student s ON a.student_no = s.student_number
            WHERE a.class_no = ?
        ");
        $attendance_stmt->bind_param("s", $class_no);
        $attendance_stmt->execute();
        $attendance_result = $attendance_stmt->get_result();
        $attendance_data = $attendance_result->fetch_all(MYSQLI_ASSOC);

        header('Content-Type: application/json');
        echo json_encode([
            'prof_id' => $prof_id,
            'prof_name' => $prof_name,
            'class_no' => $class_no,
            'course_code' => $course_code,
            'attendance' => $attendance_data
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'prof_id' => 'Not found',
            'prof_name' => 'Not found',
            'class_no' => 'Not found',
            'course_code' => 'Not found',
            'attendance' => []
        ]);
    }
}
?>
