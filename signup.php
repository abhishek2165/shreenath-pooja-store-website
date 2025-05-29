<?php
header("Content-Type: application/json");

// Database Connection Details
$servername = "localhost";
$username = "root";
$password = "";
$database = "shreenath_pooja_store";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed!"]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = isset($_POST["firstName"]) ? $_POST["firstName"] : "";
    $lastName  = isset($_POST["lastName"]) ? $_POST["lastName"] : "";
    $mobile    = isset($_POST["mobile"]) ? $_POST["mobile"] : "";
    $email     = isset($_POST["email"]) ? $_POST["email"] : "";
    $pass      = isset($_POST["password"]) ? password_hash($_POST["password"], PASSWORD_DEFAULT) : "";

    if (!empty($firstName) && !empty($lastName) && !empty($mobile) && !empty($email) && !empty($pass)) {
        // Check if email or mobile already exists
        $checkQuery = "SELECT * FROM users WHERE email = ? OR mobile = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ss", $email, $mobile);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "Email or Mobile already registered!", "redirect" => "login.html"]);
        } else {
            $query = "INSERT INTO users (first_name, last_name, mobile, email, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssss", $firstName, $lastName, $mobile, $email, $pass);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Signup successful!", "redirect" => "login.html"]);
            } else {
                echo json_encode(["success" => false, "message" => "Signup failed!"]);
            }
        }
    } else {
        echo json_encode(["success" => false, "message" => "All fields are required!"]);
    }
}

$conn->close();
?>
