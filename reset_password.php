<?php
// reset_password.php
$servername = "localhost";
$db_username = "root";
$db_password = "";
$database = "shreenath_pooja_store";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $database);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['token'])) {
        echo "Invalid request!";
        exit();
    }
    $token = $_GET['token'];
    $stmt = $conn->prepare("SELECT * FROM password_reset WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo "Invalid or expired token!";
        exit();
    }
    $row = $result->fetch_assoc();
    if (strtotime($row['expiry']) < time()) {
        echo "Token has expired!";
        exit();
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Reset Password</title>
      <link rel="stylesheet" href="style.css">
    </head>
    <body>
      <div class="container">
        <h1>Reset Your Password</h1>
        <form id="resetPasswordForm" action="reset_password.php" method="post">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
          <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required>
          </div>
          <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
          </div>
          <button type="submit">Reset Password</button>
        </form>
      </div>
      <script>
        // Simple JS validation: check if the passwords match
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
          let password = document.getElementById('password').value;
          let confirmPassword = document.getElementById('confirm_password').value;
          if(password !== confirmPassword) {
            alert("Passwords do not match!");
            e.preventDefault();
          }
        });
      </script>
    </body>
    </html>
    <?php
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
        exit();
    }

    // Validate token again
    $stmt = $conn->prepare("SELECT * FROM password_reset WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo "Invalid token!";
        exit();
    }
    $row = $result->fetch_assoc();
    if (strtotime($row['expiry']) < time()) {
        echo "Token has expired!";
        exit();
    }

    // Update the user's password (hash it before saving)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $email = $row['email'];
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);
    if ($stmt->execute()) {
        // Delete the token after successful reset
        $stmt = $conn->prepare("DELETE FROM password_reset WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        echo "Password has been reset successfully! <a href='login.html'>Login here</a>.";
    } else {
        echo "Failed to reset password!";
    }
}
$conn->close();
?>
