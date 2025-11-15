<?php
// booking_information.php
include 'db_connect.php'; // your DB connection file

$query = "SELECT b.booking_id, b.booking_date, b.booking_time, b.purpose_of_visit, p.petowners_name 
          FROM booking b 
          JOIN petowners p ON b.petowners_ID = p.petowners_ID 
          ORDER BY b.booking_date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Information | Mesra Vet Shop</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
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
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
      transition: 0.3s;
    }

    .navbar .links a:hover {
      background-color: #e6efff;
    }

    .logo-img {
      width: 250px;
      height: 55px;
      object-fit: cover;
    }

    .container {
      max-width: 1100px;
      margin: 50px auto;
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.08);
      padding: 30px 40px;
      animation: fadeIn 0.5s ease-in-out;
    }

    h2 {
      color: #003d80;
      font-size: 26px;
      margin-bottom: 20px;
    }

    .search-box {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 15px;
    }

    .search-box input {
      padding: 10px 14px;
      width: 250px;
      border: 2px solid #0056b3;
      border-radius: 10px;
      font-size: 14px;
      transition: 0.3s;
    }

    .search-box input:focus {
      outline: none;
      border-color: #0077b6;
      box-shadow: 0 0 6px rgba(0,119,182,0.3);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #0056b3;
      color: white;
    }

    tr:hover {
      background-color: #eaf7ff;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <div class="navbar">
    <div class="logo">
      <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img">
    </div>
    <div class="links">
      <a href="staff_dashboard3.php"><i class="fa-solid fa-arrow-left"></i> Back</a>
      
    </div>
  </div>

  <div class="container">
    <h2>All Booking Information</h2>

    <div class="search-box">
      <input type="text" id="searchInput" placeholder="Search by name or purpose...">
    </div>

    <table id="bookingTable">
      <thead>
        <tr>
          <th>Booking ID</th>
          <th>Date</th>
          <th>Time</th>
          <th>Pet Owner</th>
          <th>Purpose</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= htmlspecialchars($row['booking_id']); ?></td>
          <td><?= htmlspecialchars($row['booking_date']); ?></td>
          <td><?= htmlspecialchars($row['booking_time']); ?></td>
          <td><?= htmlspecialchars($row['petowners_name']); ?></td>
          <td><?= htmlspecialchars($row['purpose_of_visit']); ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <script>
    // Live search filter
    document.getElementById("searchInput").addEventListener("keyup", function() {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll("#bookingTable tbody tr");
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
      });
    });
  </script>

</body>
</html>
