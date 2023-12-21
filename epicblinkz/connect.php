<?php
$host = "localhost";
$user = "postgres";
$pass = "postgres";
$db = "db_epicblinkz";

try {
    // Connect to PostgreSQL using PDO
    $con = new PDO("pgsql:dbname=$db;host=$host", $user, $pass);

    // Set the PDO error mode to exception
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit(); // Exit the script if the connection fails
}
?>