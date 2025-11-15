<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['petowners_ID'])) {
    header("Location: process_login.php");
    exit();
}

$petowners_ID = $_SESSION['petowners_ID'];
$query = "SELECT * FROM booking WHERE petowners_ID = '$petowners_ID' ORDER BY booking_date DESC, booking_time DESC";
$result = mysqli_query($conn, $query);

// Initialize appointments array
$appointments = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }

    // Sort by latest first if there are any
    if (count($appointments) > 0) {
        usort($appointments, function($a, $b){
            return strtotime($b['booking_date'].' '.$b['booking_time']) - strtotime($a['booking_date'].' '.$a['booking_time']);
        });
    }
}

$queryPets = "SELECT * FROM petinfo WHERE petowners_ID = '$petowners_ID'";
$resultPets = mysqli_query($conn, $queryPets);
$pets = [];
if ($resultPets) {
    while ($row = mysqli_fetch_assoc($resultPets)) {
        $pets[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard | Mesra Vet Clinic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Global and Reset */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f5f8ff; margin: 0; padding: 0; color: #333; min-height: 100vh; }
        * { box-sizing: border-box; } /* Excellent practice for layout consistency */

        /* --- Navbar (Header) Styles --- */
        .navbar {
			position: fixed;
			top: 0;
			width: 100%;
			background-color: #0056b3;
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 15px 40px;
			z-index: 1000;
			box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
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

        /* --- Dashboard Layout --- */
        .dashboard-container { width: 90%; max-width: 1400px; margin: 180px auto; padding: 0 20px; box-sizing: border-box; }
        .welcome-header h1 { color: #333; font-size: 28px; font-weight: 600; margin-bottom: 5px; }
        .welcome-header p { color: #777; font-size: 15px; margin-bottom: 25px; }

        .main-content-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .pet-section { grid-column: 1 / -1; } /* Registered Pets spans both columns */

        /* --- Card Styling --- */
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            padding: 30px;
        }
        .card h3 { color: #0056b3; font-size: 20px; margin: 0 0 15px 0; font-weight: 600; }

        /* --- Appointment Specific Styles --- */
        .appointment-list { display: flex; flex-direction: column; gap: 20px; }
        .appointment-item { display: flex; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #eee; }
        .appointment-item:last-child { border-bottom: none; }

        .time-icon { color: #0056b3; margin-right: 15px; font-size: 16px; position: relative; top: 2px; }
        .appointment-details { flex-grow: 1; }
        .appointment-time { font-weight: 600; color: #333; margin-bottom: 2px; display: block; }
        .appointment-purpose { color: #555; font-size: 15px; display: block; }
        .appointment-date { color: #777; font-size: 14px; margin-bottom: 5px; display: block; }

        .status-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            text-transform: capitalize;
            align-self: flex-start;
            margin-left: auto;
        }
        .status-confirmed { background-color: #e6ffed; color: #00873c; border: 1px solid #c8f5d8; } /* Light green */
        .status-pending { background-color: #fff8e6; color: #cc8400; border: 1px solid #ffe8cc; } /* Light orange/yellow */

        /* Scrollable Appointment Section (My Appointments) */
        .scrollable-appointments { max-height: 350px; overflow-y: auto; padding-right: 15px; } /* Space for scrollbar */
        /* Custom Scrollbar Styling to match the image */
        .scrollable-appointments::-webkit-scrollbar { width: 8px; }
        .scrollable-appointments::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
        .scrollable-appointments::-webkit-scrollbar-track { background: #f5f5f5; }

        /* --- Registered Pets Specific Styles --- */
        .pet-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .pet-list-item:last-child { border-bottom: none; }
        .pet-details-name { font-weight: 600; color: #333; font-size: 16px; margin-bottom: 2px; }
        .pet-details-info { color: #777; font-size: 14px; }

        /* Dropdown/Actions for Pets */
        .dropdown { position: relative; display: inline-block; }
        .dropbtn { background: none; border: none; font-size: 20px; cursor: pointer; color: #777; padding: 0 5px; }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 140px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            right: 0;
            border-radius: 6px;
        }
        .dropdown-content a {
            color: #333;
            padding: 10px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .dropdown-content a:hover { background-color: #e6f0ff; color: #0056b3; }
        .dropdown-content a i { margin-right: 8px; }

        /* Book Appointment Button */
        .book-btn {
            display: inline-flex; align-items: center; gap: 8px;
            background-color: #0056b3; color: white; padding: 10px 20px; border-radius: 6px;
            font-size: 16px; font-weight: 500; text-decoration: none; margin-bottom: 30px;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 86, 179, 0.3);
        }
        .book-btn:hover { background-color: #004494; }

        /* Responsive Layout */
        @media(max-width: 992px) {
            .main-content-grid { grid-template-columns: 1fr; }
            .dashboard-container { margin: 20px auto; padding: 0 15px; }
            .navbar { padding: 15px 20px; }
        }

        .search-container {
            margin-bottom: 20px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 10px 15px 10px 40px; /* Adjust padding for icon */
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .search-input:focus {
            border-color: #0056b3;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        /* ⭐ Specific style for the profile button to make it look like an icon-only button */
        .navbar .links .profile-btn {
            background-color: white; /* Make it transparent */
            padding: 8px 12px; /* Adjusted padding for better icon appearance */
        }
        .navbar .links .profile-btn:hover {
            background-color: rgba(255, 255, 255, 0.2); /* Light background on hover */
        }
        
        /* --- START MODERN MODAL STYLES (New) --- */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 10000; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6); /* Black w/ opacity */
            backdrop-filter: blur(2px);
            animation: fadein-overlay 0.3s;
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 30px;
            border-radius: 15px;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            border-top: 5px solid #ff4d4d; /* Red warning line */
            animation: slidein 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .modal-content h3 {
            color: #ff4d4d;
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .modal-content p {
            color: #555;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .modal-footer {
            display: flex;
            justify-content: space-around;
            gap: 10px;
        }

        .modal-btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            flex: 1;
            transition: background-color 0.3s;
            border: none;
        }

        .btn-confirm {
            background-color: #ff4d4d;
            color: white;
        }

        .btn-confirm:hover {
            background-color: #cc0000;
        }

        .btn-cancel {
            background-color: #ddd;
            color: #333;
        }

        .btn-cancel:hover {
            background-color: #ccc;
        }

        @keyframes slidein {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadein-overlay {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        /* --- END MODERN MODAL STYLES --- */

    </style>
</head>
<body>

    <div class="navbar">
    <div class="logo" onclick="goBack()">
        <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img">
    </div>
    <div class="links">
    <a href="update_profile.php" class="profile-btn" title="Update Profile"><i class="fa-solid fa-user-pen"></i></a>
    <a href="testmainscroll1.html"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>
</div>

    <div class="dashboard-container">
        <div class="welcome-header">
            <h1>Welcome, <?php echo isset($_SESSION['petowners_name']) ? htmlspecialchars($_SESSION['petowners_name']) : 'Pet Owner'; ?></h1>
            <p>Manage your pets, appointments, and personal profile everything you need is right here.</p>
        </div>

        <a href="petinfotest.php" class="book-btn"><i class="fa-solid fa-calendar-plus"></i> Book Appointment</a>

        <div class="main-content-grid">

            <div class="card today-appointments">
                <h3>Today's Appointments</h3>
                <div class="appointment-list">
                    <?php
                    $today = date('Y-m-d');
                    $hasTodayAppointments = false;
                    foreach($appointments as $row){
                        if($row['booking_date'] == $today){
                            $hasTodayAppointments = true;
                            $time = date('h:i A', strtotime($row['booking_time']));
                            $purpose = htmlspecialchars($row['purpose_of_visit']);
                            $status = strtolower($row['booking_status']);
                            $statusClass = ($status == 'confirmed') ? 'status-confirmed' : 'status-pending';

                            echo "<div class='appointment-item'>
                                <i class='fa-solid fa-clock time-icon'></i>
                                <div class='appointment-details'>
                                    <span class='appointment-time'>{$time}</span>
                                    <span class='appointment-purpose'>{$purpose}</span>
                                </div>
                                <span class='status-badge {$statusClass}'>{$status}</span>
                            </div>";
                        }
                    }
                    if (!$hasTodayAppointments) {
                        echo "<p style='color:#777; text-align:center; font-style:italic;'>No appointments scheduled for today.</p>";
                    }
                    ?>
                </div>
            </div>

            <div class="card my-appointments">
                <h3>My Appointments</h3>
                <div class="scrollable-appointments appointment-list">
                    <?php
                    if(!empty($appointments)):
                        foreach($appointments as $row):
                            $date = date("d M Y", strtotime($row['booking_date']));
                            $time = date("h:i A", strtotime($row['booking_time']));
                            $purpose = htmlspecialchars($row['purpose_of_visit']);
                            $status = strtolower($row['booking_status']);
                            $statusClass = ($status == 'confirmed') ? 'status-confirmed' : 'status-pending';
                    ?>
                    <div class='appointment-item'>
                        <i class='fa-solid fa-clock time-icon'></i>
                        <div class='appointment-details'>
                            <span class='appointment-date'><?php echo $date; ?></span>
                            <span class='appointment-time'><?php echo $time; ?></span>
                            <span class='appointment-purpose'><?php echo $purpose; ?></span>
                        </div>
                        <span class='status-badge <?php echo $statusClass; ?>'><?php echo $status; ?></span>
                    </div>
                    <?php endforeach; else: ?>
                    <p style='color:#777; text-align:center; font-style:italic;'>You have no scheduled appointments.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card pet-section">
                <h3 style="color: #0056b3;"><i class="fa-solid fa-paw" style="margin-right: 10px;"></i> Registered Pets</h3>
                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="petSearchInput" onkeyup="filterPets()" placeholder="Search by name or breed..." class="search-input">
                </div>
                <div class="pet-list" id="petListContainer">
                    <?php if(count($pets) > 0):
                        foreach($pets as $pet): ?>
                        <div class="pet-list-item">
                            <div class="pet-details">
                                <div class="pet-details-name"><?php echo htmlspecialchars($pet['pet_name']); ?></div>
                                <div class="pet-details-info">
                                    <?php echo htmlspecialchars($pet['pet_breed']); ?> &middot;
                                    <?php echo htmlspecialchars($pet['pet_age']); ?> years &middot;
                                    <?php echo htmlspecialchars($pet['pet_gender']); ?>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="dropbtn" onclick="toggleDropdown(this)">⋮</button>
                                <div class="dropdown-content">
                                    <a href="update_petinfo.php?edit=<?php echo $pet['pet_ID']; ?>">
                                        <i class="fa-solid fa-pen-to-square"></i> Update
                                    </a>
                                    <!-- ⭐ UPDATED: Call the new showDeleteModal function -->
                                    <a href="#" onclick="showDeleteModal(<?php echo $pet['pet_ID']; ?>); return false;">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <p style="text-align:center; color:#777; font-style:italic;">No pets registered yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ⭐ START MODERN DELETION MODAL STRUCTURE (New) -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3><i class="fa-solid fa-trash-can"></i> Confirm Deletion</h3>
            <p>You are about to permanently delete this pet's record. This action cannot be undone.</p>
            <p style="font-weight: 600;">Are you sure you want to proceed?</p>
            <div class="modal-footer">
                <button class="modal-btn btn-cancel" onclick="hideDeleteModal()">Cancel</button>
                <button class="modal-btn btn-confirm" id="modalConfirmBtn">Yes, Delete Pet</button>
            </div>
        </div>
    </div>
    <!-- ⭐ END MODERN DELETION MODAL STRUCTURE -->

    <script>
    // Store the pet ID temporarily for deletion
    let currentPetID = null;

    // ⭐ NEW: Function to show the modern modal
    function showDeleteModal(petID) {
        currentPetID = petID;
        document.getElementById('deleteModal').style.display = 'block';
    }

    // ⭐ NEW: Function to hide the modern modal
    function hideDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        currentPetID = null;
    }

    // ⭐ NEW: Event listener for the modal's confirm button
    const modalConfirmBtn = document.getElementById('modalConfirmBtn');
    if (modalConfirmBtn) {
        modalConfirmBtn.addEventListener('click', function() {
            if (currentPetID !== null) {
                // Redirects to the PHP script that processes the final deletion
                // This will use the pet_ID and then redirect back to this dashboard.
                window.location.href = 'delete_petinfo2.php?pet_ID=' + currentPetID; 
            }
            hideDeleteModal();
        });
    }

    // Close dropdown if clicked outside and close modal if clicked on overlay
    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn')) {
            const dropdowns = document.querySelectorAll('.dropdown-content');
            dropdowns.forEach(dc => dc.style.display = 'none');
        }
        
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            hideDeleteModal();
        }
    }

    // Existing functions (renamed the old confirmDelete)
    function toggleDropdown(button) {
        const dropdownContent = button.nextElementSibling;
        const isCurrentlyOpen = dropdownContent.style.display === 'block';
        const allDropdowns = document.querySelectorAll('.dropdown-content');

        // Close all other dropdowns
        allDropdowns.forEach(dc => {
            dc.style.display = 'none';
        });

        // Toggle current dropdown
        if (!isCurrentlyOpen) {
            dropdownContent.style.display = 'block';
        }
    }

    function filterPets() {
        // 1. Get the search input value and convert to uppercase
        let input = document.getElementById('petSearchInput');
        let filter = input.value.toUpperCase();

        // 2. Get the list of all pet items
        let listContainer = document.getElementById('petListContainer');
        
        // Safety check: if container doesn't exist, exit.
        if (!listContainer) return; 

        let items = listContainer.getElementsByClassName('pet-list-item');
        
        // 3. Loop through all list items
        for (let i = 0; i < items.length; i++) {
            // Find the specific elements containing the Name and Info within the current item
            let nameElement = items[i].querySelector('.pet-details-name');
            let infoElement = items[i].querySelector('.pet-details-info');
            
            let combinedText = "";

            // Safely extract text content
            if (nameElement) {
                combinedText += nameElement.textContent || nameElement.innerText;
            }
            if (infoElement) {
                // Add a space to separate name and info text for better searching
                combinedText += " " + (infoElement.textContent || infoElement.innerText);
            }
            
            // Convert the combined text to uppercase for comparison
            let textToCompare = combinedText.toUpperCase();
            
            // Check if the search term (filter) is found anywhere in the combined text
            if (textToCompare.indexOf(filter) > -1) {
                // Use 'flex' here to ensure item reappears correctly based on your CSS
                items[i].style.display = "flex"; 
            } else {
                items[i].style.display = "none"; // Hide the item
            }
        }
    }

    // Function to handle clicking the logo (using 'goBack' placeholder from original)
    function goBack() {
        // Since this is the dashboard, clicking the logo can just reload the page or do nothing.
        // window.location.reload(); 
    }
    </script>
</body>
</html>