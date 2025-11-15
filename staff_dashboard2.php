<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Dashboard | Mesra Vet Clinic</title>
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
      display: flex;
      align-items: center;
    }

    .logo-img {
      width: 250px;
      height: 55px;
      margin-right: 15px;
      object-fit: cover;
    }

    .navbar .links a {
      background-color: white;
      color: #0056b3;
      padding: 8px 16px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .navbar .links a:hover {
      background-color: #e6efff;
    }

    /* DASHBOARD CONTAINER */
    .dashboard-container {
      max-width: 1000px;
      margin: 80px auto;
      padding: 40px;
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
      text-align: center;
      animation: fadeIn 0.5s ease-in-out;
    }

    .dashboard-container h2 {
      color: #003d80;
      font-size: 26px;
      margin-bottom: 10px;
    }

    .dashboard-container p {
      color: #555;
      font-size: 15px;
      margin-bottom: 40px;
    }

    /* DASHBOARD GRID BUTTONS */
    .dashboard-buttons {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 30px; /* spacing between the two rows */
      margin-top: 30px;
    }

    .dashboard-row {
      display: flex;
      justify-content: center;
      gap: 40px; /* spacing between cards */
      flex-wrap: nowrap; /* keeps cards on same line */
    }

    .dashboard-card {
      background-color: #f9fbff;
      border-radius: 12px;
      padding: 30px 20px;
      width: 220px;
      max-width: 220px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    }

    .dashboard-card:hover {
      background-color: #eaf1ff;
      transform: translateY(-5px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .dashboard-card i {
      font-size: 32px;
      color: #0056b3;
      margin-bottom: 10px;
    }

    .dashboard-card h4 {
      color: #003d80;
      font-size: 16px;
      margin-top: 5px;
    }

    /* FOOTER */
    .footer {
      text-align: center;
      padding: 20px;
      color: #888;
      font-size: 14px;
      margin-top: 60px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
    .dashboard-row {
      flex-direction: column;
      align-items: center;
    }
  }
  </style>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <div class="logo">
      <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img">
    </div>
    <div class="links">
      <a href="testmainscroll1.html"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
  </div>

  <!-- Dashboard -->
  <div class="dashboard-container">
  <h2>Welcome, <?php echo isset($_SESSION['staff_name']) ? htmlspecialchars($_SESSION['staff_name']) : 'Staff'; ?> </h2>
  <p>Access daily schedules, client records, staff details, and reports — all in one place.</p>

  <div class="dashboard-buttons">
    <!-- First Row (3 cards) -->
    <div class="dashboard-row">
      <div class="dashboard-card" onclick="window.location.href='daily_schedule.php'">
        <i class="fa-solid fa-calendar-day"></i>
        <h4>Daily Schedule</h4>
      </div>

      <div class="dashboard-card" onclick="window.location.href='booking_information.php'">
        <i class="fa-solid fa-circle-info"></i>
        <h4>Booking Information</h4>
      </div>

      <div class="dashboard-card" onclick="window.location.href='petowners_info.php'">
        <i class="fa-solid fa-paw"></i>
        <h4>Pet Owners</h4>
      </div>
    </div>

    <!-- Second Row (2 cards) -->
    <div class="dashboard-row">
      <div class="dashboard-card" onclick="window.location.href='staff_info.php'">
        <i class="fa-solid fa-user-md"></i>
        <h4>Staff Info</h4>
      </div>

      <div class="dashboard-card" onclick="window.location.href='generate_report2.php'">
        <i class="fa-solid fa-chart-line"></i>
        <h4>Reports</h4>
      </div>
    </div>
  </div>
</div>

  <div class="footer">
    &copy; <?php echo date("Y"); ?> Mesra Vet Clinic. All rights reserved.
  </div>

</body>
</html>

