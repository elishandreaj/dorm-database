<?php
session_start();

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'database.php';

$stmt = $conn->prepare("SELECT dorm_id FROM manager WHERE manager_id = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Dorm Manager not found');</script>";
    exit();
}

$managerData = $result->fetch_assoc();
$dorm_id = $managerData['dorm_id'];

$stmt->close();

$stmt = $conn->prepare("SELECT * FROM staff WHERE dorm_id = ?");
$stmt->bind_param("i", $dorm_id);
$stmt->execute();
$staffs = $stmt->get_result();
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
        <h1>Staffs in Your Dorm</h1>
        <table>
            <tr>
                <th>Staff ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Duty</th>
                <th>Picture</th>
                <th>Action</th>
            </tr>
            <?php while ($staff = $staffs->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $staff['staff_id']; ?></td>
                    <td><?php echo $staff['name']; ?></td>
                    <td><?php echo $staff['email']; ?></td>
                    <td><?php echo $staff['duty']; ?></td>
                    <td>
                        <?php if (!empty($student['picture'])): ?>
                            <img src="<?php echo $student['picture']; ?>" alt="Student Picture">
                        <?php else: ?>
                            <div style="width:100px;height:100px;background-color:grey;"></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form action="deleteStaffManager.php" method="post">
                            <input type="hidden" name="student_id" value="<?php echo $staff['staff_id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <br><a href="managerDashboard.php"><button>Back</button></a>
    </div>
</body>
</html>