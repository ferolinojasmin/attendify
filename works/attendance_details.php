<?php
session_start();
if (!isset($_SESSION['student_number'])) {
    header("Location: ./works/log_in_form.html");
    exit();
}
require '../db_connect.php';

$student_number = $_SESSION['student_number'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_date = $_POST['date'];

    if ($selected_date === 'total') {
        // Fetch total attendance data for all classes
        $stmt = $conn->prepare("
            SELECT class_no, status, COUNT(*) AS count
            FROM attendance
            WHERE student_no = ?
            GROUP BY class_no, status
        ");
        $stmt->bind_param("s", $student_number);
        $stmt->execute();
        $result = $stmt->get_result();

        $attendance_data = [];
        $total_attendance = ['Present' => 0, 'Absent' => 0];
        while ($row = $result->fetch_assoc()) {
            $class_no = $row['class_no'];
            $status = $row['status'];
            if (!isset($attendance_data[$class_no])) {
                $attendance_data[$class_no] = ['Present' => 0, 'Absent' => 0];
            }
            $attendance_data[$class_no][$status] = $row['count'];
            $total_attendance[$status] += $row['count'];
        }

        echo json_encode([
            'date' => 'Total',
            'attendance' => $attendance_data,
            'total_attendance' => $total_attendance
        ]);
    } else {
        // Fetch attendance data for a specific date
        $stmt = $conn->prepare("
            SELECT class_no, status, COUNT(*) AS count
            FROM attendance
            WHERE student_no = ? AND date = ?
            GROUP BY class_no, status
        ");
        $stmt->bind_param("ss", $student_number, $selected_date);
        $stmt->execute();
        $result = $stmt->get_result();

        $attendance_data = [];
        $total_attendance = ['Present' => 0, 'Absent' => 0];
        while ($row = $result->fetch_assoc()) {
            $class_no = $row['class_no'];
            $status = $row['status'];
            if (!isset($attendance_data[$class_no])) {
                $attendance_data[$class_no] = ['Present' => 0, 'Absent' => 0];
            }
            $attendance_data[$class_no][$status] = $row['count'];
            $total_attendance[$status] += $row['count'];
        }

        echo json_encode([
            'date' => $selected_date,
            'attendance' => $attendance_data,
            'total_attendance' => $total_attendance
        ]);
    }
    exit();
}

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
    <title>Attendance Details</title>
    <link rel="stylesheet" href="../styles/attendance_details_styles.css" />
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        const dates = <?php echo $dates_json; ?>;
    </script>
</head>
<body>
    <header>
        <h2 id="header">Attendance Details</h2>
        <div class="gold-line"></div>
    </header>
    <button id="back-button" onclick="window.location.href='./student_dashboard.php'">Back</button>
    <div id="content">
        <form id="date-form">
            <label for="date">Select Date:</label>
            <select name="date" id="date">
                <option value="total">Total Attendance</option> 
                <?php foreach ($dates as $date) : ?>
                    <option value="<?php echo htmlspecialchars($date); ?>"><?php echo htmlspecialchars($date); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="action-btn">View Attendance</button>
        </form>
        <div id="chart_div" style="width: 100%; height: 500px; margin: 0 auto;"></div>
        <div id="details_div"></div> 
    </div>
    <script src="../js/attendance_details_script.js" defer></script>
</body>
</html>
