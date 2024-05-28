<?php
session_start();

if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'database.php';

$stmt = $conn->prepare("SELECT student.*, dorm.name as dorm_name FROM student LEFT JOIN dorm ON student.dorm_id = dorm.dorm_id WHERE student_id = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo "User not found.";
    exit();
}

$userData = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
</head>
<body>
    <div class="container">
        <div class="profile-picture">
            <?php if (!empty($userData['picture'])): ?>
                <img src="<?php echo $userData['picture']; ?>" alt="Profile Picture" style="width:150px;height:150px;">
            <?php else: ?>
                <div style="width:150px;height:150px;background-color:black;"></div>
            <?php endif; ?>
        </div>

        <div class="user-info">
            <h1>Welcome, <?php echo $userData['name']; ?></h1>
            <p>Student ID: <?php echo $userData['student_id']; ?></p>
            <p>Email: <?php echo $userData['email']; ?></p>
            <p>Course: <?php echo $userData['course']; ?></p>
            <p>Year Level: <?php echo $userData['year_level']; ?></p>
            <p>Room Number: <?php echo $userData['room_number']; ?></p>
            <p>Fees: <?php echo $userData['fees']; ?></p>
            <p>Dorm: <?php echo $userData['dorm_name']; ?></p>
        </div>
        
        <div class="action-buttons">
            <a href="editProfileStudent.php"><button>Edit Profile</button></a>
        </div>
    </div>
</body>
</html>