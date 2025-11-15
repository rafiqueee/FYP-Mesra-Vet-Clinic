<?php
// Note: All non-standard spaces (like the wide spaces you had) have been cleaned.
$conn = new mysqli("localhost", "root", "", "fyp2_pet_clinic");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$message = ""; // store message for display later
$registration_successful = false; // Flag to control success auto-redirect
$redirect_to_signup = false; // NEW: Flag to control error auto-redirect to signup
$errors = []; // Array to collect validation errors

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and collect input
    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"]; 
    $phone    = trim($_POST["phone"]);
    $address  = trim($_POST["address"]);

    // --- START VALIDATION CHECKS ---

    // 1. Full Name: Must not contain numbers
    if (!preg_match("/^[a-zA-Z\s.-]+$/", $name)) {
        $errors[] = "❌ Full Name can only contain letters, spaces, dots, or hyphens.";
    }
    
    // 2. Email: Must be a valid format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "❌ Email format is invalid.";
    }

    // 3. Password: Combination of character and numbers, limit to 12 characters (min 6, max 12)
    if (strlen($password) < 6 || strlen($password) > 12) {
        $errors[] = "❌ Password must be between 6 and 12 characters long.";
    }
    // Requires at least one letter and one number
    if (!preg_match("/^(?=.*[a-zA-Z])(?=.*\d).+$/", $password)) {
        $errors[] = "❌ Password must contain a combination of letters and numbers.";
    }

    // 4. Phone Number: Must start with 0, must be numeric, min 10 max 11 digits (typical Malaysian format)
    if (!preg_match("/^0\d{9,10}$/", $phone)) {
        $errors[] = "❌ Phone Number must start with '0' and contain 10 to 11 digits.";
    }

    // 5. Address: Needs to be a substantial, descriptive address (min length check used as a proxy)
    if (strlen($address) < 20) {
        $errors[] = "❌ Address is too short. Please include house number, street, city, and state.";
    }

    // --- END VALIDATION CHECKS ---
    
    if (count($errors) > 0) {
        // If validation fails, compile errors and set flag to redirect to signup
        $message = implode("<br>", $errors);
        $redirect_to_signup = true; 

    } else {
        // Only proceed if validation passes

        // 6. Check duplicate email (now that email format is guaranteed)
        $check = $conn->query("SELECT * FROM petowners WHERE petowners_email='$email'");
        if ($check->num_rows > 0) {
            // DUPLICATE EMAIL FOUND: Set error message and redirect flag
            $message = "❌ Email **" . htmlspecialchars($email) . "** is already registered! Redirecting back to the registration form.";
            $redirect_to_signup = true; 
        } else {
            
            
            // Use prepared statements for security (highly recommended)
            $stmt = $conn->prepare("INSERT INTO petowners (petowners_name, petowners_email, petowners_password, petowners_phone, petowners_address) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $password, $phone, $address); // Used $hashed_password here

            if ($stmt->execute()) {
                // Set the message and flag for SUCCESS
                $message = "✅ Registration successful! Redirecting to the Login Form shortly.";
                $registration_successful = true; // Set flag for success redirect
            } else {
                $message = "❌ Error during registration: " . $conn->error;
                // Technical error, no redirect, user stays on this page to read the error
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Result | Mesra Vet Clinic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* [ ... Existing CSS styles are here ... ] */
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f8f9ff;
            margin: 0;
            padding: 0;
            text-align: center;
        }
    
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
            color: white;
        }
    
        .navbar .logo {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 1px;
            cursor: pointer;
        }
    
        .logo-img {
            width: 250px;
            height: 55px;
            margin-right: 15px;
            border-radius: 0;
            object-fit: cover;
        }
    
        .navbar .links a {
            color: white;
            text-decoration: none;
            margin-left: 30px;
            font-weight: 500;
            transition: color 0.3s;
        }
    
        .navbar .links a:hover {
            color: #ffe066;
        }
    
        .popup-container {
            margin-top: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    
        .popup-box {
            background-color: white;
            padding: 40px 60px;
            border-radius: 15px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: fadeIn 0.4s ease;
        }
    
        .popup-box h2 {
            color: #003d80;
            margin-bottom: 25px;
            font-size: 22px;
        }
        
        .success-message {
            color: #00873c; /* Green color for success */
            font-weight: 600;
            display: block;
            margin-bottom: 15px;
        }
        
        .error-message {
            color: #cc0000; /* Red color for error */
            font-weight: 600;
            display: block;
            margin-bottom: 15px;
        }

        .popup-box button {
            background-color: #0056b3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            margin: 8px;
            transition: all 0.3s ease;
        }
    
        .popup-box button:hover {
            background-color: #004090;
            transform: scale(1.05);
        }
    
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* [ ... End of Existing CSS styles ] */
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo" onclick="goBack()">
            <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img">
        </div>
    </div>

    <div class="popup-container">
        <div class="popup-box">
            
            <?php if ($registration_successful): ?>
                <h2 class="success-message"><?php echo $message; ?></h2>
            <?php else: ?>
                <h2 class="error-message"><?php echo $message; ?></h2>
            <?php endif; ?>

        </div>
    </div>
    
    <script>
        // Simple goBack function for the logo
        function goBack() {
            window.history.back();
        }

        // Check if we need to redirect on SUCCESS (to Login)
        <?php if ($registration_successful): ?>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Registration successful. Redirecting to login in 2 seconds...");
            setTimeout(function() {
                // SUCCESS REDIRECTION (to Login form)
                window.location.href = 'testmainscroll1.html?action=login';
            }, 2000); // 2 seconds
        });
        
        // Check if we need to redirect on FAILURE/VALIDATION ERROR (to Signup)
        <?php elseif ($redirect_to_signup): ?>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Validation/Duplicate Error. Redirecting to signup form in 3 seconds...");
            setTimeout(function() {
                // ERROR REDIRECTION (to Signup form)
                window.location.href = 'testmainscroll1.html?action=signup';
            }, 3000); // 3 seconds delay for user to read the error
        });
        <?php endif; ?>
    </script>
</body>
</html>
