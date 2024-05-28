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

$stmt = $conn->prepare("
    SELECT se.*, s.dorm_id, s.name AS student_name 
    FROM student_edits se 
    JOIN student s ON se.student_id = s.student_id 
    WHERE s.dorm_id = ?
");
$stmt->bind_param("i", $dorm_id);
$stmt->execute();
$studentEdits = $stmt->get_result();
$stmt->close();
$conn->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'database.php';

    if (isset($_POST['approve'])) {
        $edit_id = $_POST['edit_id'];
        $stmt = $conn->prepare("
            UPDATE student s
            JOIN student_edits se ON s.student_id = se.student_id
            SET 
                s.name = se.name,
                s.email = se.email,
                s.course = se.course,
                s.year_level = se.year_level,
                s.room_number = se.room_number,
                s.fees = se.fees,
                s.picture = se.picture
            WHERE se.edit_id = ?
        ");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM student_edits WHERE edit_id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $stmt->close();

        header("Location: manageStudentsStaff.php");
        exit();
    }

    if (isset($_POST['delete'])) {
        $edit_id = $_POST['edit_id'];
        $stmt = $conn->prepare("DELETE FROM student_edits WHERE edit_id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $stmt->close();

        header("Location: manageStudentsStaff.php");
        exit();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Student Edits</title>
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
        <h1>Manage Student Edits</h1>
        <table>
            <tr>
                <th>Edit ID</th>
                <th>Student Name</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Year Level</th>
                <th>Room Number</th>
                <th>Fees</th>
                <th>Picture</th>
                <th>Action</th>
            </tr>
            <?php while ($edit = $studentEdits->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $edit['edit_id']; ?></td>
                    <td><?php echo $edit['student_name']; ?></td>
                    <td><?php echo $edit['name']; ?></td>
                    <td><?php echo $edit['email']; ?></td>
                    <td><?php echo $edit['course']; ?></td>
                    <td><?php echo $edit['year_level']; ?></td>
                    <td><?php echo $edit['room_number']; ?></td>
                    <td><?php echo $edit['fees']; ?></td>
                    <td>
                        <?php if (!empty($edit['picture'])): ?>
                            <img src="<?php echo $edit['picture']; ?>" alt="Student Picture">
                        <?php else: ?>
                            <div style="width:100px;height:100px;background-color:grey;"></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form action="manageStudentsStaff.php" method="post">
                            <input type="hidden" name="edit_id" value="<?php echo $edit['edit_id']; ?>">
                            <button type="submit" name="approve">Approve</button>
                            <button type="submit" name="delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>