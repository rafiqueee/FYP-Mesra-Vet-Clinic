<?php
include 'db_connect.php';

// --- FIX START: Check for the 'pet_ID' parameter used by the dashboard's JavaScript ---
if (isset($_GET['pet_ID'])) {
    $id = $_GET['pet_ID'];
    
    // Fetch pet details for confirmation page
    $result = mysqli_query($conn, "SELECT * FROM petinfo WHERE pet_ID='$id'");
    $pet = mysqli_fetch_assoc($result);

    // If pet not found, show error and redirect
    if (!$pet) {
        echo "<script>console.error('Pet not found for ID: $id'); window.location.href='petowners_dashboard2.php';</script>";
        exit();
    }
} else {
    // If no ID is passed in the URL, redirect back to management page
    echo "<script>console.error('No pet selected for deletion.'); window.location.href='petowners_dashboard2.php';</script>";
    exit();
}
// --- FIX END ---

// Handle form submission (Deletion Confirmation)
if (isset($_POST['delete'])) {
    $id_to_delete = $_POST['pet_id'];

    // Perform the SQL DELETE operation
    $query = "DELETE FROM petinfo WHERE pet_ID='$id_to_delete'";
    mysqli_query($conn, $query);

    // Success Popup (Maintaining original style)
    echo "
<div class='top-popup'>
    <span>🗑️ Pet information successfully deleted!</span>
    <button onclick=\"closePopup()\">OK</button>
</div>

<script>
    function closePopup() {
        document.querySelector('.top-popup').style.display = 'none';
        window.location.href = 'petowners_dashboard2.php'; // Redirect after successful deletion
    }
</script>

<style>
    /* Popup styling maintained from original code */
    .top-popup {
        position: fixed; top: 10px; left: 50%; transform: translateX(-50%);
        background-color: #ffffff; border: 2px solid #0056b3; color: #0056b3;
        font-family: Arial, sans-serif; padding: 12px 25px; border-radius: 10px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.2); z-index: 9999; display: flex;
        align-items: center; gap: 10px; animation: fadeIn 0.4s ease-in-out;
    }
    .top-popup span { font-weight: 600; font-size: 15px; }
    .top-popup button {
        background-color: #0056b3; color: white; border: none; padding: 5px 15px;
        border-radius: 6px; cursor: pointer; font-weight: 600;
    }
    .top-popup button:hover { background-color: #003d80; }
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
<title>Delete Pet Information</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    body { font-family: 'Poppins', sans-serif; background-color: #f5f8ff; margin: 0; padding: 0; color: #333; min-height: 100vh; }
    * { box-sizing: border-box; } 

    /* NAVBAR - Maintained style */
    .navbar { background: #0056b3; display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
    .navbar .logo-img { width: 250px; height: 55px; object-fit: cover; }
    .navbar .links a { background-color: white; color: #0056b3; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; }
    .navbar .links a:hover { background-color: #e6efff; }

    /* FORM CONTAINER - Danger Context */
    .update-container {
        max-width: 600px; background: white; margin: 50px auto; padding: 40px;
        border-radius: 16px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        border: 2px solid #ff4d4d; /* Red border for danger context */
    }

    h2 { color: #cc0000; /* Red title for deletion context */ text-align: center; margin-bottom: 25px; }

    label { display: block; margin-bottom: 5px; font-weight: 600; color: #0056b3; }

    input, select {
        width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ccc;
        border-radius: 8px; font-size: 15px; background-color: #f0f0f0; /* Gray background for readonly fields */
    }

    .btn { width: 100%; padding: 12px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: 0.3s ease; }

    /* Delete Button Style (Danger Red) */
    .delete-btn { background-color: #ff4d4d; color: white; }

    .delete-btn:hover { background-color: #cc0000; }

    .cancel-btn { background-color: #ccc; color: #333; margin-top: 10px; }

    .cancel-btn:hover { background-color: #b3b3b3; }
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
        <h2><i class="fa-solid fa-triangle-exclamation"></i> Confirm Deletion</h2>
        <p style="text-align: center; color: #cc0000; font-weight: 600; margin-bottom: 30px;">
            Are you absolutely sure? Deleting this pet's information is permanent.
        </p>
        <form method="POST">
            <!-- Hidden field for the ID to be deleted -->
            <input type="hidden" name="pet_id" value="<?= htmlspecialchars($pet['pet_ID']) ?>">

            <!-- Display Pet Details for Confirmation (All fields are readonly) -->
            <label>Pet ID:</label>
            <input type="text" value="<?= htmlspecialchars($pet['pet_ID']) ?>" readonly>
            
            <label>Pet Name:</label>
            <input type="text" value="<?= htmlspecialchars($pet['pet_name']) ?>" readonly>

            <label>Breed:</label>
            <input type="text" value="<?= htmlspecialchars($pet['pet_breed']) ?>" readonly>

            <label>Age:</label>
            <input type="number" value="<?= htmlspecialchars($pet['pet_age']) ?>" readonly>

            <label>Color:</label>
            <input type="text" value="<?= htmlspecialchars($pet['pet_color']) ?>" readonly>

            <label>Gender:</label>
            <input type="text" value="<?= htmlspecialchars($pet['pet_gender']) ?>" readonly>

            <!-- Deletion Button -->
            <button type="submit" name="delete" class="btn delete-btn">
                <i class="fa-solid fa-trash-can"></i> Delete Pet Permanently
            </button>
            
            <!-- Cancel Button -->
            <button type="button" class="btn cancel-btn" onclick="goBack()">Cancel</button>
        </form>
    </div>

    <script>
        function goBack() {
            window.location.href = "petowners_dashboard2.php";
        }
    </script>
</body>
</html>