<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPV Dormitories</title>
    <style>
        body {
            background-color: #BB7B47;
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            padding: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #ddd;
        }
        h1 {
            text-align: center;
            color: #333333;
        }
        .container {
            width: 100%;
            margin: auto;
            overflow: hidden;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
            include "database.php";

            $input = isset($_POST['viewDorm']) ? $_POST['viewDorm'] : '';

            if (empty($input)) {
                $sql = "SELECT * FROM dormdatabase.dorm";
            } else {
                // Using prepared statements to prevent SQL injection
                $stmt = $conn->prepare("SELECT * FROM dormdatabase.dorm WHERE dorm_id = ? OR name = ? OR email = ? OR contact = ? OR capacity = ?");
                $stmt->bind_param("issii", $input, $input, $input, $input, $input); 
                $stmt->execute();
                $result = $stmt->get_result();
            }

            // Only execute this if the prepared statement was not used
            if (!isset($result)) {
                $result = $conn->query($sql);
            }
            
            echo "<h1>UPV DORMITORIES</h1>";
            echo "<table>" .
                "<tr>".
                    "<th>ID</th>".
                    "<th>Name</th>".
                    "<th>Email</th>".
                    "<th>Contact</th>".
                    "<th>Capacity</th>".
                "</tr>";

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>".
                        "<td>" . htmlspecialchars($row["dorm_id"]) . "</td>".
                        "<td>" . htmlspecialchars($row["name"]) . "</td>".
                        "<td>" . htmlspecialchars($row["email"]) . "</td>".
                        "<td>" . htmlspecialchars($row["contact"]) . "</td>".
                        "<td>" . htmlspecialchars($row["capacity"]) . "</td>".
                    "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>0 results</td></tr>";
            }
            echo "</table>";
            echo "<a href='user.html' class='back-link'>Back to Home</a>";

            $conn->close();
        ?>
    </div>
</body>
</html>
