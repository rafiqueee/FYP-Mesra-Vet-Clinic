<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['petowners_ID'])) {
    header("Location: testmainscroll1.html");
    exit();
}

$user_id = $_SESSION['petowners_ID'];

// Fetch existing user info
$result = mysqli_query($conn, "SELECT * FROM petowners WHERE petowners_ID='$user_id'");
$user = mysqli_fetch_assoc($result);

// Handle form submission
if (isset($_POST['update'])) {
    $email = $_POST['petowners_email'];
    $phone = $_POST['petowners_phone'];
    $address = $_POST['petowners_address'];

    $query = "UPDATE petowners 
              SET petowners_email='$email', petowners_phone='$phone', petowners_address='$address' 
              WHERE petowners_ID='$user_id'";
    mysqli_query($conn, $query);

    echo "
    <div class='top-popup'>
      <span>✅ Profile updated successfully!</span>
      <button onclick=\"closePopup()\">OK</button>
    </div>

    <script>
      function closePopup() {
        document.querySelector('.top-popup').style.display = 'none';
        window.location.href = 'petowners_dashboard2.php';
      }
    </script>

    <style>
      .top-popup {
        position: fixed;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #ffffff;
        border: 2px solid #0056b3;
        color: #0056b3;
        font-family: Arial, sans-serif;
        padding: 12px 25px;
        border-radius: 10px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.2);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: fadeIn 0.4s ease-in-out;
      }

      .top-popup span {
        font-weight: 600;
        font-size: 15px;
      }

      .top-popup button {
        background-color: #0056b3;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
      }

      .top-popup button:hover {
        background-color: #003d80;
      }

      @keyframes fadeIn {
        from { opacity: 0; transform: translate(-50%, -10px); }
        to { opacity: 1; transform: translate(-50%, 0); }
      }
    </style>
    ";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Profile</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
   @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f5f8ff; margin: 0; padding: 0; color: #333; min-height: 100vh; }
        * { box-sizing: border-box; } /* Excellent practice for layout consistency */

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
      letter-spacing: 1px;
    }

  .navbar .logo-img {
    width: 250px;
    height: 55px;
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

  /* FORM CONTAINER */
  .update-container {
    max-width: 600px;
    background: white;
    margin: 50px auto;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  }

  h2 {
    color: #003d80;
    text-align: center;
    margin-bottom: 25px;
  }

  label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #0056b3;
  }

  input {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
  }

  input:focus {
    border-color: #0056b3;
    outline: none;
    box-shadow: 0 0 6px rgba(0, 86, 179, 0.2);
  }

  .btn {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
  }

  .save-btn {
    background-color: #0056b3;
    color: white;
  }

  .save-btn:hover {
    background-color: #004099;
  }

  .cancel-btn {
    background-color: #ccc;
    color: #333;
    margin-top: 10px;
  }

  .cancel-btn:hover {
    background-color: #b3b3b3;
  }
</style>
</head>

<body>
  <div class="navbar">
    <div class="logo" onclick="goBack()">
      <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img">
    </div>
    <div class="links">
      <a href="testmainscroll1.html"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
  </div>

  <div class="update-container">
    <h2>Update Profile</h2>
    <form method="POST">
      <label>Name:</label>
      <input type="text" name="petowners_name" value="<?= htmlspecialchars($user['petowners_name']) ?>" readonly>

      <label>Email:</label>
      <input type="email" name="petowners_email" value="<?= htmlspecialchars($user['petowners_email']) ?>" required>

      <label>Phone Number:</label>
      <input type="text" name="petowners_phone" value="<?= htmlspecialchars($user['petowners_phone']) ?>" required>

      <label>Address:</label>
      <input type="text" name="petowners_address" value="<?= htmlspecialchars($user['petowners_address']) ?>" required>

      <button type="submit" name="update" class="btn save-btn">Save Changes</button>
      <button type="button" class="btn cancel-btn" onclick="goBack()">Cancel</button>
    </form>
  </div>

  <script>
    function goBack() {
      window.location.href = "petowners_dashboard2.php"; // redirect to dashboard
    }
  </script>
</body>
</html>

