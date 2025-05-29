<?php
session_start();
header('Content-Type: application/json');

// ✅ Database Connection
$conn = new mysqli("localhost", "root", "", "shreenath_pooja_store");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// ✅ Get Data from Frontend
$email = $_POST['email'];
$total_amount = $_POST['total_amount'];
$payment_method = $_POST['payment_method'];
$address = $_POST['address'];
$city = $_POST['city'];
$state = $_POST['state'];
$postal_code = $_POST['postal_code'];
$phone = $_POST['phone'];
$cart_items = json_decode($_POST['cart_items'], true);

if (!$email || !$total_amount || !$address || !$city || !$postal_code || !$phone || empty($cart_items)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// ✅ Insert Order
$stmt = $conn->prepare("INSERT INTO orders (user_email, total_amount, payment_method) VALUES (?, ?, ?)");
$stmt->bind_param("sds", $email, $total_amount, $payment_method);
$stmt->execute();
$order_id = $stmt->insert_id;

// ✅ Insert Order Items
foreach ($cart_items as $item) {
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
    $stmt->execute();
}

// ✅ Insert Payment
$stmt = $conn->prepare("INSERT INTO payments (order_id, payment_method) VALUES (?, ?)");
$stmt->bind_param("is", $order_id, $payment_method);
$stmt->execute();

// ✅ Insert Shipping
$stmt = $conn->prepare("INSERT INTO shipping (order_id, address, city, state, postal_code, phone) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $order_id, $address, $city, $state, $postal_code, $phone);
$stmt->execute();

echo json_encode(['success' => true, 'message' => 'Order placed successfully']);

$conn->close();
?>
