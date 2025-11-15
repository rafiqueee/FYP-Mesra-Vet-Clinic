<?php
include 'db_connect.php';

// Check if "edit" parameter is passed
if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  $result = mysqli_query($conn, "SELECT * FROM petinfo WHERE pet_id='$id'");
  $pet = mysqli_fetch_assoc($result);
} else {
  echo "<script>alert('No pet selected.'); window.location.href='manage_petinfo.php';</script>";
  exit();
}

// Handle form submission
if (isset($_POST['update'])) {
  $id = $_POST['pet_id'];
  $pet_name = $_POST['pet_name'];
  $pet_breed = $_POST['pet_breed'];
  $pet_age = $_POST['pet_age'];
  $pet_color = $_POST['pet_color'];
  $pet_gender = $_POST['pet_gender'];

  $query = "UPDATE petinfo 
            SET pet_name='$pet_name', pet_breed='$pet_breed', pet_age='$pet_age', 
                pet_color='$pet_color', pet_gender='$pet_gender' 
            WHERE pet_id='$id'";
  mysqli_query($conn, $query);

  echo "
<div class='top-popup'>
  <span>✅ Pet information updated successfully!</span>
  <button onclick=\"closePopup()\">OK</button>
</div>

<script>
  function closePopup() {
    document.querySelector('.top-popup').style.display = 'none';
    window.location.href = 'mypets2.php';
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
<title>Update Pet Information</title>
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

  input, select {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
  }

  input:focus, select:focus {
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
    <h2>Update Pet Information</h2>
    <form method="POST">
       <input type="hidden" name="pet_id" value="<?= htmlspecialchars($pet['pet_ID']) ?>">

      <label>Pet Name:</label>
      <input type="text" name="pet_name" value="<?= $pet['pet_name'] ?>" required readonly>

      <label>Breed:</label>
      <input type="text" name="pet_breed" value="<?= $pet['pet_breed'] ?>" required readonly>

      <label>Age:</label>
      <input type="number" name="pet_age" value="<?= $pet['pet_age'] ?>" required>

      <label>Color:</label>
      <input type="text" name="pet_color" value="<?= $pet['pet_color'] ?>" required readonly>

      <label>Gender:</label>
      <input type="text" name="pet_gender" value="<?= $pet['pet_gender'] ?>" readonly>

      <button type="submit" name="update" class="btn save-btn">Save Changes</button>
      <button type="button" class="btn cancel-btn" onclick="goBack()">Cancel</button>
    </form>
  </div>

  <script>
    function goBack() {
      window.location.href = "petowners_dashboard1.php";
    }
  </script>
</body>
</html>