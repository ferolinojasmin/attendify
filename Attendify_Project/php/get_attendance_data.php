<?php
session_start();
require '../db_connect.php';

// Ensure only logged-in professors can access this page
if (!isset($_SESSION['prof_id'])) {
    header("Location: ../works/log_in_form.html?error=notloggedin");
    exit();
}

// Check if class_no and date are provided via GET request
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['class_no']) && isset($_GET['date'])) {
    $class_no = $_GET['class_no'];
    $date = $_GET['date'];

    // Query to fetch attendance data including student details
    $stmt = $conn->prepare("SELECT a.attendance_id, a.date, a.status, a.class_no, a.student_no, s.firstname AS first_name, s.lastname AS last_name
                            FROM attendance a
                            INNER JOIN student s ON a.student_no = s.student_number
                            WHERE a.class_no = ? AND a.date = ?");
    $stmt->bind_param("ss", $class_no, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if attendance records were found
    if ($result->num_rows > 0) {
        // Start JSON response
        $attendance_data = array();

        // Fetch data and store in array
        while ($row = $result->fetch_assoc()) {
            $attendance_data[] = array(
                'attendance_id' => $row['attendance_id'],
                'date' => $row['date'],
                'status' => $row['status'],
                'class_no' => $row['class_no'],
                'student_no' => $row['student_no'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name']
            );
        }

        // Output JSON response
        header('Content-Type: application/json');
        echo json_encode($attendance_data);
    } else {
        echo json_encode(array('message' => 'No attendance records found for this class and date.'));
    }
} else {
    echo json_encode(array('message' => 'Invalid request.'));
}
?>
