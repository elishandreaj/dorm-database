<?php
session_start();

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['staff_id'])) {
        $staff_id = $_POST['staff_id'];

        include 'database.php';
        
        $stmt = $conn->prepare("DELETE FROM staff WHERE staff_id = ?");
        $stmt->bind_param("i", $staff_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Staff deleted successfully');</script>";
        } else {
            echo "<script>alert('Failed to delete staff');</script>";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "<script>alert('Staff ID not provided');</script>";
    }
} else {
    echo "<script>alert('Invalid request');</script>";
}

echo "<script>window.setTimeout(function(){ window.location = 'viewStaffManager.php'; }, 0);</script>";
exit();
?>