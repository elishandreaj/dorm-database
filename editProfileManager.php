<?php
session_start();

if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'database.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $duty = json_encode(isset($_POST['duty']) ? $_POST['duty'] : []);
    $dorm_id = $_POST['dorm_id'];
    $manager_id = $_SESSION['username'];

    $picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'jpeg', 'png');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $dest_path = 'uploads/' . $manager_id . '.' . $fileExtension;
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $picture = $dest_path;
            } else {
                $message = 'There was an error moving the file to the upload directory.';
            }
        } else {
            $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
        }
    }

    if ($picture) {
        $stmt = $conn->prepare("UPDATE manager SET name = ?, email = ?, duty = ?, dorm_id = ?, picture = ? WHERE manager_id = ?");
        $stmt->bind_param("ssssss", $name, $email, $duty, $dorm_id, $picture, $manager_id);
    } else {
        $stmt = $conn->prepare("UPDATE manager SET name = ?, email = ?, duty = ?, dorm_id = ? WHERE manager_id = ?");
        $stmt->bind_param("sssss", $name, $email, $duty, $dorm_id, $manager_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully');</script>";
        echo "<script>window.setTimeout(function(){ window.location = 'managerDashboard.php'; }, 0);</script>";
    } else {
        echo "<script>alert('Error updating profile');</script>";
    }
}

$stmt = $conn->prepare("SELECT * FROM manager WHERE manager_id = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo "<script>alert('Dorm Manager not found');</script>";
    exit();
}

$managerData = $result->fetch_assoc();
$managerData['duty'] = json_decode($managerData['duty'], true);

$stmt = $conn->prepare("SELECT * FROM dorm");
$stmt->execute();
$dorms = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Edit Profile</title>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <?php if ($message != ''): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="editProfileManager.php" method="post" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($managerData['name']); ?>" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($managerData['email']); ?>" required>
            <br>
            <label for="duty">Duty:</label>
            <div class="dropdown-checklist">
                <button type="button" onclick="toggleChecklist()">Select Duties</button>
                <div class="dropdown-checklist-content" id="duty-checklist">
                    <?php
                    $allDuties = ["Monday 1st Shift", "Monday 2nd Shift", "Monday 3rd Shift", "Tuesday 1st Shift", "Tuesday 2nd Shift", 
                                    "Tuesday 3rd Shift", "Wednesday 1st Shift", "Wednesday 2nd Shift", "Wednesday 3rd Shift", 
                                    "Thursday 1st Shift", "Thursday 2nd Shift", "Thursday 3rd Shift", "Friday 1st Shift", 
                                    "Friday 2nd Shift", "Friday 3rd Shift", "Saturday 1st Shift", "Saturday 2nd Shift", 
                                    "Saturday 3rd Shift", "Sunday 1st Shift", "Sunday 2nd Shift", "Sunday 3rd Shift",]; 
                    foreach ($allDuties as $duty) {
                        $checked = in_array($duty, $managerData['duty']) ? 'checked' : '';
                        echo "<label><input type='checkbox' name='duty[]' value='$duty' $checked>$duty</label>";
                    }
                    ?>
                </div>
            </div>
            <br>
            <label for="dorm_id">Dorm:</label>
            <select name="dorm_id" required>
                <?php while ($dorm = $dorms->fetch_assoc()): ?>
                    <option value="<?php echo $dorm['dorm_id']; ?>" <?php if ($dorm['dorm_id'] == $managerData['dorm_id']) echo "selected"; ?>><?php echo $dorm['name']; ?></option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="profile_picture">Profile Picture (jpg, png):</label>
            <input type="file" name="profile_picture" value="<?php echo $managerData['picture']; ?>">
            <br>
            <button type="submit">Update Profile</button>
        </form>
        <br><a href="managerDashboard.php"><button>Cancel</button></a>
    </div>

    <script>
        function toggleChecklist() {
            var checklist = document.getElementById('duty-checklist');
            if (checklist.style.display === 'none' || checklist.style.display === '') {
                checklist.style.display = 'block';
            } else {
                checklist.style.display = 'none';
            }
        }
    </script>

</body>
</html>