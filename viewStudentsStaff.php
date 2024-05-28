<?php
session_start();

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'database.php';

$stmt = $conn->prepare("SELECT dorm_id FROM staff WHERE staff_id = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Staff not found');</script>";
    exit();
}

$staffData = $result->fetch_assoc();
$dorm_id = $staffData['dorm_id'];

$stmt->close();

$stmt = $conn->prepare("SELECT * FROM student WHERE dorm_id = ?");
$stmt->bind_param("i", $dorm_id);
$stmt->execute();
$students = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
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
        <h1>Students in Your Dorm</h1>
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
                <th>Action</th>
            </tr>
            <?php while ($student = $students->fetch_assoc()): ?>
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
                    <td>
                        <form action="deleteStudentStaff.php" method="post">
                            <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <a href="staffDashboard.php"><button>Back</button></a>
    </div>
</body>
</html>