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
    echo "<script>window.setTimeout(function(){ window.location = 'registration.html'; }, 0);</script>";
    exit;
}

$username = $_POST['username'];
$name = $_POST['name'];
$email = $_POST['email'];
$role = $_POST['role'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$dorm_id = $_POST['dorm'];

$stmt = $conn->prepare("SELECT * FROM login WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "<script>alert('Username is already registered. Please choose a different username.');</script>";
    echo "<script>window.setTimeout(function(){ window.location = 'registration.html'; }, 0);</script>";
    exit;
}
$stmt->close();

if ($role === 'employee' && $_POST['employee_type'] === 'dorm_manager') {
    $stmt = $conn->prepare("SELECT * FROM manager WHERE dorm_id = ?");
    $stmt->bind_param("i", $dorm_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<script>alert('A dorm manager is already registered for this dorm.');</script>";
        echo "<script>window.setTimeout(function(){ window.location = 'registration.html'; }, 0);</script>";
        exit;
    }
    $stmt->close();
}

$duties = isset($_POST['duty']) ? json_encode($_POST['duty']) : '';

if ($role === 'employee') {
    $requiredFields = ['employee_type', 'duty'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            echo "<script>alert('All fields are required for employee.');</script>";
            echo "<script>window.setTimeout(function(){ window.location = 'registration.html'; }, 0);</script>";
            exit;
        }
    }

    if ($_POST['employee_type'] === 'staff') {
        $stmt = $conn->prepare("INSERT INTO staff (staff_id, name, email, duty, dorm_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $username, $name, $email, $duties, $dorm_id);
    } else if ($_POST['employee_type'] === 'dorm_manager') {
        $stmt = $conn->prepare("INSERT INTO manager (manager_id, name, email, duty, dorm_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $username, $name, $email, $duties, $dorm_id);
    }
} else if ($role === 'student') {
    $requiredFields = ['course', 'year_level', 'room_number', 'fees'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            echo "<script>alert('All fields are required for student.');</script>";
            echo "<script>window.setTimeout(function(){ window.location = 'registration.html'; }, 0);</script>";
            exit;
        }
    }
    $stmt = $conn->prepare("INSERT INTO student (student_id, name, email, course, year_level, room_number, fees, dorm_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiisi", $username, $name, $email, $_POST['course'], $_POST['year_level'], $_POST['room_number'], $_POST['fees'], $dorm_id);
}

if ($stmt->execute()) {
    echo "<script>alert('User registered successfully.');</script>";
} else {
    echo "<script>alert('Error: ' . $stmt->error);</script>";
}
$stmt->close();

$stmt = $conn->prepare("INSERT INTO login (username, password, role, employee_type, id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $password, $role, $_POST['employee_type'], $username);

if ($stmt->execute()) {
    echo "<script>alert('Login details saved successfully.');</script>";
    echo "<script>window.setTimeout(function(){ window.location = 'user.html'; }, 0);</script>";
} else {
    echo "<script>alert('Error: ' . $stmt->error);</script>";
}
$stmt->close();

$conn->close();
?>