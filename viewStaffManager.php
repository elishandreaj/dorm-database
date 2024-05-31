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

$searchQuery = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = "%{$_GET['search']}%";
    $searchQuery = $_GET['search'];

    if (is_numeric($search)) {
        $stmt = $conn->prepare("SELECT * FROM staff WHERE dorm_id = ? AND (staff_id = ?)");
        $stmt->bind_param("ii", $dorm_id, $search);
    } else {
        $searchLike = "%" . $search . "%"; 
        $stmt = $conn->prepare("SELECT * FROM staff WHERE dorm_id = ? AND (name LIKE ? OR email LIKE ? OR duty LIKE ?)");
        $stmt->bind_param("isss", $dorm_id, $searchLike, $searchLike, $searchLike);
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM staff WHERE dorm_id = ?");
    $stmt->bind_param("i", $dorm_id);
}

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
        body{
            background-color: #BB7B47;
        }
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
        <h1 align="center">Staffs in Your Dorm</h1>

        <form method="get" align="center">
            <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <tr>
                <th>Staff ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Duty</th>
                <th>Picture</th>
                <th>Action</th>
            </tr>
            <?php if ($staffs->num_rows > 0): ?>
                <?php while ($staff = $staffs->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($staff['staff_id']); ?></td>
                        <td><?php echo htmlspecialchars($staff['name']); ?></td>
                        <td><?php echo htmlspecialchars($staff['email']); ?></td>
                        <td><?php echo htmlspecialchars($staff['duty']); ?></td>
                        <td>
                            <?php if (!empty($staff['picture'])): ?>
                                <img src="<?php echo htmlspecialchars($staff['picture']); ?>" alt="Staff Picture">
                            <?php else: ?>
                                <div style="width:100px;height:100px;background-color:grey;"></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="deleteStaffManager.php" method="post">
                                <input type="hidden" name="staff_id" value="<?php echo htmlspecialchars($staff['staff_id']); ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No staff found</td>
                </tr>
            <?php endif; ?>
        </table>
        <br><a href="managerDashboard.php"><button>Back</button></a>
    </div>
</body>
</html>