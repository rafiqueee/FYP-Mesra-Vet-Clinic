<?php 
session_start();
include 'db_connect.php'; // Ensure this file establishes $conn

$message = ""; // To display success or error messages

// --- 1. HANDLE DELETION ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $petowner_id_to_delete = mysqli_real_escape_string($conn, $_POST['delete_id']);
    
    // Start Transaction: Ensures either all deletions happen, or none do.
    mysqli_begin_transaction($conn);

    try {
        // 1. Delete associated Pets (assuming 'pets' table has foreign key 'owner_id' linking to petowners)
        $delete_pets_query = "DELETE FROM petinfo WHERE petowners_ID = '$petowner_id_to_delete'";
        if (!mysqli_query($conn, $delete_pets_query)) {
            throw new Exception("Error deleting associated pets: " . mysqli_error($conn));
        }

        // 2. Delete the Pet Owner record
        $delete_owner_query = "DELETE FROM petowners WHERE petowners_ID = '$petowner_id_to_delete'";
        if (!mysqli_query($conn, $delete_owner_query)) {
            throw new Exception("Error deleting pet owner: " . mysqli_error($conn));
        }

        // If all deletions succeed, commit the transaction
        mysqli_commit($conn);
        // Updated Success Message for a cleaner look next to the dedicated icon
        $message = "✅ Pet Owner (ID: $petowner_id_to_delete) and all associated records deleted successfully. All associated pets were also removed.";

    } catch (Exception $e) {
        // If any error occurs, rollback the transaction
        mysqli_rollback($conn);
        $message = "❌ Deletion failed: " . $e->getMessage();
    }
}

// --- 2. HANDLE FETCH ---
// Fetch ALL records for client-side search.
$query = "SELECT * FROM petowners ORDER BY petowners_name ASC";
$result = mysqli_query($conn, $query);

// Close connection after fetching data
if (isset($conn)) {
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pet Owners Info | Mesra Vet Clinic</title>
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

        /* --- NAVBAR & LOGO --- */
        .navbar {
            background: #0056b3;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar .links a {
            background-color: white; color: #0056b3; padding: 8px 16px; border-radius: 6px;
            text-decoration: none; font-weight: 600; transition: all 0.3s ease;
        }
        .navbar .links a:hover { background-color: #e6efff; }
        .logo-img { width: 250px; height: 55px; object-fit: cover; }

        /* --- CONTAINER & HEADER --- */
        .dashboard-container {
            max-width: 1200px; 
            margin: 80px auto; padding: 40px; background: white; border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08); 
            animation: fadeIn 0.5s ease-in-out;
        }

        h2 {
            color: #0056b3;
            font-size: 24px;
            margin-bottom: 25px; /* Spacing below the title */
            text-align: left;
        }
        
        /* --- SEARCH BOX --- */
        .search-box {
            display: flex;
            justify-content: flex-end; 
            margin-bottom: 15px; /* Spacing above the table */
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

        /* --- TABLE STYLES --- */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; vertical-align: middle; }
        th { background-color: #0056b3; color: white; }
        tr:hover { background-color: #f1f6ff; }
        .no-data { color: #777; font-style: italic; margin-top: 10px; text-align: center; }

        /* --- DELETE BUTTON --- */
        .delete-btn {
            background-color: #dc3545; color: white; border: none; padding: 8px 12px; 
            border-radius: 4px; cursor: pointer; font-size: 14px; transition: background-color 0.3s;
        }
        .delete-btn:hover { background-color: #c82333; }
        
        /* --- TOAST NOTIFICATION STYLES (Modern Replacement for .message) --- */

        .toast {
            position: fixed; 
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 10px;
            font-weight: 600;
            text-align: left;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            opacity: 1;
            transform: translateX(0);
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toast.success {
            background-color: #e6ffed; 
            color: #00703c; 
            border: 1px solid #73e6a0;
        }
        .toast.success .icon {
            font-size: 1.2em;
            color: #00a85a; 
        }

        .toast.error {
            background-color: #ffe6e6; 
            color: #9d0000; 
            border: 1px solid #e67373;
        }
        .toast.error .icon {
            font-size: 1.2em;
            color: #e60000; 
        }

        .toast.hide {
            opacity: 0;
            transform: translateX(100%);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
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

    <?php if (!empty($message)): 
        $class = strpos($message, '✅') !== false ? 'success' : 'error';
        $icon_class = strpos($message, '✅') !== false ? 'fa-check-circle' : 'fa-times-circle';
        // Clean the message by removing the emoji for cleaner text next to the dedicated icon
        $clean_message = str_replace(['✅ ', '❌ '], '', $message);
    ?>
        <div class="toast <?php echo $class; ?>" id="notification-toast">
            <i class="fa-solid <?php echo $icon_class; ?> icon"></i>
            <span><?php echo $clean_message; ?></span>
        </div>
    <?php endif; ?>


    <div class="dashboard-container">
        
        <h2>Pet Owners Information</h2>
        
        <div class="search-box">
            <input 
                type="text" 
                id="searchInput" 
                placeholder="Search by name, phone, or email..."
            >
        </div>


        <?php if (mysqli_num_rows($result) > 0): ?>
            <table id="petOwnerTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Action</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['petowners_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['petowners_phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['petowners_email']); ?></td>
                            <td><?php echo htmlspecialchars($row['petowners_address']); ?></td>
                            <td>
                                <button type="button" class="delete-btn" 
                                    onclick="openDeleteModal(
                                        '<?php echo htmlspecialchars($row['petowners_ID']); ?>',
                                        '<?php echo htmlspecialchars($row['petowners_name']); ?>'
                                    )">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                                
                                <form id="deleteForm_<?php echo htmlspecialchars($row['petowners_ID']); ?>" method="POST" action="petowners_info1.php" style="display: none;">
                                    <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($row['petowners_ID']); ?>">
                                    <input type="hidden" name="petowner_name" value="<?php echo htmlspecialchars($row['petowners_name']); ?>"> 
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No pet owner data found.</p>
        <?php endif; ?>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Client-Side Live Search JavaScript for Pet Owners ---
            const searchInput = document.getElementById("searchInput");
            const table = document.getElementById("petOwnerTable");
            
            if (searchInput && table) {
                searchInput.addEventListener("keyup", function() {
                    const filter = this.value.toLowerCase();
                    const rows = table.querySelectorAll("tbody tr");
                    
                    rows.forEach(row => {
                        const rowText = row.innerText.toLowerCase();
                        row.style.display = rowText.includes(filter) ? "" : "none";
                    });
                });
            }

            // --- Modern Toast Message Hiding Script ---
            const messageElement = document.getElementById('notification-toast');
            if (messageElement) {
                setTimeout(() => {
                    // Start the fade-out/slide-out transition
                    messageElement.classList.add('hide');
                    
                    // Remove the element from the DOM after the transition completes
                    setTimeout(() => messageElement.remove(), 600); 
                }, 5000); // Wait 5 seconds before starting the animation
            }
        });
    </script>
</body>
</html>