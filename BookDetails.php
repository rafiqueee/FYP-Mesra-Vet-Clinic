<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['petowners_ID'])) {
    header("Location: testmainscroll1.html");
    exit;
}

$owner_id = $_SESSION['petowners_ID'];
$selectedPets = $_SESSION['booking_pets'] ?? [];

if (empty($selectedPets)) {
    echo "<script>alert('No pets selected.'); window.location.href='petinfotest.php';</script>";
    exit;
}

// Fetch selected pet info
$placeholders = implode(',', array_fill(0, count($selectedPets), '?'));
$sql = "SELECT pet_id, pet_name FROM petinfo WHERE pet_id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$types = str_repeat('i', count($selectedPets));
$stmt->bind_param($types, ...$selectedPets);
$stmt->execute();
$result = $stmt->get_result();
$selectedPetDetails = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Appointment</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
body { font-family: 'Poppins', sans-serif; background-color: #f5f8ff; margin:0; padding:0; color:#333; }
.navbar { background:#0056b3; color:white; display:flex; justify-content:space-between; align-items:center; padding:15px 40px; box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.navbar .logo-img { width:250px; height:55px; object-fit:cover; }
.appointment-container { max-width:700px; margin:40px auto; background:#fff; padding:30px 40px; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.1); }
h2 { font-size:22px; color:#003d80; margin-bottom:25px; }
.form-group { margin-bottom:18px; }
label { display:block; margin-bottom:6px; font-weight:600; color:#333; }
input[type=text], input[type=date], select, textarea { width:100%; padding:10px 12px; border-radius:8px; border:1px solid #ccc; font-size:14px; transition:all 0.3s ease; }
input:focus, select:focus, textarea:focus { border-color:#0056b3; box-shadow:0 0 5px rgba(0,86,179,0.2); outline:none; }
textarea { resize:vertical; }
.next-btn, .back-btn { padding:12px 20px; border:none; border-radius:8px; cursor:pointer; font-weight:600; font-size:14px; transition:all 0.3s ease; }
.next-btn { background:#0056b3; color:#fff; width:100%; }
.next-btn:hover { background:#007bff; }
.back-btn { background:#eee; color:#333; margin-bottom:20px; }
.notes textarea { min-height:80px; }
@media (max-width:600px) { .appointment-container { padding:20px; margin:20px; } .navbar { flex-direction:column; align-items:flex-start; gap:10px; } }
</style>
</head>
<body>

<div class="navbar">
    <div class="logo" onclick="window.history.back()">
        <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img">
    </div>
</div>

<div class="appointment-container">
  <button class="back-btn" onclick="window.history.back()">BACK</button>
  <h2>Book Your Preferences</h2>

  <form action="process_booking.php" method="POST" class="appointment-form">

    <!-- Selected Pets -->
    <div class="form-group">
      <label>Selected Pets</label>
      <?php foreach ($selectedPetDetails as $pet): ?>
        <div>
          <input type="hidden" name="pet_ids[]" value="<?= $pet['pet_id']; ?>">
          <span><?= htmlspecialchars($pet['pet_name']); ?></span>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Date -->
    <div class="form-group">
      <label for="date">Booking Date</label>
      <input type="date" id="date" name="booking_date" required>
    </div>

    <!-- Time -->
    <div class="form-group">
      <label for="time">Booking Time</label>
      <select id="time" name="booking_time" required>
        <option value="">Select Time</option>
      </select>
    </div>

    <!-- Purpose -->
    <div class="form-group">
      <label for="purpose">Purpose of Visit</label>
      <select id="purpose" name="purpose_of_visit" required>
        <option value="">Select Purpose of Visit</option>
        <option value="Animal Treatment">Animal Treatment</option>
        <option value="Vaccination & Deworming">Vaccination & Deworming</option>
        <option value="Neutering / Spaying">Neutering / Spaying</option>
        <option value="Follow Up Appointment">Follow Up Appointment</option>
        <option value="Others">Others</option>
      </select>
    </div>

    <!-- Price -->
    <div class="form-group">
      <label for="price">Estimated Price (RM)</label>
      <input type="text" id="price" name="estimated_price" readonly>
    </div>

    <!-- Notes -->
    <div class="form-group notes">
      <label for="notes">Booking Notes</label>
      <textarea id="notes" name="booking_notes" rows="4" placeholder="Write notes or special instructions..."></textarea>
    </div>

    <button type="submit" class="next-btn">Confirm Booking</button>
  </form>
</div>

<script>
const dateInput = document.getElementById("date");
const timeSelect = document.getElementById("time");
const purposeSelect = document.getElementById("purpose");
const priceField = document.getElementById("price");

// Date Validation
const today = new Date();
const todayMidnight = new Date(today.getFullYear(), today.getMonth(), today.getDate());
const yyyy = today.getFullYear();
const mm = String(today.getMonth() + 1).padStart(2, "0");
const dd = String(today.getDate()).padStart(2, "0");
const minDate = `${yyyy}-${mm}-${dd}`;
const maxDateObj = new Date();
maxDateObj.setMonth(maxDateObj.getMonth() + 3);
const maxY = maxDateObj.getFullYear();
const maxM = String(maxDateObj.getMonth() + 1).padStart(2, "0");
const maxD = String(maxDateObj.getDate()).padStart(2, "0");
const maxDate = `${maxY}-${maxM}-${maxD}`;
dateInput.setAttribute("min", minDate);
dateInput.setAttribute("max", maxDate);

dateInput.addEventListener("change", function () {
    const selectedDate = new Date(this.value);
    if (selectedDate < todayMidnight) { alert("⚠️ You cannot book a past date!"); this.value = ""; }
    else if (selectedDate > maxDateObj) { alert("⚠️ Bookings can only be made within 3 months from today!"); this.value = ""; }
});

// Time Slots
function formatTime12Hour(hour, minute) {
    const ampm = hour >= 12 ? "PM" : "AM";
    const h = hour % 12 || 12;
    const m = String(minute).padStart(2, "0");
    return `${h}:${m} ${ampm}`;
}

async function fetchBookedTimes(date) {
    if (!date) return [];
    
    // 1. AJAX CALL to your new backend file
    const url = `fetch_booked_slots.php?date=${date}`;
    
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        return data.booked_slots || []; // Expects an array of "HH:mm" strings
    } catch (error) {
        console.error("Error fetching booked slots:", error);
        alert("Could not load available times. Please try again.");
        return [];
    }
}

async function updateAvailableTimes() {
    const selectedDateValue = dateInput.value;
    if (!selectedDateValue) {
        timeSelect.innerHTML = '<option value="">Select Time</option>';
        return;
    }

    const selectedDate = new Date(selectedDateValue);
    const now = new Date();
    timeSelect.innerHTML = '<option value="">Select Time</option>';
    
    // 2. Await the booked times list
    const bookedTimes = await fetchBookedTimes(selectedDateValue);
    
    const startHour = 10, endHour = 19, interval = 15, lunchStart = 14, lunchEnd = 15;
    
    for (let hour=startHour; hour<=endHour; hour++) {
        for (let minute=0; minute<60; minute+=interval) {
            
            // Skip lunch time
            if (hour>=lunchStart && hour<lunchEnd) continue;
            if (hour===endHour && minute>0) break;
            
            const timeOption = new Date(selectedDate);
            timeOption.setHours(hour, minute, 0, 0);

            // Time option value in HH:mm format
            const hh = String(hour).padStart(2,"0");
            const mm = String(minute).padStart(2,"0");
            const timeValue = `${hh}:${mm}`;

            // Check if slot is in the past (existing logic)
            const minAllowed = new Date(now.getTime() + 30*60000);
            if (selectedDate.toDateString()===now.toDateString() && timeOption<=minAllowed) continue;
            
            // 3. CHECK FOR BOOKED SLOT (THE NEW VALIDATION)
            const isBooked = bookedTimes.includes(timeValue);
            
            const option = document.createElement("option");
            option.value = timeValue;
            option.textContent = formatTime12Hour(hour, minute);
            
            if (isBooked) {
                // If booked, mark it as disabled and add a visual indicator
                option.disabled = true;
                option.textContent += " (Booked)";
                option.style.backgroundColor = '#ffdddd'; // Optional: visual cue
            }

            timeSelect.appendChild(option);
        }
    }
}
dateInput.addEventListener("input", updateAvailableTimes);

// Price Auto-fill
const servicePrices = { "Animal Treatment":"80","Vaccination & Deworming":"50","Neutering / Spaying":"200","Follow Up Appointment":"60","Others":"TBD" };
purposeSelect.addEventListener("change",()=>{priceField.value=servicePrices[purposeSelect.value]||"";});
</script>
</body>
</html>


