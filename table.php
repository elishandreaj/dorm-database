<?php
include 'database.php';

$tables = [
    "CREATE TABLE IF NOT EXISTS dorm (
        dorm_id INT(1) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        contact INT(11) UNSIGNED NOT NULL,
        capacity INT(3) UNSIGNED NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS manager (
        manager_id INT(9) UNSIGNED PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        duty VARCHAR(100) NOT NULL,
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
    "CREATE TABLE IF NOT EXISTS login (
        username INT(9) UNSIGNED PRIMARY KEY,
        password VARCHAR(100) NOT NULL,
        role VARCHAR(20) NOT NULL,
        employee_type VARCHAR (20),
        id INT(9) NOT NULL
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
    ['dorm_id' => 1, 'name' => 'Balay Apitong', 'email' => 'apitong@dorm.com', 'contact' => '09123456764','capacity' => '64'],
    ['dorm_id' => 2, 'name' => 'Balay Gumamela', 'email' => 'gumamela@dorm.com', 'contact' => '09368742447', 'capacity' => '116'],
    ['dorm_id' => 3, 'name' => 'Balay Kanlaon', 'email' => 'kanlaon@dorm.com', 'contact' => '09123435567', 'capacity' => '16'],
    ['dorm_id' => 4, 'name' => 'Balay Lampirong', 'email' => 'lampirong@dorm.com', 'contact' => '09951234567', 'capacity' => '116'],
    ['dorm_id' => 5, 'name' => 'Balay Madyaas', 'email' => 'madyaas@dorm.com', 'contact' => '09367534567', 'capacity' => '150'],
    ['dorm_id' => 6, 'name' => 'Balay Miagos', 'email' => 'miagos@dorm.com', 'contact' => '09632475492', 'capacity' => '95'],
    ['dorm_id' => 7, 'name' => 'International Dorm', 'email' => 'international@dorm.com', 'contact' => '09652938294', 'capacity' => '95']
];

foreach ($dorms as $dorm) {
    $stmt = $conn->prepare("INSERT IGNORE INTO dorm (dorm_id, name, email, contact, capacity) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issii", $dorm['dorm_id'], $dorm['name'], $dorm['email'], $dorm['contact'], $dorm['capacity']);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>