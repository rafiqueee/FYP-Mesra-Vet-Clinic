<?php 
session_start();
include 'db_connect.php';

// Get today's date
$today = date("Y-m-d");

// Fetch today's bookings joined with pet owners
$query = "
  SELECT b.booking_time, b.purpose_of_visit, b.booking_status, p.petowners_name
  FROM booking b
  JOIN petowners p ON b.petowners_ID = p.petowners_ID
  WHERE b.booking_date = '$today'
  ORDER BY b.booking_time ASC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Daily Schedule | Mesra Vet Clinic</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
     @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f5f8ff;
      margin: 0;
      padding: 0;
      color: #333;
    }

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
    }

    .navbar .links a:hover {
      background-color: #e6efff;
    }

    .logo-img {
      width: 250px;
      height: 55px;
      margin-right: 15px;
      object-fit: cover;
    }

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
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #0056b3;
      color: white;
      font-weight: 600;
    }

    tr:hover {
      background-color: #f1f6ff;
    }

    .no-data {
      color: #777;
      font-style: italic;
      margin-top: 10px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
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
      <a href="staff_dashboard3.php"><i class="fa-solid fa-arrow-left"></i> Back</a>
      
    </div>
  </div>

  <!-- Daily Schedule -->
  <div class="dashboard-container">
    <h2>Daily Schedule (<?php echo date("d M Y"); ?>)</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <table>
        <tr>
          <th>Time</th>
          <th>Pet Owner</th>
          <th>Purpose</th>
          <th>Status</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><strong><?php echo date("h:i A", strtotime($row['booking_time'])); ?></strong></td>
          <td><?php echo htmlspecialchars($row['petowners_name']); ?></td>
          <td><?php echo htmlspecialchars($row['purpose_of_visit']); ?></td>
          <td><?php echo ucfirst($row['booking_status']); ?></td>
        </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p class="no-data">No bookings scheduled for today.</p>
    <?php endif; ?>
  </div>

</body>
</html>
