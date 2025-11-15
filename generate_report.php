<?php 
session_start();
include 'db_connect.php';

// Optional: You could generate summaries here, e.g., number of bookings today/month
$today = date("Y-m-d");
$month = date("m");

$daily_count_query = "SELECT COUNT(*) AS total FROM booking WHERE booking_date = '$today'";
$monthly_count_query = "SELECT COUNT(*) AS total FROM booking WHERE MONTH(booking_date) = '$month'";

$daily_count = mysqli_fetch_assoc(mysqli_query($conn, $daily_count_query))['total'];
$monthly_count = mysqli_fetch_assoc(mysqli_query($conn, $monthly_count_query))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports | Mesra Vet Clinic</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f5f8ff;
      margin: 0;
      padding: 0;
      color: #333;
    }

    /* NAVBAR */
    .navbar {
      background: #0056b3;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 40px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar .logo {
      font-size: 22px;
      font-weight: 700;
    }

    .navbar .links a {
      background-color: white;
      color: #0056b3;
      padding: 8px 16px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      margin-left: 10px;
    }

    .navbar .links a:hover {
      background-color: #e6efff;
    }

    .logo-img {
      width: 250px;
      height: 55px;
      object-fit: cover;
    }

    /* CONTAINER */
    .dashboard-container {
      max-width: 900px;
      margin: 80px auto;
      padding: 40px;
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
      text-align: center;
      animation: fadeIn 0.5s ease-in-out;
    }

    h2 {
      color: #003d80;
      font-size: 24px;
      margin-bottom: 10px;
    }

    p {
      color: #555;
      font-size: 15px;
      margin-bottom: 30px;
    }

    /* REPORT STATS BOXES */
    .report-stats {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
      margin-bottom: 40px;
    }

    .stat-box {
      background-color: #f9fbff;
      border: 1px solid #e0e7ff;
      border-radius: 12px;
      width: 220px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
    }

    .stat-box:hover {
      transform: translateY(-5px);
      background-color: #eaf1ff;
    }

    .stat-box h3 {
      color: #0056b3;
      font-size: 22px;
      margin: 0;
    }

    .stat-box span {
      display: block;
      color: #666;
      margin-top: 5px;
      font-size: 15px;
    }

    /* BUTTONS */
    .report-actions {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    .report-actions a {
      display: inline-block;
      background-color: #0056b3;
      color: white;
      padding: 12px 25px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: background 0.3s;
    }

    .report-actions a:hover {
      background-color: #003d80;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <div class="navbar">
    <div class="logo">
      <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img">
    </div>
    <div class="links">
      <a href="staff_dashboard2.php"><i class="fa-solid fa-arrow-left"></i> Back</a>
      <a href="testmainscroll.html"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
  </div>

  <!-- REPORT CONTENT -->
  <div class="dashboard-container">
    <h2>📊 Reports Overview</h2>
    <p>View or generate daily and monthly booking reports for Mesra Vet Clinic.</p>

    <div class="report-stats">
      <div class="stat-box">
        <h3><?php echo $daily_count; ?></h3>
        <span>Bookings Today</span>
      </div>

      <div class="stat-box">
        <h3><?php echo $monthly_count; ?></h3>
        <span>Bookings This Month</span>
      </div>
    </div>

    <div class="report-actions">
      <a href="download_daily_report.php"><i class="fa-solid fa-file-arrow-down"></i> Download Daily Report</a>
      <a href="download_monthly_report.php"><i class="fa-solid fa-file-lines"></i> Download Monthly Report</a>
    </div>
  </div>

</body>
</html>
