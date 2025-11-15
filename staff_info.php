<?php 
session_start();
include 'db_connect.php';

// Fetch staff info
$query = "SELECT staff_id, staff_name, staff_email, staff_position, staff_phone FROM staff ORDER BY staff_name ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Information | Mesra Vet Clinic</title>
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
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
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

  <!-- NAVBAR -->
  <div class="navbar">
    <div class="logo">
      <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img">
    </div>
    <div class="links">
      <a href="staff_dashboard3.php"><i class="fa-solid fa-arrow-left"></i> Back</a>
    
    </div>
  </div>

  <!-- STAFF INFO -->
  <div class="dashboard-container">
    <h2>Staff Information</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <table>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Position</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['staff_name']); ?></td>
            <td><?php echo htmlspecialchars($row['staff_email']); ?></td>
            <td><?php echo htmlspecialchars($row['staff_phone']); ?></td>
            <td><?php echo htmlspecialchars($row['staff_position']); ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p class="no-data">No staff records found.</p>
    <?php endif; ?>
  </div>

</body>
</html>
