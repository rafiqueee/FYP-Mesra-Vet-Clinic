<?php 
session_start();
// Include the database connection file
include 'db_connect.php'; 

$today_schedule = [];
$date_today = date('Y-m-d'); // Get today's date in YYYY-MM-DD format

// Ensure connection is successful before querying
if (isset($conn) && $conn !== false) {
    // Query to fetch today's appointments
    $query_schedule = "
        SELECT 
            b.booking_time, 
            p.petowners_name, 
            b.reason_for_visit
        FROM bookings b
        JOIN petowners p ON b.petowners_ID = p.petowners_ID
        WHERE b.booking_date = '$date_today'
        ORDER BY b.booking_time ASC
        LIMIT 5
    ";

    $result_schedule = mysqli_query($conn, $query_schedule);

    if ($result_schedule) {
        while ($row = mysqli_fetch_assoc($result_schedule)) {
            $formatted_time = date('h:i A', strtotime($row['booking_time']));
            
            $today_schedule[] = [
                'time' => $formatted_time,
                'client' => htmlspecialchars($row['petowners_name']),
                'reason' => htmlspecialchars($row['reason_for_visit']),
            ];
        }
        mysqli_free_result($result_schedule);
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
                                <div>
                                    <span><?php echo $item['client']; ?></span>
                                    <small>Reason: <?php echo $item['reason']; ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #999; text-align: center; margin-top: 10px;">
                            No appointments scheduled for <?php echo date('F j, Y'); ?>.
                        </p>
                    <?php endif; ?>
                </div>
                
                <div class="card profile-card">
                    <h3><i class="fa-solid fa-user-tie"></i> Staff Account</h3>
                    <p>Name: <?php echo $_SESSION['staff_name']; ?></p>
                    <p>Role: <?php echo $_SESSION['staff_role']; ?></p>
                    <p>ID: S-<?php echo $_SESSION['staff_id']; ?></p>
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

                        <!-- ⭐ MANAGER ONLY BUTTON ADDED HERE ⭐ -->
                        <?php if (isset($_SESSION['staff_position']) && $_SESSION['staff_position'] === 'Manager'): ?>
                            <div class="dashboard-card" onclick="window.location.href='add_staff.php'">
                                <i class="fa-solid fa-user-plus"></i>
                                <h4>Add Staff Member</h4>
                            </div>
                        <?php endif; ?>

                        <div class="dashboard-card" style="visibility: hidden; background: transparent; box-shadow:none;"></div>

                    </div>
                </div>

                <div class="card announcements-card">
                    <h3><i class="fa-solid fa-bullhorn"></i> Clinic Announcements</h3>
                    <p style="font-size: 14px; color: #777;">[10/11/2025] Remember to check the vaccine stock before the afternoon shift.</p>
                </div>
            </div>

        </div> 
    </div> 
    
    <div class="footer" style="text-align:center; padding:20px; color:#888;">
        &copy; <?php echo date("Y"); ?> Mesra Vet Clinic. All rights reserved.
    </div>

</body>
</html>
