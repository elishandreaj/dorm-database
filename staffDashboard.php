<?php
session_start();

if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'database.php';

$stmt = $conn->prepare("SELECT staff.*, dorm.name as dorm_name FROM staff LEFT JOIN dorm ON staff.dorm_id = dorm.dorm_id WHERE staff_id = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo "<script>alert('Staff not found');</script>";
    exit();
}

$staffData = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
</head>
<body>
    <div class="container">
        <div class="profile-picture">
                <?php if (!empty($staffData['picture'])): ?>
                    <img src="<?php echo $staffData['picture']; ?>" alt="Profile Picture" style="width:150px;height:150px;">
                <?php else: ?>
                    <div style="width:150px;height:150px;background-color:black;"></div>
                <?php endif; ?>
            </div>

        <div class="staff-info">
            <h1>Welcome, <?php echo $staffData['name']; ?></h1>
            <p>Staff ID: <?php echo $staffData['staff_id']; ?></p>
            <p>Email: <?php echo $staffData['email']; ?></p>
            <p>Duty: <?php echo $staffData['duty']; ?></p>
            <p>Dorm: <?php echo $staffData['dorm_name']; ?></p>
        </div>
        
        <div class="action-buttons">
            <a href="editProfileStaff.php"><button>Edit Profile</button></a>
            <a href="viewStudentsStaff.php"><button>View Students</button>
        </div>
    </div>
</body>
</html>
