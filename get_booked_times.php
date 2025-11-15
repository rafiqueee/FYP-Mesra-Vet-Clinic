<?php
session_start();
include('db_connect.php');

if (!isset($_GET['date'])) {
    echo json_encode([]);
    exit();
}

$date = $_GET['date'];

// Fetch all booked times for the selected date
$stmt = $conn->prepare("SELECT booking_time FROM booking WHERE booking_date = ?");
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$bookedTimes = [];
while ($row = $result->fetch_assoc()) {
    $bookedTimes[] = $row['booking_time'];
}

$stmt->close();
$conn->close();

echo json_encode($bookedTimes);
?>
