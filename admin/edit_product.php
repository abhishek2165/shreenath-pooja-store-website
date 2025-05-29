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

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM products WHERE id = $id");
$product = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $weight = $_POST['weight'];
    $brand = $_POST['brand'];

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, weight=?, brand=? WHERE id=?");
    $stmt->bind_param("sdssi", $name, $price, $weight, $brand, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: products.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <form method="POST">
        <input type="text" name="name" value="<?= $product['name'] ?>" required>
        <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>
        <input type="text" name="weight" value="<?= $product['weight'] ?>" required>
        <input type="text" name="brand" value="<?= $product['brand'] ?>" required>
        <button type="submit">Update Product</button>
    </form>
</body>
</html>
