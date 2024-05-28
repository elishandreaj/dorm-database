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
    $duty = $_POST['duty'];
    $dorm_id = $_POST['dorm_id'];
    $staff_id = $_SESSION['username'];

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
            $dest_path = 'uploads/' . $staff_id . '.' . $fileExtension;
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $picture = $dest_path;
            } else {
                $message = 'There was an error moving the file to the upload directory.';
            }
        } else {
            $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
        }
    }

    $stmt = $conn->prepare("UPDATE staff SET name = ?, email = ?, duty = ?, dorm_id = ?, picture = ? WHERE staff_id = ?");
    $stmt->bind_param("ssssss", $name, $email, $duty, $dorm_id, $picture, $staff_id);
    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully');</script>";
        echo "<script>window.setTimeout(function(){ window.location = 'staffDashboard.php'; }, 0);</script>";
    } else {
        echo "<script>alert('Error updating profile');</script>";
    }

    $stmt->close();
    $conn->close();
}

$stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo "<script>alert('Staff not found');</script>";
    exit();
}

$staffData = $result->fetch_assoc();

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
    <title>Edit Profile</title>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <?php if ($message != ''): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="editProfileStaff.php" method="post" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo $staffData['name']; ?>" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo $staffData['email']; ?>" required>
            <br>
            <label for="duty">Duty:</label>
            <input type="text" name="duty" value="<?php echo $staffData['duty']; ?>" required>
            <br>
            <label for="dorm_id">Dorm:</label>
            <select name="dorm_id" required>
                <?php while ($dorm = $dorms->fetch_assoc()): ?>
                    <option value="<?php echo $dorm['dorm_id']; ?>" <?php if ($dorm['dorm_id'] == $staffData['dorm_id']) echo 'selected'; ?>>
                        <?php echo $dorm['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="profile_picture">Profile Picture (jpg, png):</label>
            <input type="file" name="profile_picture">
            <br>
            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
