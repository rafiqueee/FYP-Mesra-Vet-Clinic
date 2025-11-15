<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['petowners_ID'])) {
    header("Location: testmainscroll.html");
    exit();
}

$petowners_ID = $_SESSION['petowners_ID'];
$query = "SELECT * FROM booking WHERE petowners_ID = '$petowners_ID' ORDER BY booking_date DESC, booking_time DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Appointments | Mesra Vet Clinic</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f9fc; margin: 0; padding: 0;">

  <!-- Navbar -->
  <div style="position: fixed; top: 0; width: 100%; background-color: #0056b3; display: flex; justify-content: space-between; align-items: center; padding: 12px 30px; z-index: 1000; box-shadow: 0 3px 8px rgba(0,0,0,0.1); color: white;">
    <div style="display: flex; align-items: center; cursor: pointer;" onclick="window.location.href='petowners_dashboard.php'">
      <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" style="width: 250px; height: 55px; margin-right: 15px; border-radius: 0; object-fit: cover;">
    </div>
  </div>

  <!-- Appointments Table Container -->
  <div style="max-width: 900px; margin: 120px auto 50px auto; background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
    <h2 style="color: #0056b3; text-align: center; margin-bottom: 25px;">My Appointments</h2>

    <a href="petowners_dashboard.php" 
       style="display:inline-block; margin-top:25px; background-color:#0056b3; color:white; padding:10px 20px; border-radius:6px; text-decoration:none; font-weight:600; transition:0.3s;" 
       onmouseover="this.style.backgroundColor='#003d80'" 
       onmouseout="this.style.backgroundColor='#0056b3'">← Back to Dashboard</a>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <?php
        $rows = [];
        while ($r = mysqli_fetch_assoc($result)) { $rows[] = $r; }

        usort($rows, function($a, $b) {
          return strtotime($b['booking_date'] . ' ' . $b['booking_time'])
               - strtotime($a['booking_date'] . ' ' . $a['booking_time']);
        });
        $latestBookingDate = $rows[0]['booking_date'] . ' ' . $rows[0]['booking_time'];
      ?>

      <table style="width: 100%; border-collapse: collapse; margin-top: 25px;">
        <tr style="background-color: #0056b3; color: white; font-weight: 600;">
          <th style="padding: 14px; text-align: left;">Booking Date</th>
          <th style="padding: 14px; text-align: left;">Time</th>
          <th style="padding: 14px; text-align: left;">Purpose</th>
          <th style="padding: 14px; text-align: left;">Status</th>
        </tr>

        <?php foreach ($rows as $row): ?>
          <?php
            $currentBooking = $row['booking_date'] . ' ' . $row['booking_time'];
            $highlight = ($currentBooking == $latestBookingDate) ? 'background-color:#e6f0ff;font-weight:bold;' : '';
          ?>
          <tr style="border-bottom:1px solid #ddd; <?php echo $highlight; ?>" 
              onmouseover="this.style.backgroundColor='#d0e0ff'" 
              onmouseout="this.style.backgroundColor='<?php echo ($highlight) ? '#e6f0ff' : 'transparent'; ?>'">
            <td style="padding: 14px;"><strong style="color: #0056b3;"><?php echo date("d M Y", strtotime($row['booking_date'])); ?></strong></td>
            <td style="padding: 14px;"><strong style="color: #0056b3;"><?php echo date("h:i A", strtotime($row['booking_time'])); ?></strong></td>
            <td style="padding: 14px;"><?php echo htmlspecialchars($row['purpose_of_visit']); ?></td>
            <td style="padding: 14px;"><?php echo ucfirst($row['booking_status']); ?></td>
          </tr>
        <?php endforeach; ?>
      </table>

    <?php else: ?>
      <p style="text-align:center; color:#777; font-style:italic; margin-top: 20px;">No recent bookings found.</p>
    <?php endif; ?>

    
  </div>

</body>
</html>

