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
$managerData['duty'] = json_decode($managerData['duty'], true);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Dorm Manager Dashboard</title>
    <style>
        .staff-info li {
            color: #BB7B47; 
            text-align: center; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-picture">
                <?php if (!empty($managerData['picture'])): ?>
                    <img src="<?php echo $managerData['picture']; ?>" alt="Profile Picture" style="width:150px;height:150px;">
                <?php else: ?>
                    <img src="icon.png" alt="Default Profile Picture" style="width:150px;height:150px;">
                <?php endif; ?>
            </div>

        <div class="staff-info">
            <h1>Welcome, <?php echo $managerData['name']; ?></h1>
            <p>Staff ID: <?php echo $managerData['manager_id']; ?></p>
            <p>Email: <?php echo $managerData['email']; ?></p>
            <p>Duty: <?php if (!empty($managerData['duty'])): ?>
                <ul>
                    <?php foreach ($managerData['duty'] as $duty): ?>
                        <li><?php echo $duty; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                No duties assigned.
            <?php endif; ?></p>
            <p>Dorm: <?php echo $managerData['dorm_name']; ?></p>
        </div>
        
        <div class="action-buttons">
            <a href="editProfileManager.php"><button>Edit Profile</button></a><br><br>
            <a href="viewStaffManager.php"><button>View Staffs</button><br><br>
            <a href="viewStudentsManager.php"><button>View Students in My Dorm</button><br><br>
            <a href="viewAllManager.php"><button>View All Students</button><br><br>
            <a href="user.html"><button>Logout</button></a>
        </div>
    </div>
</body>
</html>