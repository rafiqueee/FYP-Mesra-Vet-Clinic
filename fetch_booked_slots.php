<?php
// fetch_booked_slots.php
session_start();
include('db_connect.php'); // Assuming this provides the $conn connection object

header('Content-Type: application/json');

if (!isset($_GET['date'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing date parameter.']);
    exit;
}

$booking_date = $_GET['date'];

// The actual logic: Query the database for all booked times on this date
// NOTE: If you have multiple doctors/resources, you must also filter by doctor/resource ID here!
// For simplicity, this assumes a single resource (the clinic/doctor)
$sql = "SELECT DATE_FORMAT(booking_time, '%H:%i') as booked_time 
        FROM booking 
        WHERE booking_date = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $booking_date);
$stmt->execute();
$result = $stmt->get_result();

$booked_slots = [];
while ($row = $result->fetch_assoc()) {
    // Collect the time in HH:mm format, which matches your JavaScript option values
    $booked_slots[] = $row['booked_time'];
}

$stmt->close();
$conn->close();

// Return the array of booked times as a JSON response
echo json_encode(['booked_slots' => $booked_slots]);
?>