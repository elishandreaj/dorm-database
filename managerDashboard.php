<?php
session_start();

if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'database.php';

$stmt = $conn->prepare("SELECT manager.*, dorm.name as dorm_name FROM manager LEFT JOIN dorm ON manager.dorm_id = dorm.dorm_id WHERE manager_id = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo "<script>alert('Dorm Manager not found');</script>";
    exit();
}

$managerData = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dorm Manager Dashboard</title>
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
            <h1>Welcome, <?php echo $managerData['name']; ?></h1>
            <p>Staff ID: <?php echo $managerData['manager_id']; ?></p>
            <p>Email: <?php echo $managerData['email']; ?></p>
            <p>Duty: <?php echo $managerData['duty']; ?></p>
            <p>Dorm: <?php echo $managerData['dorm_name']; ?></p>
        </div>
        
        <div class="action-buttons">
            <a href="editProfileManager.php"><button>Edit Profile</button></a>
            <a href="viewStaffManager.php"><button>View Staffs</button>
            <a href="viewStudentsManager.php"><button>View Students in My Dorm</button>
            <a href="viewAllManager.php"><button>View All Students</button>
        </div>
    </div>
</body>
</html>
