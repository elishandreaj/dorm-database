<?php
include 'database.php';

$username = $_POST['username'] ?? null;
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$role = $_POST['role'] ?? null;
$password = $_POST['password'] ?? null;
$employee_type = $_POST['employee_type'] ?? null;
$dorm_id = $_POST['dorm'] ?? null;

if (empty($_POST['username']) || empty($_POST['password'])) {
    echo "<script>alert('Please provide both username and password.');</script>";
    header("Location: registration.html");
    exit();
}

$username = $_POST['username'];
$name = $_POST['name'];
$email = $_POST['email'];
$role = $_POST['role'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$dorm_id = $_POST['dorm'];

if ($role === 'student') {
    $course = $_POST['course'] ?? null;
    $year_level = $_POST['year_level'] ?? null;
    $room_number = $_POST['room_number'] ?? null;
    $fees = $_POST['fees'] ?? null;

    if (empty($course) || empty($year_level) || empty($room_number) || empty($fees)) {
        echo "<script>alert('All fields are required for student.');</script>";
    }
    $stmt = $conn->prepare("INSERT INTO student (student_id, name, email, course, year_level, room_number, fees, dorm_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiisi", $username, $name, $email, $course, $year_level, $room_number, $fees, $dorm_id);
    
} else if ($role === 'employee') {
    $employee_type = $_POST['employee_type'] ?? null;
    $duty = $_POST['duty'] ?? null;

    if (empty($employee_type) || empty($duty)) {
        echo "<script>alert('All fields are required for employee.');</script>";
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
    // Hash the password
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

     $stmt = $conn->prepare("INSERT INTO login (username, password, role, employee_type, id) VALUES (?, ?, ?, ?, ?)");
     $stmt->bind_param("sssss", $username, $password, $role, $employee_type, $username);
 
     if ($stmt->execute()) {
         echo "Login details saved successfully.<br>";
     } else {
         echo "Error: " . $stmt->error . "<br>";
     }
 } else {
     echo "Error: " . $stmt->error . "<br>";
 }
 
 $stmt->close();
 $conn->close();
 header("Location: login.php");

if ($stmt->execute()) {
    echo "<script>alert('User registered successfuly.');</script>";
} else {
    echo "Error: " . $stmt->error . "<br>";
}
$stmt->close();

$stmt = $conn->prepare("INSERT INTO login (username, password, role, employee_type, id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $password, $role, $employee_type, $username);

if ($stmt->execute()) {
    echo "<script>alert('Login details saved successfully.');</script>";
    echo "<script>window.setTimeout(function(){ window.location = 'user.html'; }, 0);</script>";
} else {
    echo "Error: " . $stmt->error . "<br>";
}
$stmt->close();

$conn->close();
?>
