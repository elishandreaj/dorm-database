<?php
session_start();

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['student_id'])) {
        $student_id = $_POST['student_id'];

        include 'database.php';
        
        $stmt = $conn->prepare("DELETE FROM student WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Student deleted successfully');</script>";
        } else {
            echo "<script>alert('Failed to delete student');</script>";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "<script>alert('Student ID not provided');</script>";
    }
} else {
    echo "<script>alert('Invalid request');</script>";
}

header("Location: viewStudentsManager.php");
exit();
