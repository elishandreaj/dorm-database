<?php
include 'database.php';

session_start();

if(isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password, role, employee_type, id FROM login WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password, $role, $employee_type, $id);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['id'] = $id;

            if ($role === 'student') {
                header("Location: studentDashboard.php");
                exit();
            } else if ($role === 'employee') {
                if ($employee_type === 'staff') {
                    header("Location: staffDashboard.php");
                    exit();
                } else if ($employee_type === 'dorm_manager') {
                    header("Location: managerDashboard.php");
                    exit();
                }
            }            
        } else {
            echo "<script>alert('Invalid password');</script>";
            echo "<script>window.setTimeout(function(){ window.location = 'user.html'; }, 500);</script>";
        }
    } else {
        echo "<script>alert('Invalid username or password');</script>";
        echo "<script>window.setTimeout(function(){ window.location = 'user.html'; }, 500);</script>";
    }
} else {
    echo "<script>alert('Please provide both username and password');</script>";
    echo "<script>window.setTimeout(function(){ window.location = 'user.html'; }, 500);</script>";
}

if(isset($stmt)) {
    $stmt->close();
}

$conn->close();
?>