<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "shreenath_pooja_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $weight = $_POST['weight'];
    $brand = $_POST['brand'];

    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $hover_file = $target_dir . basename($_FILES["hoverImage"]["name"]);

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file) &&
        move_uploaded_file($_FILES["hoverImage"]["tmp_name"], $hover_file)) {

        $stmt = $conn->prepare("INSERT INTO products (name, price, weight, brand, image, hoverImage) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssss", $name, $price, $weight, $brand, $target_file, $hover_file);
        $stmt->execute();
        $stmt->close();

        header("Location: products.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="ADEstyles.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h1>Add New Product</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Product Name" required>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <input type="text" name="weight" placeholder="Weight" required>
            <input type="text" name="brand" placeholder="Brand" required>
            <input type="file" name="image" required>
            <input type="file" name="hoverImage" required>
            <button type="submit">Add Product</button>
        </form>
    </div>
</body>
</html>
