<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $query = "SELECT * FROM staff WHERE staff_email = '$email' AND staff_password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['staff_id'] = $row['staff_id'];
            $_SESSION['staff_name'] = $row['staff_name'];
            // ⭐ NEW — store staff role (VERY IMPORTANT)
            // Make sure your 'staff' table has column: staff_role
            $_SESSION['staff_position'] = $row['staff_position'];

            $message = "✅ Successful Login!";
            $redirect = "staff_dashboard3.php";
        } else {
            $message = "❌ Invalid email or password!";
            $redirect = "testmainscroll1.html";
        }
    } else {
        $message = "❌ Error: " . mysqli_error($conn);
        $redirect = "testmainscroll1.html"; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login Status</title>
  <link rel="stylesheet" href="style.css">
  <style>
     @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f5f8ff; margin: 0; padding: 0; color: #333; min-height: 100vh; }
        * { box-sizing: border-box; } /* Excellent practice for layout consistency */

    /* Navbar styling like main page */
    .navbar {
      position: fixed;
      top: 0;
      width: 100%;
      background-color: #0056b3;
      display: flex;
      align-items: center;
      padding: 15px 40px;
      z-index: 1000;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    .navbar .logo {
      cursor: pointer;
    }

    .navbar .logo img {
      width: 250px;
      height: 55px;
      object-fit: cover;
    }

    .logo-img { 
    /* Set the size of your logo image here */
      width: 200px; /* Adjust this width as needed */
      height: auto; 
      object-fit: contain; 
    }

    /* Popup styling */
    .popup-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      padding-top: 70px; /* space for navbar */
      box-sizing: border-box;
    }

    .popup-box {
      padding: 40px 60px;
      background-color: rgba(242,242,242,0.9);
      border-radius: 10px;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
      text-align: center;
    }

    .popup-box h2 {
      font-size: 22px;
      margin: 0 0 10px 0;
    }
  </style>

  <script>
    // Redirect automatically after 3 seconds
    setTimeout(function() {
      window.location.href = "<?php echo $redirect; ?>";
    }, 2000);
  </script>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <div class="logo">
      <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img">
    </div>
  </div>

  <!-- Popup message -->
  <div class="popup-container">
    <div class="popup-box">
      <h2><?php echo $message; ?></h2>
      <p>Please Wait Redirecting...</p>
    </div>
  </div>

</body>
</html>

