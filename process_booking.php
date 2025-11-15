<?php 
session_start();
// Use include or require for consistency, and avoid hardcoding credentials directly here if possible
$conn = new mysqli("localhost", "root", "", "fyp2_pet_clinic");
if ($conn->connect_error) {
    // Log the error instead of just dying in a production environment
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- 1. Input Validation and Sanitization ---
    $petowners_ID = $_SESSION['petowners_ID'] ?? null;
    $pet_ids = $_POST['pet_ids'] ?? [];
    $booking_date = $_POST['booking_date'] ?? '';
    $booking_time = $_POST['booking_time'] ?? '';
    $purpose_of_visit = $_POST['purpose_of_visit'] ?? '';
    $booking_notes = $_POST['booking_notes'] ?? '';
    $booking_status = "CONFIRMED";
    
    // Basic null/empty checks
    if (!$petowners_ID || empty($pet_ids) || empty($booking_date) || empty($booking_time)) {
        echo "<script>alert('Error: Required booking information is missing.'); window.location.href='BookDetails.php';</script>";
        exit();
    }
    
    // Check if multiple pets were selected but a single slot is booked.
    // This logic assumes ALL pets in a single booking request share the same time slot.
    // If you need separate slots for each pet, the entire process must change.
    if (count($pet_ids) > 1) {
        // You might want to prevent booking multiple pets in one time slot unless you account for time extension.
        // For simplicity, let's allow it, but ensure only one pet is counted per time slot check.
        // The check below handles the crucial logic.
    }


    // --- 2. CRITICAL TIME SLOT VALIDATION (The Solution to Double-Booking) ---
    // Check for ANY existing booking at this date/time.
    // This prevents the clinic from double-booking.
    $check_slot_sql = "SELECT COUNT(*) AS count FROM booking WHERE booking_date = ? AND booking_time = ?";
    $check_slot = $conn->prepare($check_slot_sql);
    $check_slot->bind_param("ss", $booking_date, $booking_time);
    $check_slot->execute();
    $slot_result = $check_slot->get_result();
    $slot_data = $slot_result->fetch_assoc();
    $check_slot->close();

    if ($slot_data['count'] > 0) {
        // Double-booking detected! Immediately reject the request.
        echo "<script>alert('❌ This time slot is already taken. Please choose another time.'); window.location.href='BookDetails.php';</script>";
        exit();
    }
    
    // --- 3. Transaction Handling (Optional but Recommended) ---
    // Use transactions to ensure all related inserts succeed or fail together.
    $conn->begin_transaction();
    $success = true;

    // --- 4. Insert Bookings ---
    $sql = $conn->prepare("INSERT INTO booking (petowners_ID, pet_id, booking_date, booking_time, purpose_of_visit, booking_notes, booking_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($pet_ids as $pet_id) {
        // You only need to prepare the statement once outside the loop for efficiency
        $pet_id = (int) $pet_id; // Ensure pet_id is an integer for security and type matching
        $sql->bind_param("iisssss", $petowners_ID, $pet_id, $booking_date, $booking_time, $purpose_of_visit, $booking_notes, $booking_status);
        
        if (!$sql->execute()) {
            $success = false;
            // Optionally log the error here: error_log("Failed to insert booking for Pet ID: " . $pet_id);
            break; // Stop on the first failure
        }
    }
    
    $sql->close();

    if ($success) {
        $conn->commit();
        // --- 5. Success Display (Rest of your existing success block) ---
        
        // Prepare booking details string
        $pet_count = count($pet_ids);
        $booking_info = "Date: " . htmlspecialchars($booking_date) . ", Time: " . htmlspecialchars($booking_time) . ", Purpose: " . htmlspecialchars($purpose_of_visit) . " (For $pet_count pet(s))";

        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
          <meta charset='UTF-8'>
          <title>Booking Success</title>
          <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css'>
          <style>/* ... your existing CSS ... */
          @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
            body { font-family: 'Poppins', sans-serif; background-color: #f5f8ff; margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; }
            .popup-box { padding: 40px 50px; background-color: #ffffff; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 86, 179, 0.15); text-align: center; max-width: 420px; animation: fadeIn 0.5s ease-in-out; border: 1px solid #e0e0e0; }
            .success-icon { color: #0056b3; font-size: 50px; margin-bottom: 15px; }
            .popup-box h2 { font-size: 24px; color: #0056b3; font-weight: 700; margin: 0 0 10px 0; }
            .popup-box .info-label { display: block; color: #555; font-size: 14px; margin-top: 15px; margin-bottom: 5px; font-weight: 600; text-transform: uppercase; }
            .popup-box .info-details { display: block; color: #333; font-size: 15px; margin-bottom: 20px; }
            .popup-box button { background-color: #0056b3; color: white; border: none; padding: 10px 25px; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 600; transition: background-color 0.3s ease, transform 0.1s ease; box-shadow: 0 4px 8px rgba(0, 86, 179, 0.2); }
            .popup-box button:hover { background-color: #004494; transform: translateY(-1px); }
            @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
          </style>
        </head>
        <body>
            <div class='popup-container'>
                <div class='popup-box'>
                    <i class='fa-solid fa-calendar-check success-icon'></i>
                    <h2>Booking Successful!</h2>
                    <span class='info-label'>Booking Details</span>
                    <span class='info-details'>$booking_info</span>
                    <p style='color: #777; font-size: 13px; margin-top: 5px;'>Redirecting in 3 seconds...</p>
                    <button onclick=\"window.location.href='petowners_dashboard2.php'\">Go to Dashboard Now</button>
                </div>
            </div>
            <script>
                setTimeout(function() { window.location.href = 'petowners_dashboard2.php'; }, 3000);
            </script>
        </body>
        </html>
        ";
        
        // Remove the successful booking from session if necessary
        unset($_SESSION['booking_pets']); 

    } else {
        $conn->rollback();
        // Handle case where commit failed (e.g., due to database error)
        echo "<script>alert('⚠️ System Error: Failed to save all bookings. Transaction rolled back.'); window.location.href='BookDetails.php';</script>";
        exit();
    }
}

$conn->close();
?>
