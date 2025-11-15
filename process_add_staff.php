<?php 
session_start();

// WARNING: Storing passwords in plain text is highly insecure.
// In a real application, you MUST use password_hash() for security.

// NOTE: Ensure your db_connect.php file is correctly configured to connect to your MySQL database.
include 'db_connect.php'; 

// --- 1. Define Helper Function for Messages and Redirection ---
function alertAndRedirect($message, $location = 'window.history.back();') {
    // Escapes single quotes in the message to prevent breaking the JS string
    $safe_message = str_replace("'", "\'", $message);
    echo "<script>alert('{$safe_message}'); {$location}</script>";
    exit();
}

// --- 2. Receive POST Data and Sanitize ---
// Use the null coalescing operator (??) to safely access $_POST data.
$staff_name     = trim(htmlspecialchars($_POST['staff_name'] ?? ''));
$staff_email    = trim(htmlspecialchars($_POST['staff_email'] ?? ''));
$staff_phone    = trim(htmlspecialchars($_POST['staff_phone'] ?? ''));
$staff_position = trim(htmlspecialchars($_POST['staff_position'] ?? ''));
$staff_password = $_POST['staff_password'] ?? '';       // Keep plain text as requested
$staff_confirm  = $_POST['staff_confirm_password'] ?? '';
$staff_address  = trim(htmlspecialchars($_POST['staff_address'] ?? ''));


// --- 3. Server-Side Validation ---

// Check for empty fields
if (empty($staff_name) || empty($staff_email) || empty($staff_phone) || empty($staff_position) || empty($staff_password) || empty($staff_address)) {
    alertAndRedirect('Error: All fields must be filled out.');
}

// Password Length Check
if (strlen($staff_password) < 6) {
    alertAndRedirect('Error: Password must be at least 6 characters long.');
}

// Password Match Check
if ($staff_password !== $staff_confirm) {
    alertAndRedirect('Error: Password confirmation does not match the password!');
}

// Basic email format check
if (!filter_var($staff_email, FILTER_VALIDATE_EMAIL)) {
    alertAndRedirect('Error: Invalid email format.');
}

// --- 4. PREPARE and EXECUTE INSERT QUERY ---

// NOTE: We are inserting the **plain** password as requested by the user.
$sql = $conn->prepare("
    INSERT INTO staff 
    (staff_name, staff_email, staff_phone, staff_position, staff_password, staff_address) 
    VALUES (?, ?, ?, ?, ?, ?)
");

// Check if prepare failed
if ($sql === false) {
    alertAndRedirect("Database error during preparation: " . $conn->error);
}

// Bind parameters (all are strings: "ssssss")
$sql->bind_param("ssssss", 
    $staff_name, 
    $staff_email, 
    $staff_phone, 
    $staff_position, 
    $staff_password, // <-- Inserting plain text password
    $staff_address
);

// EXECUTE
if ($sql->execute()) {
    // Success - redirect to dashboard with success message
    $sql->close();
    $conn->close();
    alertAndRedirect('Staff added successfully!', "window.location='staff_dashboard3.php';");
} else {
    // Error - display specific MySQL error
    $error_message = $conn->error;
    $sql->close();
    $conn->close();
    
    // Display error message to help the user debug table issues or constraints
    alertAndRedirect("Error adding staff. Check your database schema: " . $error_message);
}

// Cleanup the session message just in case
unset($_SESSION['message']);
?>
