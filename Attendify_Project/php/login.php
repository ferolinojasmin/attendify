<?php
session_start(); 

require '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $special_key = $_POST['student_number'];
    $password = $_POST['password'];

    if (empty($special_key) || empty($password)) {
        header("Location: ../works/log_in_form.html?error=emptyfields");
        exit();
    }

    if (strpos($special_key, "S") === 0) {
        $stmt = $conn->prepare("SELECT * FROM student WHERE student_number = ? AND password = ?");
    } elseif (strpos($special_key, "P") === 0) {
        $stmt = $conn->prepare("SELECT * FROM professor WHERE prof_id = ? AND password = ?");
    } elseif (strpos($special_key, "A") === 0) {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE admin_id = ? AND password = ?");
    } else {
        header("Location: ../works/log_in_form.html?error=invalidkey");
        exit();
    }

    $stmt->bind_param("ss", $special_key, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (strpos($special_key, "S") === 0) {
            $_SESSION['student_name'] = $row['firstname'] . ' ' . $row['lastname'];
            $_SESSION['gender'] = $row['gender'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['section'] = 'N/A';
            $_SESSION['student_number'] = $row['student_number'];
            $_SESSION['contact_number'] = $row['phone'];
            header("Location: ../works/student_dashboard.php");
        } elseif (strpos($special_key, "P") === 0) {
            $_SESSION['prof_id'] = $row['prof_id'];
            header("Location: ../works/professor_dashboard.php");
        } elseif (strpos($special_key, "A") === 0) {
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['admin_name'] = $row['firstname'] . ' ' . $row['lastname'];
            header("Location: ../works/administrator_dashboard.php");
        }
        exit();
    } else {
        header("Location: ../works/log_in_form.html?error=loginfailed");
        exit();
    }
}
?>
