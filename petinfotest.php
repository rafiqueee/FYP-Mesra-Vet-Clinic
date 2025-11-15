<?php 
session_start();
include('db_connect.php');

if (!isset($_SESSION['petowners_ID'])) {
    header("Location: testmainscroll1.html");
    exit;
}

$owner_id = $_SESSION['petowners_ID'];

// Fetch registered pets
$sql = "SELECT pet_id, pet_name FROM petinfo WHERE petowners_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
$pets = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pet Information</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    body { font-family: Poppins, sans-serif; background: #f5f8ff; margin: 0; }
    .appointment-container { max-width:700px; margin:120px auto; background:#fff; padding:30px 40px; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.1); }
    h2 { color: #003d80; margin-bottom: 20px; }
    h3 { color: #003d80; margin-top: 20px; }
    .form-group { margin-bottom: 15px; }
    label { font-weight: 600; display: block; margin-bottom: 6px; }
    input[type=text], select { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; }
    .next-btn, .back-btn { padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; width: 100%; }
    .next-btn { background: #0056b3; color: #fff; margin-top: 10px; }
    .next-btn:hover { background: #007bff; }
    .back-btn { background:#eee; color:#333; margin-bottom:20px; }
    .pet-checkbox { margin-bottom: 8px; }

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
</style>
</head>
<body>

  <div class="navbar">
    <div class="logo">
      <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img">
    </div>
  </div>

<div class="appointment-container">
    <button class="back-btn" onclick="window.location.href='petowners_dashboard2.php'">BACK</button>

    <h2>Select or Add Pet</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <div style="background:#ffe6e6;padding:10px;border-radius:6px;color:#a00;margin-bottom:15px;">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="processpettest.php" method="POST">
        <!-- MULTIPLE REGISTERED PETS -->
        <div class="form-group">
            <label>Select Registered Cats (you may choose multiple)</label>
            <?php if (empty($pets)): ?>
                <p>No registered cats found.</p>
            <?php else: ?>
                <?php foreach ($pets as $pet): ?>
                    <div class="pet-checkbox">
                        <input type="checkbox" name="selected_pets[]" value="<?= $pet['pet_id']; ?>" id="pet_<?= $pet['pet_id']; ?>">
                        <label for="pet_<?= $pet['pet_id']; ?>"><?= htmlspecialchars($pet['pet_name']); ?></label>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <hr style="margin: 25px 0;">

        <!-- ADD NEW PET SECTION -->
        <h3>Add New Cat (Optional)</h3>
        <div class="form-group">
            <label>Pet Name</label>
            <input type="text" name="new_pet_name">
        </div>
        <div class="form-group">
            <label>Pet Age</label>
            <select name="new_pet_age">
                <option value="">-- Select Age --</option>
                <?php for ($i=0; $i<=20; $i++): ?>
                    <option value="<?= $i; ?>"><?= $i; ?> years</option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Pet Color</label>
            <select name="new_pet_color">
                <option value="">Select Color</option>
                <option>Black</option>
                <option>White</option>
                <option>Orange</option>
                <option>Brown</option>
                <option>Golden</option>
                <option>Gray</option>
                <option>Mixed</option>
            </select>
        </div>
        <div class="form-group">
            <label>Pet Breed</label>
            <select name="new_pet_breed">
                <option value="">Select Breed</option>
                <option>Persian</option>
                <option>Siamese</option>
                <option>Maine Coon</option>
                <option>Bengal</option>
                <option>British Shorthair</option>
                <option>Mixed</option>
                <option>Other</option>
            </select>
        </div>
        <div class="form-group">
            <label>Pet Gender</label>
            <select name="new_pet_gender">
                <option value="">Select Gender</option>
                <option>Male</option>
                <option>Female</option>
            </select>
        </div>

        <button type="submit" class="next-btn">NEXT</button>
    </form>
</div>
</body>
</html>



