<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$order_id = $_GET['id'];

$query = "SELECT o.id, u.email, o.total_amount, o.payment_status, s.address, s.city, s.state, s.postal_code, o.created_at 
          FROM orders o
          JOIN users u ON o.user_id = u.id
          JOIN shipping s ON o.id = s.order_id 
          WHERE o.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// Order Items Fetch
$item_query = "SELECT p.name, oi.quantity, oi.price 
               FROM order_items oi
               JOIN products p ON oi.product_id = p.id 
               WHERE oi.order_id = ?";
$stmt = $conn->prepare($item_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>

<div class="container">
    <h2>Order Details</h2>
    <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
    <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
    <p><strong>Payment Status:</strong> <?php echo $order['payment_status']; ?></p>
    <p><strong>Address:</strong> <?php echo $order['address']; ?>, <?php echo $order['city']; ?>, <?php echo $order['state']; ?> - <?php echo $order['postal_code']; ?></p>
    <p><strong>Order Date:</strong> <?php echo $order['created_at']; ?></p>

    <h3>Order Items</h3>
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
