<?php
session_start();
if (!isset($_SESSION['student_number'])) {
    header("Location: ../works/log_in_form.html");
    exit();
}
require '../db_connect.php';

$student_number = $_SESSION['student_number'];
$stmt = $conn->prepare("SELECT * FROM student WHERE student_number = ?");
$stmt->bind_param("s", $student_number);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

$stmt = $conn->prepare("SELECT DISTINCT date FROM attendance WHERE student_no = ?");
$stmt->bind_param("s", $student_number);
$stmt->execute();
$dates_result = $stmt->get_result();
$dates = [];
while ($row = $dates_result->fetch_assoc()) {
    $dates[] = $row['date'];
}

$dates_json = json_encode($dates);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Information</title>
    <link rel="stylesheet" href="../styles/student_dashboard_styles.css" />
    <script type="text/javascript" src="../js/student_dashboard_script.js" defer></script>
</head>
<body>
    <header>
        <h2 id="header">Student Information</h2>
        <div class="gold-line"></div>
        <div id="buttons">
            <button id="logout">Log Out</button>
            <a href="attendance_details.php"><button id="viewAttendance">View Attendance Details</button></a>
        </div>
    </header>
    <div id="content">
        <div id="info">
            <div class="greeting">
                <h1 id="student-name">Hello, <?php echo htmlspecialchars($_SESSION['student_name']); ?></h1>
            </div>
            <div class="student-info">
                <div id="name">Name: <?php echo htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?></div>
                <div id="gender">Gender: <?php echo htmlspecialchars($student['gender']); ?></div>
                <div id="email">Email: <?php echo htmlspecialchars($student['email']); ?></div>
                <div id="section">Section: <?php echo htmlspecialchars($_SESSION['section']); ?></div>
                <div id="studentnumber">Student Number: <?php echo htmlspecialchars($student['student_number']); ?></div>
                <div id="contactnumber">Contact Number: <?php echo htmlspecialchars($student['phone']); ?></div>
            </div>
        </div>
    </div>
</body>
</html>
