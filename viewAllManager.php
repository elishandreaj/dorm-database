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
$manager_dorm_id = $managerData['dorm_id'];

$stmt->close();

$searchQuery = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $search = "%" . $searchQuery . "%";

    if (is_numeric($search)) {
        $stmt = $conn->prepare("SELECT * FROM student WHERE dorm_id = ? AND (student_id = ? OR room_number = ? OR year_level = ?)");
        $stmt->bind_param("iiss", $dorm_id, $search, $search, $search);
    } else {
        $searchLike = "%" . $search . "%"; 
        $stmt = $conn->prepare("SELECT * FROM student WHERE dorm_id = ? AND (name LIKE ? OR course LIKE ?)");
        $stmt->bind_param("iss", $dorm_id, $searchLike, $searchLike);
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM student");
}

$stmt->execute();
$students = $stmt->get_result();
$stmt->close();

$studentsByDorm = [];
while ($student = $students->fetch_assoc()) {
    $studentsByDorm[$student['dorm_id']][] = $student;
}

$stmt = $conn->prepare("SELECT * FROM dorm");
$stmt->execute();
$dorms = $stmt->get_result();
$dormList = [];
while ($dorm = $dorms->fetch_assoc()) {
    $dormList[$dorm['dorm_id']] = $dorm;
}
$stmt->close();
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
        .no-student-found {
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 align="center">All Students in All Dorms</h1>

        <form method="get" align="center">
            <input type="text" name="search" placeholder="Search by name or student ID..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>

        <?php foreach ($studentsByDorm as $dorm_id => $students): ?>
            <h2><?php echo htmlspecialchars($dormList[$dorm_id]['name']); ?></h2>
            <?php if (empty($students)): ?>
                <p class="no-student-found">No students found</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Picture</th>
                        <?php if ($dorm_id == $manager_dorm_id): ?>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Year Level</th>
                            <th>Room Number</th>
                            <th>Fees</th>
                        <?php endif; ?>
                    </tr>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td>
                                <?php if (!empty($student['picture'])): ?>
                                    <img src="<?php echo htmlspecialchars($student['picture']); ?>" alt="Student Picture">
                                <?php else: ?>
                                    <div style="width:100px;height:100px;background-color:grey;"></div>
                                <?php endif; ?>
                            </td>
                            <?php if ($dorm_id == $manager_dorm_id): ?>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['course']); ?></td>
                                <td><?php echo htmlspecialchars($student['year_level']); ?></td>
                                <td><?php echo htmlspecialchars($student['room_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['fees']); ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        <?php endforeach; ?>

        <br><a href="managerDashboard.php"><button>Back</button></a>
    </div>
</body>
</html>
