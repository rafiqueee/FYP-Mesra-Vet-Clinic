<?php
include 'db_connect.php';

$range = isset($_GET['range']) ? intval($_GET['range']) : 30;
$view = isset($_GET['view']) ? $_GET['view'] : 'weekly';

$labels = [];
$values = [];

if ($view === 'weekly') {
  // Group bookings by week
  $query = "
    SELECT YEARWEEK(booking_date, 1) AS week_label, COUNT(*) AS total
    FROM booking
    WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL $range DAY)
    GROUP BY week_label
    ORDER BY week_label ASC
  ";
  $result = mysqli_query($conn, $query);
  while ($row = mysqli_fetch_assoc($result)) {
    $weekNum = substr($row['week_label'], 4); // Get week number
    $labels[] = 'Week ' . $weekNum;
    $values[] = intval($row['total']);
  }
} else {
  // Group bookings by month
  $query = "
    SELECT DATE_FORMAT(booking_date, '%Y-%m') AS month_label, COUNT(*) AS total
    FROM booking
    WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL $range DAY)
    GROUP BY month_label
    ORDER BY month_label ASC
  ";
  $result = mysqli_query($conn, $query);
  while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = date("M Y", strtotime($row['month_label'] . '-01'));
    $values[] = intval($row['total']);
  }
}

echo json_encode([
  "labels" => $labels,
  "values" => $values
]);
?>
