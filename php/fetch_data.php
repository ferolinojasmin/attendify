<?php
header('Content-Type: application/json');
require '../db_connect.php';

$category = $_GET['category'] ?? '';
$type = $_GET['type'] ?? '';

if ($type === 'chart') {
    switch ($category) {
        case 'Students':
        case 'Professors':
            $table = ($category === 'Students') ? 'student' : 'professor';
            $query = "SELECT gender, COUNT(*) as count FROM $table GROUP BY gender";
            break;

        case 'Total Students':
            $query = "SELECT COUNT(*) as total FROM student";
            break;

        default:
            echo json_encode([]);
            exit;
    }

    $result = $conn->query($query);
    $data = [];

    if ($category === 'Total Students') {
        $row = $result->fetch_assoc();
        $data[] = ['Category', 'Count'];
        $data[] = ['Total Students', (int) $row['total']];
    } else {
        $data[] = [$category === 'Students' ? 'Gender' : 'Gender', 'Count'];
        while ($row = $result->fetch_assoc()) {
            $data[] = [$row['gender'], (int) $row['count']];
        }
    }

    echo json_encode($data);
} elseif ($type === 'list') {
    $listType = $_GET['listType'] ?? 'professor';
    $query = $listType === 'student'
        ? 'SELECT lastname, firstname, email, phone, gender, age FROM student'
        : 'SELECT lastname, firstname, email, phone, gender, age FROM professor';

    $result = $conn->query($query);
    $data = [];

    if ($result->num_rows > 0) {
        $index = 1;
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'index' => $index++,
                'lastname' => htmlspecialchars($row['lastname']),
                'firstname' => htmlspecialchars($row['firstname']),
                'email' => htmlspecialchars($row['email']),
                'phone' => htmlspecialchars($row['phone']),
                'gender' => htmlspecialchars($row['gender']),
                'age' => htmlspecialchars($row['age'])
            ];
        }
    }

    echo json_encode($data);
}

$conn->close();
?>