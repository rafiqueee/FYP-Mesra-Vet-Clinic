<?php 
session_start();
include 'db_connect.php';

// --- Security: Only logged-in staff can access
if (!isset($_SESSION['staff_id'])) {
    echo "<script>alert('Please login first!'); window.location='testmainscroll1.html?action=login';</script>";
    exit();
}

// --- Search Logic ---
$search_term = '';
$where_clause = '';

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = mysqli_real_escape_string($conn, trim($_GET['search']));
    $where_clause = " WHERE staff_name LIKE '%$search_term%' OR staff_email LIKE '%$search_term%' OR staff_position LIKE '%$search_term%'";
}

// --- Fetch staff info
$query = "SELECT staff_id, staff_name, staff_email, staff_position, staff_phone FROM staff" . $where_clause . " ORDER BY staff_name ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Information | Mesra Vet Clinic</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
body { font-family: "Poppins", sans-serif; background-color: #f5f8ff; margin:0; padding:0; color:#333; }
.navbar { background: #0056b3; color:white; display:flex; justify-content:space-between; align-items:center; padding:15px 40px; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
.navbar .logo { font-size: 22px; font-weight:700; }
.navbar .links a { background-color:white; color:#0056b3; padding:8px 16px; border-radius:6px; text-decoration:none; font-weight:600; transition:all 0.3s ease; margin-left:10px; }
.navbar .links a:hover { background-color:#e6efff; }
.logo-img { width:250px; height:55px; object-fit:cover; }

.dashboard-container { max-width:900px; margin:80px auto; padding:40px; background:white; border-radius:16px; box-shadow:0 4px 16px rgba(0,0,0,0.08); animation:fadeIn 0.5s ease-in-out; }
.title-left { color:#003d80; font-size:24px; margin:0 0 10px 0; display:block; }
.search-form { float:right; margin-bottom:25px; }
.search-input { padding:10px 15px; border:1px solid #ccc; border-radius:8px; outline:none; font-size:14px; width:250px; background:white; transition:border-color 0.3s ease, box-shadow 0.3s ease; }
.search-input:focus { border-color:#0056b3; box-shadow:0 0 0 3px rgba(0,86,179,0.2); }

table { width:100%; border-collapse:collapse; margin-top:15px; }
th, td { padding:12px; border-bottom:1px solid #ddd; text-align:left; }
th { background-color:#0056b3; color:white; }
tr:hover { background-color:#f1f6ff; }

.action-btn { text-decoration:none; color:white; padding:6px 8px; border-radius:4px; font-size:14px; margin-right:8px; display:inline-flex; align-items:center; transition:background-color 0.2s; }
.edit-btn { background-color:#17a2b8; }
.edit-btn:hover { background-color:#138496; }
.delete-btn { background-color:#dc3545; }
.delete-btn:hover { background-color:#c82333; }

.no-data { color:#777; font-style:italic; margin-top:10px; text-align:center; }

@keyframes fadeIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
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

  <h2 class="title-left">Staff Information</h2>

  <!-- Search Form -->
  <form method="GET" action="" class="search-form">
      <input type="text" name="search" placeholder="Search by name, email, or position..." value="<?php echo htmlspecialchars($search_term); ?>" class="search-input">
  </form>
  <div style="clear:both;"></div>

  <?php if (mysqli_num_rows($result) > 0): ?>
  <table>
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Phone</th>
      <th>Position</th>
      <th>Action</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['staff_name']); ?></td>
        <td><?php echo htmlspecialchars($row['staff_email']); ?></td>
        <td><?php echo htmlspecialchars($row['staff_phone']); ?></td>
        <td><?php echo htmlspecialchars($row['staff_position']); ?></td>
        <td>
          <?php
          // Show edit button only for Manager OR self
          if ($_SESSION['staff_position'] === 'Manager' || $_SESSION['staff_id'] == $row['staff_id']):
          ?>
          <a href="update_staff_profile.php?id=<?php echo $row['staff_id']; ?>" class="action-btn edit-btn" title="Edit Staff">
            <i class="fa-solid fa-pen-to-square"></i>
          </a>
          <?php endif; ?>

          <?php
          // Show delete button only for Manager
          if ($_SESSION['staff_position'] === 'Manager'):
          ?>
          <a href="delete_staff.php?id=<?php echo $row['staff_id']; ?>" class="action-btn delete-btn" title="Delete Staff" onclick="return confirm('Are you sure you want to delete this staff?');">
            <i class="fa-solid fa-trash"></i>
          </a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
    <p class="no-data">
      <?php echo !empty($search_term) ? "No staff records found matching \"$search_term\"." : "No staff records found."; ?>
    </p>
  <?php endif; ?>

</div>
</body>
</html>

