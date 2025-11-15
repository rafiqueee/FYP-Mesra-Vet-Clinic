<?php
$servername = "localhost";
$username = "root"; // default WAMP user
$password = ""; // default WAMP has no password
$dbname = "fyp2_pet_clinic";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>