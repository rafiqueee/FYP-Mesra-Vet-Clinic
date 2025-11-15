<?php
session_start();
// NOTE: Ensure your db_connect.php file is correctly configured to connect to your database.
include 'db_connect.php';

// --- Session Check (Assuming the staff ID is stored in 'staff_ID' session variable) ---
if (!isset($_SESSION['staff_id'])) {
    // Redirect to a staff login page if not logged in
    header("Location: testmainscroll1.html"); 
    exit();
}

$staff_id = $_SESSION['staff_id'];

// --- Helper Function for Popup Styling (Used for success message) ---
function getPopupStyles() {
    return '
    <style>
      .top-popup {
        position: fixed;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #ffffff;
        border: 2px solid #28a745; /* Green border for success */
        color: #28a745;
        font-family: \'Poppins\', sans-serif;
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
        background-color: #28a745;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
      }

      .top-popup button:hover {
        background-color: #218838;
      }

      @keyframes fadeIn {
        from { opacity: 0; transform: translate(-50%, -10px); }
        to { opacity: 1; transform: translate(-50%, 0); }
      }
    </style>
    ';
}

// --- Handle Form Submission ---
if (isset($_POST['update'])) {
    // 1. Get input data
    $email = trim($_POST['staff_email'] ?? '');
    $phone = trim($_POST['staff_phone'] ?? '');
    $address = trim($_POST['staff_address'] ?? '');

    // 2. Basic Server-Side Validation
    if (empty($email) || empty($phone) || empty($address)) {
        echo "<script>alert('All fields are required.');</script>";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         echo "<script>alert('Please enter a valid email address.');</script>";
    } else {
        // 3. Prepare Update Query (Securely using prepared statements)
        $stmt = $conn->prepare("UPDATE staff 
                                SET staff_email=?, staff_phone=?, staff_address=? 
                                WHERE staff_id=?");
        
        // 4. Bind parameters (s=string, s=string, s=string, i=integer)
        $stmt->bind_param("sssi", $email, $phone, $address, $staff_id);
        
        // 5. Execute
        if ($stmt->execute()) {
            $stmt->close();
            
            // Success response with the custom popup style and redirect
            echo getPopupStyles(); // Include the necessary CSS for the popup
            echo "
            <div class='top-popup'>
              <span>✅ Profile updated successfully!</span>
              <button onclick=\"closePopup()\">OK</button>
            </div>

            <script>
              function closePopup() {
                document.querySelector('.top-popup').style.display = 'none';
                window.location.href = 'staff_dashboard3.php'; // Redirect to staff dashboard
              }
            </script>
            ";
            $conn->close();
            exit();

        } else {
            // Error handling
            $error = $stmt->error;
            $stmt->close();
            echo "<script>alert('Error updating profile: " . htmlspecialchars($error) . "');</script>";
            // Fall through to show the form again with old data
        }
    }
}

// --- Fetch Staff Info for Display (Always runs if not updated or if update failed) ---
$stmt_fetch = $conn->prepare("SELECT staff_name, staff_email, staff_phone, staff_address FROM staff WHERE staff_id=?");
// Assuming staff_ID is an integer type in the database
$stmt_fetch->bind_param("i", $staff_id); 
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();
$staff = $result->fetch_assoc();
$stmt_fetch->close();

// Check if staff data was found
if (!$staff) {
    echo "<script>alert('Staff profile not found.'); window.location.href='staff_dashboard3.php';</script>";
    $conn->close();
    exit();
}

// Extract and safely escape variables for use in HTML form fields
$staff_name = htmlspecialchars($staff['staff_name']);
$staff_email = htmlspecialchars($staff['staff_email']);
$staff_phone = htmlspecialchars($staff['staff_phone']);
$staff_address = htmlspecialchars($staff['staff_address']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Staff Profile</title>
<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    body { font-family: 'Poppins', sans-serif; background-color: #f5f8ff; margin: 0; padding: 0; color: #333; min-height: 100vh; }
    * { box-sizing: border-box; } 

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

    .input-group {
        margin-bottom: 20px;
    }
    
    input, textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      transition: all 0.3s ease;
    }

    textarea {
        resize: vertical;
        min-height: 80px;
    }

    input:focus, textarea:focus {
      border-color: #0056b3;
      outline: none;
      box-shadow: 0 0 6px rgba(0, 86, 179, 0.2);
    }
    
    input[readonly] {
        background-color: #f0f0f0;
        cursor: not-allowed;
        color: #666;
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
      margin-top: 10px;
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

    @media (max-width: 600px) {
        .navbar {
            padding: 15px 20px;
        }
        .navbar .logo-img {
            width: 150px;
        }
        .update-container {
            margin: 20px;
            padding: 25px;
        }
    }
</style>
</head>

<body>
  <div class="navbar">
    <div class="logo">
      <!-- Placeholder logo image - adjust src if needed -->
      <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img">
    </div>
    <div class="links">
    <a href="staff_dashboard3.php"><i class="fa-solid fa-arrow-left"></i> Back</a>
  </div>
  </div>

  <div class="update-container">
    <h2>Update Staff Profile</h2>
    <form method="POST">
        
      <div class="input-group">
          <label>Name:</label>
          <!-- Staff name is read-only -->
          <input type="text" name="staff_name" value="<?= $staff_name ?>" readonly>
      </div>

      <div class="input-group">
          <label>Email:</label>
          <input type="email" name="staff_email" value="<?= $staff_email ?>" required>
      </div>

      <div class="input-group">
          <label>Phone Number:</label>
          <input type="text" name="staff_phone" value="<?= $staff_phone ?>" required>
      </div>

      <div class="input-group">
          <label>Address:</label>
          <textarea name="staff_address" required><?= $staff_address ?></textarea>
      </div>

      <button type="submit" name="update" class="btn save-btn">Save Changes</button>
      <button type="button" class="btn cancel-btn" onclick="goBack()">Cancel</button>
    </form>
  </div>

  <script>
    function goBack() {
      // Redirects to the staff dashboard if the user clicks cancel
      window.location.href = "staff_dashboard3.php"; 
    }
  </script>
</body>
</html>