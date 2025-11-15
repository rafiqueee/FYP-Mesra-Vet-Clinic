<?php
include 'db_connect.php';

$label = isset($_GET['label']) ? $_GET['label'] : '';
$view = isset($_GET['view']) ? $_GET['view'] : 'weekly';
$data = [];

if ($view === 'weekly' && preg_match('/Week (\d+)/', $label, $matches)) {
  $weekNum = intval($matches[1]);
  $query = "
    SELECT b.booking_date, b.booking_time, b.purpose_of_visit, p.petowners_name
    FROM booking b
    JOIN petowners p ON b.petowners_ID = p.petowners_ID
    WHERE YEARWEEK(b.booking_date, 1) = CONCAT(YEAR(CURDATE()), LPAD($weekNum, 2, '0'))
    ORDER BY b.booking_date ASC, b.booking_time ASC
  ";
} elseif ($view === 'monthly') {
  $month = date('m', strtotime($label));
  $year = date('Y', strtotime($label));
  $query = "
    SELECT b.booking_date, b.booking_time, b.purpose_of_visit, p.petowners_name
    FROM booking b
    JOIN petowners p ON b.petowners_ID = p.petowners_ID
    WHERE MONTH(b.booking_date) = $month AND YEAR(b.booking_date) = $year
    ORDER BY b.booking_date ASC, b.booking_time ASC
  ";
} else {
  echo json_encode([]);
  exit;
}

$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
  $data[] = [
    "booking_date" => $row['booking_date'],
    "booking_time" => date("h:i A", strtotime($row['booking_time'])),
    "petowners_name" => $row['petowners_name'],
    "purpose_of_visit" => $row['purpose_of_visit']
  ];
}

echo json_encode($data);
?>
