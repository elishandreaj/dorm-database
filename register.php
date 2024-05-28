<?php
include 'database.php';

$username = $_POST['username'] ?? null;
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$role = $_POST['role'] ?? null;
$password = $_POST['password'] ?? null;
// $employee_type = $_POST['employee_type'] ?? null;
$dorm_id = $_POST['dorm'] ?? null;

if (empty($username) || empty($password)) {
    echo "Please provide both username and password.<br>";
    exit();
}

// Hash the password
$password_hashed = password_hash($password, PASSWORD_DEFAULT);

if ($role === 'student') {
    $course = $_POST['course'] ?? null;
    $year_level = $_POST['year_level'] ?? null;
    $room_number = $_POST['room_number'] ?? null;
    $fees = $_POST['fees'] ?? null;

    if (empty($course) || empty($year_level) || empty($room_number) || empty($fees)) {
        die('All fields are required for student.');
    }

    $stmt = $conn->prepare("INSERT INTO student (student_id, name, email, course, year_level, room_number, fees, dorm_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiisi", $username, $name, $email, $course, $year_level, $room_number, $fees, $dorm_id);
} else if ($role === 'employee') {
    $employee_type = $_POST['employee_type'] ?? null;
    $duty = $_POST['duty'] ?? null;

    if (empty($employee_type) || empty($duty)) {
        die('All fields are required for employee.');
    }

    if ($employee_type === 'staff') {
        $stmt = $conn->prepare("INSERT INTO staff (staff_id, name, email, duty, dorm_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $username, $name, $email, $duty, $dorm_id);
    } else if ($employee_type === 'dorm_manager') {
        $stmt = $conn->prepare("INSERT INTO manager (manager_id, name, email, duty, dorm_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $username, $name, $email, $duty, $dorm_id);
    }
}

if ($stmt->execute()) {
    echo "User registered successfully.<br>";
} else {
    echo "Error: " . $stmt->error . "<br>";
}
$stmt->close();

// $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO login (username, password, role, employee_type, id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $password, $role, $employee_type, $username);

if ($stmt->execute()) {
    echo "Login details saved successfully.<br>";
} else {
    echo "Error: " . $stmt->error . "<br>";
}
$stmt->close();

$conn->close();
header("Location: login.php");
?>
