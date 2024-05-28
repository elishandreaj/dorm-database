<?php
include 'database.php';

$tables = [
    "CREATE TABLE IF NOT EXISTS dorm (
        dorm_id INT(1) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE
    )",
    "CREATE TABLE IF NOT EXISTS manager (
        manager_id INT(9) UNSIGNED PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        duty VARCHAR(40) NOT NULL,
        dorm_id INT(1) UNSIGNED,
        picture VARCHAR(60) NOT NULL,
        FOREIGN KEY (dorm_id) REFERENCES dorm(dorm_id)
    )",
    "CREATE TABLE IF NOT EXISTS staff (
        staff_id INT(9) UNSIGNED PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        duty VARCHAR(40) NOT NULL,
        dorm_id INT(1) UNSIGNED,
        picture VARCHAR(60) NOT NULL,
        FOREIGN KEY (dorm_id) REFERENCES dorm(dorm_id)
    )",
    "CREATE TABLE IF NOT EXISTS employeesInDorms (
        dorm_id INT(1) UNSIGNED,
        manager_id INT(9) UNSIGNED,
        staff_id INT(9) UNSIGNED,
        PRIMARY KEY (dorm_id, manager_id, staff_id),
        FOREIGN KEY (dorm_id) REFERENCES dorm(dorm_id),
        FOREIGN KEY (manager_id) REFERENCES manager(manager_id),
        FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
    )",
    "CREATE TABLE IF NOT EXISTS student (
        student_id INT(9) UNSIGNED PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        course VARCHAR(40) NOT NULL,
        year_level INT(1) NOT NULL,
        room_number INT(2) UNSIGNED,
        fees INT(4) UNSIGNED,
        dorm_id INT(1) UNSIGNED,
        picture VARCHAR(60) NOT NULL,
        FOREIGN KEY (dorm_id) REFERENCES dorm(dorm_id),
        INDEX room_number_index (room_number)
    )",
    "CREATE TABLE IF NOT EXISTS roomOfStudent (
        room_number INT(2) UNSIGNED,
        student_id INT(9) UNSIGNED,
        PRIMARY KEY (room_number, student_id),
        FOREIGN KEY (room_number) REFERENCES student(room_number),
        FOREIGN KEY (student_id) REFERENCES student(student_id)
    )",
    "CREATE TABLE IF NOT EXISTS roomsInDorms (
        room_number INT(2) UNSIGNED,
        dorm_id INT(1) UNSIGNED,
        PRIMARY KEY (room_number, dorm_id),
        FOREIGN KEY (room_number) REFERENCES roomOfStudent(room_number),
        FOREIGN KEY (dorm_id) REFERENCES dorm(dorm_id)
    )",
    "CREATE TABLE IF NOT EXISTS login (
        username VARCHAR(40) PRIMARY KEY,
        password VARCHAR(60) NOT NULL,
        role VARCHAR(20) NOT NULL,
        employee_type VARCHAR (20) NOT NULL,
        id VARCHAR(40) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS student_edits (
        edit_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        student_id INT(9) UNSIGNED,
        name VARCHAR(100),
        email VARCHAR(100),
        course VARCHAR(40),
        year_level INT(1),
        room_number INT(2) UNSIGNED,
        fees INT(3) UNSIGNED,
        picture VARCHAR(60),
        approved BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (student_id) REFERENCES student(student_id)
    )",
    "CREATE TABLE IF NOT EXISTS staff_edits (
        edit_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        staff_id INT(9) UNSIGNED,
        name VARCHAR(100),
        email VARCHAR(100),
        duty VARCHAR(40),
        picture VARCHAR(60),
        approved BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
    )"
];

foreach ($tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table created successfully.<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

$dorms = [
    ['dorm_id' => 1, 'name' => 'Balay Apitong', 'email' => 'apitong@dorm.com'],
    ['dorm_id' => 2, 'name' => 'Balay Gumamela', 'email' => 'gumamela@dorm.com'],
    ['dorm_id' => 3, 'name' => 'Balay Kanlaon', 'email' => 'kanlaon@dorm.com'],
    ['dorm_id' => 4, 'name' => 'Balay Lampirong', 'email' => 'lampirong@dorm.com'],
    ['dorm_id' => 5, 'name' => 'Balay Madyaas', 'email' => 'madyaas@dorm.com'],
    ['dorm_id' => 6, 'name' => 'Balay Miagos', 'email' => 'miagos@dorm.com'],
    ['dorm_id' => 7, 'name' => 'International Dorm', 'email' => 'international@dorm.com']
];

foreach ($dorms as $dorm) {
    $stmt = $conn->prepare("INSERT IGNORE INTO dorm (dorm_id, name, email) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $dorm['dorm_id'], $dorm['name'], $dorm['email']);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>