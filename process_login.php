<?php
session_start();
$conn = new mysqli("localhost", "root", "", "fyp2_pet_clinic");

$message = "";
$redirect = "testmainscroll1.html?action=login"; // default redirect on failure

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST["email"];
    $password = $_POST["password"];

    $result = $conn->query("SELECT * FROM petowners WHERE petowners_email='$email'");

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if ($password === $row['petowners_password']) {
            $_SESSION['petowners_ID']    = $row['petowners_ID'];
            $_SESSION['petowners_email'] = $row['petowners_email'];
            $_SESSION['petowners_name']  = $row['petowners_name'];

            $message = "✅ Login Successful! Welcome, " . $row['petowners_name'];
            $redirect = "petowners_dashboard2.php"; // success redirect
        } else {
            $message = "❌ Invalid password!";
        }
    } else {
        $message = "❌ No account found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login Result</title>
<link rel="stylesheet" href="style.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f5f8ff; margin: 0; padding: 0; color: #333; min-height: 100vh; }
        * { box-sizing: border-box; }

/* Navbar like main page */
.navbar {
    position: fixed;
    top: 0;
    width: 100%;
    background-color: #0056b3;
    display: flex;
    align-items: center;
    padding: 15px 40px;
    z-index: 1000;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
}

.navbar .logo {
    cursor: pointer;
}

.navbar .logo img {
    width: 250px;
    height: 55px;
    object-fit: cover;
}

.logo-img { 
    /* Set the size of your logo image here */
    width: 200px; /* Adjust this width as needed */
    height: auto; 
    object-fit: contain; 
}

/* Popup container */
.popup-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    padding-top: 70px; /* space for navbar */
    box-sizing: border-box;
}

.popup-box {
    padding: 40px 60px;
    background-color: rgba(242,242,242,0.9);
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
    text-align: center;
}

.popup-box h2 {
    font-size: 22px;
    margin: 0 0 10px 0;
}
</style>

<script>
// Automatically redirect after 3 seconds
setTimeout(function() {
    window.location.href = "<?php echo $redirect; ?>";
}, 1000);
</script>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="logo">
        <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img">
    </div>
</div>

<!-- Popup message -->
<div class="popup-container">
    <div class="popup-box">
        <h2><?php echo $message; ?></h2>
        <p>Please Wait Redirecting...</p>
    </div>
</div>

</body>
</html>
