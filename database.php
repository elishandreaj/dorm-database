<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dormDatabase"; 

$conn = new mysqli($servername, $username, $password);

$sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname";

$conn->select_db($dbname);

?>