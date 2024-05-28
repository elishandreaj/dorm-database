<?php
session_start();

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'database.php';

// Fetch dorms
$stmt = $conn->prepare("SELECT * FROM dorm");
$stmt->execute();
$dorms = $stmt->get_result();
$dormList = [];
while ($dorm = $dorms->fetch_assoc()) {
    $dormList[$dorm['dorm_id']] = $dorm;
}
$stmt->close();

// Fetch students grouped by dorm_id
$studentsByDorm = [];
foreach ($dormList as $dorm_id => $dorm) {
    $stmt = $conn->prepare("SELECT * FROM student WHERE dorm_id = ?");
    $stmt->bind_param("i", $dorm_id);
    $stmt->execute();
    $studentsByDorm[$dorm_id] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Students</title>
    <style>
        body{
            background-color: #BB7B47;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            width: 100px;
            height: 100px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>All Students in All Dorms</h1>

        <?php foreach ($studentsByDorm as $dorm_id => $students): ?>
            <h2><?php echo $dormList[$dorm_id]['name']; ?></h2>
            <table>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Year Level</th>
                    <th>Room Number</th>
                    <th>Fees</th>
                    <th>Picture</th>
                </tr>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $student['student_id']; ?></td>
                        <td><?php echo $student['name']; ?></td>
                        <td><?php echo $student['email']; ?></td>
                        <td><?php echo $student['course']; ?></td>
                        <td><?php echo $student['year_level']; ?></td>
                        <td><?php echo $student['room_number']; ?></td>
                        <td><?php echo $student['fees']; ?></td>
                        <td>
                            <?php if (!empty($student['picture'])): ?>
                                <img src="<?php echo $student['picture']; ?>" alt="Student Picture">
                            <?php else: ?>
                                <div style="width:100px;height:100px;background-color:grey;"></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endforeach; ?>

        <br><a href="managerDashboard.php"><button>Back</button></a>
    </div>
</body>
</html>
