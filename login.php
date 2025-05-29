<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header("Content-Type: application/json");

// Database Connection
$conn = new mysqli("localhost", "root", "", "shreenath_pooja_store");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

// Read JSON Input
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit();
}

$email = $conn->real_escape_string($data['email']);
$password = $data['password'];

// Check if User Exists
$query = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($query);

if ($result->num_rows <= 0) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit();
}

$row = $result->fetch_assoc();

// **Fix: First Name & Last Name Use Kela**
$full_name = trim($row['first_name'] . " " . $row['last_name']);

// Verify Password
if (!password_verify($password, $row['password'])) {
    echo json_encode(["success" => false, "message" => "Incorrect password"]);
    exit();
}

// Start Session
$_SESSION['user_id'] = $row['id'];
$_SESSION['user_name'] = $full_name;

echo json_encode(["success" => true, "message" => "Login successful", "redirect" => "products.html"]);

$conn->close();
exit();
?>
