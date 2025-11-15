<?php
session_start();
include 'db_connect.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize input
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
   

    // 3. Check if email already exists
    $check_query = "SELECT staff_id FROM staff WHERE staff_email = '$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Email already registered
        $_SESSION['error_message'] = "This email is already registered as a staff member.";
        header("Location: login_page.php?section=staff-signup&error=1"); // Adjust redirect page as needed
        exit();
    }

    // 4. Insert new staff record
    $insert_query = "INSERT INTO staff (staff_name, staff_email, staff_password, staff_position, staff_phone, staff_address) 
                     VALUES ('$name', '$email', '$password', '$position', '$phone', '$address')";

    if (mysqli_query($conn, $insert_query)) {
        // Registration successful
        $_SESSION['success_message'] = "Staff registration successful. You can now log in.";
        header("Location: login_page.php?section=staff"); // Redirect to staff login
        exit();
    } else {
        // Database error
        $_SESSION['error_message'] = "Registration failed: " . mysqli_error($conn);
        header("Location: login_page.php?section=staff-signup&error=1"); // Adjust redirect page as needed
        exit();
    }
} else {
    // If someone accesses this page directly without POST, redirect them
    header("Location: login_page.php");
    exit();
}

// Close the connection
mysqli_close($conn);
?>