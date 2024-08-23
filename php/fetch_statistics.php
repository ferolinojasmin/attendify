<?php
header('Content-Type: application/json');
require '../db_connect.php';

$prof_id = $_GET['prof_id'] ?? null;

if (!$prof_id) {
    echo json_encode(['error' => 'Professor ID is required']);
    exit;
}

$attendance_stmt = $conn->prepare("
    SELECT date, 
           SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present_count,
           SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent_count
    FROM attendance
    WHERE class_no IN (
        SELECT class_no
        FROM class
        WHERE prof_id = ?
    )
    GROUP BY date
");
$attendance_stmt->bind_param("s", $prof_id);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();
$attendance_data = $attendance_result->fetch_all(MYSQLI_ASSOC);

$gender_stmt = $conn->prepare("
    SELECT s.gender, COUNT(*) AS count
    FROM student s
    JOIN class c ON s.student_number = c.student_no
    WHERE c.class_no IN (
        SELECT class_no
        FROM class
        WHERE prof_id = ?
    )
    GROUP BY s.gender
");
$gender_stmt->bind_param("s", $prof_id);
$gender_stmt->execute();
$gender_result = $gender_stmt->get_result();
$gender_data = $gender_result->fetch_all(MYSQLI_ASSOC);

$age_stmt = $conn->prepare("
    SELECT s.age, COUNT(*) AS count
    FROM student s
    JOIN class c ON s.student_number = c.student_no
    WHERE c.class_no IN (
        SELECT class_no
        FROM class
        WHERE prof_id = ?
    )
    GROUP BY s.age
");
$age_stmt->bind_param("s", $prof_id);
$age_stmt->execute();
$age_result = $age_stmt->get_result();
$age_data = $age_result->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'attendance' => $attendance_data,
    'genderDistribution' => $gender_data,
    'ageDistribution' => $age_data
]);

$attendance_stmt->close();
$gender_stmt->close();
$age_stmt->close();
$conn->close();
?>
