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
    <link rel="stylesheet" href="style.css">
    <title>Staff Dashboard</title>
</head>
<body>
    <div class="container">
        <div class="profile-picture">
            <?php if (!empty($staffData['picture'])): ?>
                <img src="<?php echo $staffData['picture']; ?>" alt="Profile Picture" style="width:150px;height:150px;text-align: center">
            <?php else: ?>
                <img src="icon.png" alt="Default Profile Picture" style="width:150px;height:150px;">
            <?php endif; ?>
        </div>

        <div class="staff-info">
            <h1>Welcome, <?php echo $staffData['name']; ?></h1>
            <p>Staff ID: <?php echo $staffData['staff_id']; ?></p>
            <p>Email: <?php echo $staffData['email']; ?></p>
            <p>Duty: <?php foreach($_POST['duty'] as $value) {
                echo $value;
                }
            ?></p>
            <p>Dorm: <?php echo $staffData['dorm_name']; ?></p>
        </div>
        
        <div class="action-buttons">
            <a href="editProfileStaff.php"><button>Edit Profile</button></a><br><br>
            <a href="viewStudentsStaff.php"><button>View Students</button></a><br><br>
            <a href="user.html"><button>Logout</button></a>

        </div>
    </div>
</body>
</html>