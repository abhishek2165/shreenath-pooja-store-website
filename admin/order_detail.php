<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Database Connection
$conn = new mysqli("localhost", "root", "", "shreenath_pooja_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$order_id = $_GET['id'] ?? 0;

$sql = "SELECT o.id, o.user_email, o.total_amount, o.payment_method, o.order_status, o.created_at 
        FROM orders o WHERE o.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

$sql_items = "SELECT oi.product_id, p.name, oi.quantity, oi.price 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
$stmt = $conn->prepare($sql_items);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Detail</title>
    <link rel="stylesheet" href="dashboardstyle.css">
</head>
<body>

<div class="main-content">
    <h2>Order Details - ID: <?php echo $order['id']; ?></h2>
    <p>Email: <?php echo $order['user_email']; ?></p>
    <p>Total Amount: ₹<?php echo number_format($order['total_amount'], 2); ?></p>
    <p>Payment Method: <?php echo $order['payment_method']; ?></p>
    <p>Status: <?php echo $order['order_status']; ?></p>
    <p>Order Date: <?php echo $order['created_at']; ?></p>

    <h3>Items:</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $items->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>₹<?php echo number_format($item['price'], 2); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
