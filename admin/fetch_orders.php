<?php
$conn = new mysqli("localhost", "root", "", "shreenath_pooja_store");

$result = $conn->query("SELECT o.id, o.total_amount, o.payment_method, o.order_status, s.tracking_status 
                        FROM orders o 
                        JOIN shipping s ON o.id = s.order_id");

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);
?>
