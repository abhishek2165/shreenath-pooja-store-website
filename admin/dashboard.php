<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "shreenath_pooja_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Total Products
$totalProductsQuery = "SELECT COUNT(*) as totalProducts FROM products";
$totalProductsResult = $conn->query($totalProductsQuery);
$totalProducts = $totalProductsResult->fetch_assoc()['totalProducts'];

// Total Orders (orders table assume karun)
$totalOrdersQuery = "SELECT COUNT(*) as totalOrders FROM orders";
$totalOrdersResult = $conn->query($totalOrdersQuery);
$totalOrders = $totalOrdersResult->fetch_assoc()['totalOrders'];

// Total Revenue (orders table madhla total_amount column vapr)
$totalRevenueQuery = "SELECT SUM(total_amount) as totalRevenue FROM orders";
$totalRevenueResult = $conn->query($totalRevenueQuery);
$totalRevenue = $totalRevenueResult->fetch_assoc()['totalRevenue'];


// Total Users (users table assume karun)
$totalUsersQuery = "SELECT COUNT(*) as totalUsers FROM users";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = $totalUsersResult->fetch_assoc()['totalUsers'];

// Order Query Fix
$sql = "SELECT o.id, o.user_email, o.total_amount, o.payment_method, o.order_status, o.created_at 
        FROM orders o 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql); // ✅ Proper assignment

if (!$result) {
    die("Query Failed: " . $conn->error); // ✅ Debug message
}


$conn->close();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboradstyle.css" />
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="orders.php">Orders</a></li>
        <li><a href="users.php">Users</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Welcome, abhi 👋</h1>

    <div class="dashboard-cards">
        <div class="card">
            <h3>Total Products</h3>
            <h1><?php echo $totalProducts; ?></h1>
        </div>
        <div class="card">
            <h3>Total Orders</h3>
            <h1><?php echo $totalOrders; ?></h1>
        </div>
        <div class="card">
            <h3>Total Revenue</h3>
            <h1>₹<?php echo number_format($totalRevenue, 2); ?></h1>
        </div>
        <div class="card">
            <h3>Total Users</h3>
            <h1><?php echo $totalUsers; ?></h1>
        </div>
    </div>
</div>
<h2>Recent Orders</h2>
<table border="1" cellpadding="10" cellspacing="0">
<thead>
    <tr>
        <th>ID</th>
        <th>User Email</th> <!-- ✅ Add this line -->
        <th>Total Amount</th>
        <th>Payment Method</th>
        <th>Status</th>
        <th>Order Date</th>
        <th>Action</th>
    </tr>
</thead>
<tbody>
    <?php while ($order = $result->fetch_assoc()) { ?>
        <tr>
            <td>
                <a href="order.php?id=<?php echo $order['id']; ?>" style="text-decoration: none; color: blue; font-weight: bold;">
                    #<?php echo $order['id']; ?>
                </a>
            </td>
            <td><?php echo $order['user_email']; ?></td> <!-- ✅ Fixed -->
            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
            <td><?php echo $order['payment_method']; ?></td>
            <td>
                <?php if ($order['order_status'] == 'Pending') { ?>
                    <span style="color: red;"><?php echo $order['order_status']; ?></span>
                <?php } else { ?>
                    <span style="color: green;"><?php echo $order['order_status']; ?></span>
                <?php } ?>
            </td>
            <td><?php echo $order['created_at']; ?></td>
            <td>
                <a href="update_order_status.php?id=<?php echo $order['id']; ?>&status=Completed">✅ Complete</a>
            </td>
        </tr>
    <?php } ?>
</tbody>
</table>


</body>
</html>
