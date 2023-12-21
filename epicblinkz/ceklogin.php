<?php

include 'connect.php';
session_start();

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    try {
        $pdo = new PDO("pgsql:host=localhost;dbname=db_epicblinkz", "postgres", "postgres");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (!$pdo) {
            die('Failed to connect to the database');
        }

        // Check user credentials
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['user_id'] = $row['id'];
            header('location: shop.php');
        } else {
            $message[] = 'Incorrect password or email!';
        }
    } catch (PDOException $e) {
        die('Query failed: ' . $e->getMessage());
    } finally {
        $pdo = null;
    }
}

?>
