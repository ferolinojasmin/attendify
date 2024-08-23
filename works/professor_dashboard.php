<?php
session_start();
require '../db_connect.php';

if (!isset($_SESSION['prof_id'])) {
  header("Location: ../works/log_in_form.html");
  exit;
}

$prof_id = $_SESSION['prof_id'];

function getProfessorAndClassDetails($conn, $prof_id)
{
  $stmt = $conn->prepare("
        SELECT p.prof_id, CONCAT(p.firstname, ' ', p.lastname) AS prof_name, c.class_no, co.course_code
        FROM professor p
        JOIN class c ON p.prof_id = c.prof_id
        JOIN courses co ON c.courses_id = co.courses_id
        WHERE p.prof_id = ?
    ");
  $stmt->bind_param("s", $prof_id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}

function getAttendanceRecords($conn, $class_no)
{
  $stmt = $conn->prepare("
        SELECT a.date, a.status, COUNT(*) AS count
        FROM attendance a
        WHERE a.class_no = ?
        GROUP BY a.date, a.status
    ");
  $stmt->bind_param("s", $class_no);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$profData = getProfessorAndClassDetails($conn, $prof_id);

if ($profData) {
  $prof_name = $profData['prof_name'];
  $class_no = $profData['class_no'];
  $course_code = $profData['course_code'];

  $attendanceRecords = getAttendanceRecords($conn, $class_no);
} else {
  $prof_name = 'Not found';
  $class_no = 'Not found';
  $course_code = 'Not found';
  $attendanceRecords = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Professor Dashboard</title>
  <link rel="stylesheet" href="../styles/professor_dashboard_styles.css">
  <script src="../js/professor_dashboard.js" defer></script>
</head>

<body>
  <header>
    <div id="header">
      <h1>Professor Dashboard</h1>
      <div id="header_buttons">
        <button class="header-btn" id="logout">Logout</button>
        <button class="header-btn" id="statistics_button">View Statistics</button>
      </div>
    </div>
  </header>
  <section id="content">
    <section id="professor_info">
      <h2>Professor Information</h2>
      <p><strong>Professor:</strong> <span id="prof_name"><?php echo htmlspecialchars($prof_name); ?></span></p>
      <p><strong>Professor ID:</strong> <span id="prof_id"><?php echo htmlspecialchars($prof_id); ?></span></p>
      <p><strong>Class Number:</strong> <span id="class_no"><?php echo htmlspecialchars($class_no); ?></span></p>
      <p><strong>Course Code:</strong> <span id="course_name"><?php echo htmlspecialchars($course_code); ?></span></p>
    </section>
    <section id="attendance_management">
      <h2>Attendance Management</h2>
      <table id="attendance_table">
        <thead>
          <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      <button class="action-btn" id="edit_all_button">Edit All</button>
      <button class="action-btn" id="save_all_button" disabled>Save All Changes</button>
    </section>
  </section>
</body>

</html>