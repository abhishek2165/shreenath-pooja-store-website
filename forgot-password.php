<?php
// forgot-password.php
$servername = "localhost";
$db_username = "root";
$db_password = "";
$database = "shreenath_pooja_store";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $database);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    if (empty($email)) {
        echo "Please enter your email address.";
        exit();
    }

    // Verify if the email exists in users table
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo "No user found with that email address.";
        exit();
    }

    // Generate a secure token and set expiry (1 hour later)
    $token = bin2hex(random_bytes(50));
    $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Insert token into password_reset table
    $stmt = $conn->prepare("INSERT INTO password_reset (email, token, expiry) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $token, $expiry);
    $stmt->execute();

    // Compose the reset link (adjust your domain/path as needed)
    $resetLink = "http://yourdomain.com/reset_password.php?token=" . $token;
    $subject = "Password Reset Request";
    $message = "Hi,\n\nClick the following link to reset your password:\n" . $resetLink . "\n\nThis link will expire in 1 hour.";
    $headers = "From: no-reply@yourdomain.com";

    if (mail($email, $subject, $message, $headers)) {
        echo "A password reset link has been sent to your email address.";
    } else {
        echo "Failed to send email. Please try again.";
    }
}
$conn->close();
?>
