<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

include '../db.php';

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, mobile=?, email=? WHERE id=?");
    $stmt->bind_param("ssssi", $first_name, $last_name, $mobile, $email, $id);
    $stmt->execute();

    header("Location: users.php");
    exit();
}

?>

<form method="POST">
    <input type="text" name="first_name" value="<?= $user['first_name'] ?>" required>
    <input type="text" name="last_name" value="<?= $user['last_name'] ?>" required>
    <input type="text" name="mobile" value="<?= $user['mobile'] ?>" required>
    <input type="email" name="email" value="<?= $user['email'] ?>" required>
    <button type="submit">Update</button>
</form>
