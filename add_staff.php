<?php
session_start();
// NOTE: For a production application, you should check if the user is authenticated 
// and has permission (e.g., an Admin) to view this page.
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add New Staff</title>

<style>
   @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body {
        font-family: 'Poppins', sans-serif;
        background: #f4f4f4;
        margin: 0;
        padding-top: 80px; /* Space for fixed navbar */
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
    }

    .navbar {
      position: fixed;
      top: 0;
      width: 100%;
      background-color: #0056b3; /* Primary Blue */
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 40px;
      z-index: 1000;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
    }

    .navbar .logo {
      font-size: 22px;
      font-weight: bold;
      color: white;
      cursor: pointer;
    }

    .logo-img {
		width: 250px; 		/* wider logo */
		height: 55px; 		/* slightly shorter height for a rectangular shape */
		margin-right: 15px; /* good spacing from text */
		border-radius: 0; 	/* keeps sharp square corners */
	    object-fit: cover; 	/* ensures image doesn’t stretch */
	}

    .navbar .links a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      font-size: 16px;
      margin-left: 20px;
      padding: 8px 15px;
      border-radius: 6px;
      transition: background-color 0.3s ease;
    }

    .navbar .links a:hover {
      background-color: #004494; /* Darker blue on hover */
    }

    .signup-container {
        max-width: 500px;
        width: 90%;
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        margin-bottom: 40px;
        margin-top: 80px
    }

    .signup-container .links a {
    color: black;                     /* Text color */
    background: #eee;                  /* Grey background */
    border: 1px solid #ccc;            /* Light grey border */
    text-decoration: none;
    font-weight: 500;
    font-size: 16px;
    margin-left: 20px;
    padding: 8px 15px;
    border-radius: 6px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.signup-container .links a:hover {
    background-color: #ddd;           /* Darker grey on hover */
    color: #000;                       /* Ensure text stays black */
}

    .signup-container h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #0056b3;
        font-weight: 600;
        border-bottom: 2px solid #e0e0e0;
        padding-bottom: 10px;
    }

    .input-group {
        margin-bottom: 18px;
    }

    .input-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: #333;
    }

    .input-group input,
    .input-group select,
    .input-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        box-sizing: border-box; /* Includes padding in width/height */
        transition: border-color 0.3s;
    }

    .input-group input:focus,
    .input-group select:focus,
    .input-group textarea:focus {
        border-color: #0056b3;
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
    }

    .input-group textarea {
        min-height: 80px;
        resize: vertical;
    }

    .signup-btn {
        width: 100%;
        padding: 14px;
        background: #0056b3; /* Green for Action */
        color: #fff;
        font-size: 16px;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.1s;
        margin-top: 10px;
    }

    .signup-btn:hover {
        background: #218838;
        transform: translateY(-1px);
    }
    
    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
        display: none;
    }

    /* Responsive adjustments */
    @media (max-width: 600px) {
        .navbar {
            padding: 10px 20px;
        }
        .navbar .links a {
            font-size: 14px;
            margin-left: 10px;
            padding: 6px 12px;
        }
        .signup-container {
            padding: 20px;
        }
    }
</style>

</head>
<body>
    <div class="navbar">
        <div class="logo">
            <!-- Placeholder logo image - replace with your actual file -->
            <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img"> 
        </div>
        
    </div>

<div class="signup-container">
        <div class="links">
            <a href="staff_dashboard3.php"><i class="fa-solid fa-arrow-left"></i> Back</a>
        </div>
    <h2>Add New Staff</h2>

    <form action="process_add_staff.php" method="POST" class="signup-form">

        <div class="input-group">
            <label for="staff_name">Full Name</label>
            <input type="text" id="staff_name" name="staff_name" placeholder="Enter staff full name" required>
        </div>

        <div class="input-group">
            <label for="staff_email">Email</label>
            <input type="email" id="staff_email" name="staff_email" placeholder="Enter email" required>
        </div>

        <div class="input-group">
            <label for="staff_phone">Phone Number</label>
            <input type="text" id="staff_phone" name="staff_phone" placeholder="E.g. 0123456789" required pattern="[0-9]+">
        </div>

        <div class="input-group">
            <label for="staff_position">Staff Position</label>
            <select id="staff_position" name="staff_position" required>
                <option value="" disabled selected>-- Select Position --</option>
                <option value="Admin">Admin</option>
                <option value="Veterinarian">Veterinarian</option>
                <option value="Assistant">Assistant</option>
            </select>
        </div>

        <div class="input-group">
            <label for="staff_password">Password</label>
            <input type="password" id="staff_password" name="staff_password" placeholder="Enter password" required minlength="6">
            <div id="password-error" class="error-message"></div>
        </div>

        <div class="input-group">
            <label for="staff_confirm_password">Confirm Password</label>
            <input type="password" id="staff_confirm_password" name="staff_confirm_password" placeholder="Re-enter password" required minlength="6">
            <div id="confirm-error" class="error-message"></div>
        </div>

        <div class="input-group">
            <label for="staff_address">Address</label>
            <textarea name="staff_address" id="staff_address" placeholder="Address (House number, Street, City, State)" rows="3" required></textarea>
        </div>

        <button type="submit" class="signup-btn">Add Staff</button>
    </form>
</div>

<script>
    // Client-side password validation
    document.querySelector('.signup-form').addEventListener('submit', function(event) {
        const passwordField = document.getElementById('staff_password');
        const confirmField = document.getElementById('staff_confirm_password');
        const password = passwordField.value;
        const confirmPassword = confirmField.value;
        
        const passwordError = document.getElementById('password-error');
        const confirmError = document.getElementById('confirm-error');

        // Reset errors
        passwordError.style.display = 'none';
        confirmError.style.display = 'none';
        
        if (password !== confirmPassword) {
            confirmError.textContent = 'Error: Passwords do not match!';
            confirmError.style.display = 'block';
            confirmField.focus(); // Focus on the field with error
            event.preventDefault(); // Stop form submission
            return;
        }

        if (password.length < 6) {
            passwordError.textContent = 'Password must be at least 6 characters long.';
            passwordError.style.display = 'block';
            passwordField.focus();
            event.preventDefault();
            return;
        }
    });
</script>

</body>
</html>
