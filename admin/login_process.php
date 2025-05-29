<?php
session_start();

$conn = new mysqli("localhost", "root", "", "shreenath_pooja_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM `admin` WHERE `username` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        // Set session for admin
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $row['username'];
        
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid password!";
    }
} else {
    echo "Invalid username!";
}

$conn->close();
?>
