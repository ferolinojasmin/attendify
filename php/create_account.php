<?php

require '../db_connect.php';

// Get form data
$firstname = $_POST['firstname'];
$middlename = $_POST['middlename'];
$lastname = $_POST['lastname'];
$birthday = $_POST['birthday'];
$age = $_POST['age'];
$gender = $_POST['gender'];
$address = $_POST['address'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$courses = $_POST['courses'];
$status = $_POST['status'];
$password = $_POST['password'];

// Generate prof_id
$sql = "SELECT MAX(id) as max_id FROM professor";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$last_id = $row['max_id'];
$new_id = sprintf("P%05d", ($last_id ? $last_id + 1 : 1));

// Insert data into database
$sql = "INSERT INTO professor (firstname, middlename, lastname, birthday, age, gender, address, email, phone, course, status, password, prof_id)
VALUES ('$firstname', '$middlename', '$lastname', '$birthday', '$age', '$gender', '$address', '$email', '$phone', '$courses', '$status', '$password', '$new_id')";

if ($conn->query($sql) === TRUE) {
    echo "Sign up successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
