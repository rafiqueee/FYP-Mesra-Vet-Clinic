<?php 
session_start();
// Include the database connection file
include 'db_connect.php'; 

$today_schedule = [];
$date_today = date('Y-m-d'); // Get today's date in YYYY-MM-DD format

// Ensure connection is successful before querying
if (isset($conn) && $conn !== false) {
    // Query to fetch today's appointments
    // We join the 'bookings' table with the 'petowners' table to get the owner's name.
    // It filters results where the booking_date matches today's date.
    
    $query_schedule = "
        SELECT 
            b.booking_time, 
            p.petowners_name, 
            b.reason_for_visit
        FROM bookings b
        JOIN petowners p ON b.petowners_ID = p.petowners_ID
        WHERE DATE(b.booking_date) = '$date_today'
        ORDER BY b.booking_time ASC
        LIMIT 5
    "; 

    $result_schedule = mysqli_query($conn, $query_schedule);

    if ($result_schedule) {
        while ($row = mysqli_fetch_assoc($result_schedule)) {
            // Format the time display for better readability (e.g., 10:00:00 -> 10:00 AM)
            $formatted_time = date('h:i A', strtotime($row['booking_time']));
            
            $today_schedule[] = [
                'time' => $formatted_time,
                'client' => htmlspecialchars($row['petowners_name']),
                'reason' => htmlspecialchars($row['reason_for_visit']),
            ];
        }
        mysqli_free_result($result_schedule);
    } else {
        // Handle database query error (optional, for debugging)
        // echo "Error fetching schedule: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard | Mesra Vet Clinic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f5f8ff; 
            margin: 0;
            padding: 0;
            color: #333;
            min-height: 100vh;
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

        .logo-img {
            width: 250px;
            height: 55px;
            margin-right: 15px;
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

        /* --- DASHBOARD STRUCTURAL STYLES --- */
        .dashboard-container {
            margin: 40px auto;
            padding: 20px;
            max-width: 1800px; 
            background: transparent;
            box-shadow: none;
            text-align: left;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .dashboard-container h2 {
            color: #101011ff;
            font-size: 26px;
            margin-bottom: 10px;
        }

        .main-content-wrapper {
            display: flex;
            gap: 30px; 
            padding-top: 20px;
        }

        .sidebar-section {
            flex: 0 0 350px; 
            padding: 0;
        }

        .main-section {
            flex-grow: 1; 
            min-width: 0; 
        }

        /* CARD STYLING */
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            transition: all 0.2s ease;
        }
        
        .card h3 {
            color: #0056b3; 
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 2px solid #e6f0ff; 
            padding-bottom: 8px;
            font-weight: 600;
        }
        
        /* --- DAILY SCHEDULE SPECIFIC STYLES --- */
        .schedule-item {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px dashed #eee;
            margin-bottom: 5px;
        }
        .schedule-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .schedule-time {
            font-weight: 700;
            color: #007bff;
            flex: 0 0 90px;
            font-size: 15px;
        }
        .schedule-details {
            flex-grow: 1;
            font-size: 14px;
            line-height: 1.3;
        }
        .schedule-details span {
            display: block;
            color: #555;
            font-weight: 600; /* Added bolding for client name */
        }
        .schedule-details small {
            color: #999;
            font-style: italic;
        }


        /* --- STAFF QUICK TOOLS --- */
        .dashboard-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr); 
            gap: 70px; 
            margin-bottom: 10px;
        }

        .dashboard-card {
            background-color: #f9fbff;
            border-radius: 10px; 
            padding: 20px; 
            width: 80%; 
            height: 100%; 
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.04); 
            text-align: center; 
            display: flex; 
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .dashboard-card:hover {
            background-color: #eaf1ff;
            transform: translateY(-3px); 
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.08);
        }

        .dashboard-card i {
            font-size: 36px; 
            color: #0056b3;
            margin-bottom: 8px;
        }

        .dashboard-card h4 {
            color: #003d80;
            font-size: 15px; 
            margin-top: 5px;
            font-weight: 600;
        }
        
        /* FOOTER & ANIMATION */
        .footer {
            text-align: center;
            padding: 20px;
            color: #888;
            font-size: 14px;
            margin-top: 60px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* RESPONSIVE */
        @media (max-width: 1100px) {
            .dashboard-buttons {
                grid-template-columns: repeat(2, 1fr); 
            }
        }
        
        @media (max-width: 768px) {
            .main-content-wrapper {
                flex-direction: column; 
                gap: 20px;
            }

            .sidebar-section {
                flex: 0 0 auto; 
            }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">
            <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img"> 
        </div>
        <div class="links">
            <a href="testmainscroll1.html"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <div class="dashboard-container">
        
        <h2>Welcome, <?php echo isset($_SESSION['staff_name']) ? htmlspecialchars($_SESSION['staff_name']) : 'Staff'; ?> </h2>
        <p>Access daily schedules, client records, staff details, and reports all in one place.</p>

        <div class="main-content-wrapper">

            <div class="sidebar-section">

                <div class="card daily-schedule-anchor-card">
                    <h3><i class="fa-solid fa-calendar-day"></i> Today's Appointments</h3>
                    
                    <?php if (!empty($today_schedule)): ?>
                        <?php foreach ($today_schedule as $item): ?>
                            <div class="schedule-item">
                                <div class="schedule-time"><?php echo $item['time']; ?></div>
                                <div class="schedule-details">
                                    <span><?php echo $item['client']; ?></span>
                                    <small>Reason: <?php echo $item['reason']; ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div style="margin-top: 15px;">
                            <a href="daily_schedule.php" style="background: #007bff; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 14px; display: inline-block;">
                                <i class="fa-solid fa-arrow-right"></i> View Full Schedule
                            </a>
                        </div>
                    <?php else: ?>
                        <p style="color: #999; text-align: center; margin-top: 10px;">
                            No appointments scheduled for <?php echo date('F j, Y'); ?>.
                        </p>
                        <a href="daily_schedule.php" style="background: #007bff; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 14px; display: inline-block;">
                            <i class="fa-solid fa-arrow-right"></i> Check Calendar
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="card profile-card">
                    <h3><i class="fa-solid fa-user-tie"></i> Staff Account</h3>
                    <p style="margin-bottom: 5px; color:#555;">Name: <?php echo isset($_SESSION['staff_name']) ? htmlspecialchars($_SESSION['staff_name']) : 'N/A'; ?></p>
                    <p style="margin-bottom: 5px; color:#555;">Role: <?php echo isset($_SESSION['staff_position']) ? htmlspecialchars($_SESSION['staff_position']) : 'N/A'; ?></p>
                    <p style="margin-bottom: 15px; color:#555;">ID: S-<?php echo isset($_SESSION['staff_id']) ? htmlspecialchars($_SESSION['staff_id']) : '000'; ?></p>
                    
                    <button style="background: #0056b3; color: white; border: none; padding: 10px; border-radius: 6px; width: 100%; cursor: pointer; font-family: Poppins, sans-serif;"
                            onclick="window.location.href='update_staff_profile.php'">
                            <i class="fa-solid fa-pen"></i> Update Profile
                    </button>
                </div>

                <div class="card quick-schedule-card">
                    <h3><i class="fa-solid fa-clock"></i> Quick Access</h3>
                    <ul style="list-style: none; padding: 0; text-align: left;">
                        <li style="margin-bottom: 10px;"><a href="petowners_info3.php"><i class="fa-solid fa-users" style="margin-right: 8px;"></i> Manage Client Records</a></li>
                        <li style="margin-bottom: 10px;"><a href="generate_report2.php"><i class="fa-solid fa-chart-line" style="margin-right: 8px;"></i> View Reports</a></li>
                    </ul>
                </div>

            </div>
            
            <div class="main-section">
                
                <div class="card staff-tools-card">
                    <h3><i class="fa-solid fa-tools"></i> Staff Quick Tools</h3>
                    
                    <div class="dashboard-buttons">
        
                        <div class="dashboard-card" onclick="window.location.href='booking_information.php'">
                            <i class="fa-solid fa-circle-info"></i>
                            <h4>Booking Information</h4>
                        </div>
        
                        <div class="dashboard-card" onclick="window.location.href='petowners_info3.php'">
                            <i class="fa-solid fa-user"></i>
                            <h4>Pet Owners</h4>
                        </div>

                        <div class="dashboard-card" onclick="window.location.href='staff_info1.php'">
                            <i class="fa-solid fa-user-gear"></i>
                            <h4>Staff Information</h4>
                        </div>

                        <div class="dashboard-card" onclick="window.location.href='generate_report1.php'">
                            <i class="fa-solid fa-chart-line"></i>
                            <h4>Reports</h4>
                        </div>

                        <?php if (isset($_SESSION['staff_position']) && $_SESSION['staff_position'] === 'Manager'): ?>
                            <div class="dashboard-card" onclick="window.location.href='add_staff.php'">
                                <i class="fa-solid fa-user-plus"></i>
                                <h4>Add Staff Member</h4>
                            </div>
                        <?php endif; ?>

                        <div class="dashboard-card" style="visibility: hidden; box-shadow: none; background: transparent; cursor: default;"></div>
                    </div>
                </div>

                <div class="card announcements-card">
                    <h3><i class="fa-solid fa-bullhorn"></i> Clinic Announcements</h3>
                    <p style="font-size: 14px; color: #777;">[10/11/2025] Remember to check the vaccine stock before the afternoon shift. All new client records must be verified by 5 PM.</p>
                </div>
            </div>

        </div> 
    </div> 
    
    <div class="footer">
        &copy; <?php echo date("Y"); ?> Mesra Vet Clinic. All rights reserved.
    </div>

</body>
</html>