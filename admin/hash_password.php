<?php
$password = "abhi123"; // Admin cha password

// Hashing the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

echo "Hashed Password: " . $hashedPassword;
