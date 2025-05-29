<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "shreenath_pooja_store");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get category from GET request
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : null;

if ($category) {
    $sql = "SELECT * FROM products WHERE category = '$category'";
} else {
    $sql = "SELECT * FROM products"; // 👈 If category not set, show all products
}

$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($products);

$conn->close();
?>
