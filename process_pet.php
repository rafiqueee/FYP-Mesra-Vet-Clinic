<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['petowners_ID'])) {
    header("Location: testmainscroll1.html");
    exit;
}

$owner_id = $_SESSION['petowners_ID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_POST['existing_pet'])) {
        $existingPet = json_decode($_POST['existing_pet'], true);
        $_SESSION['pet_id'] = $existingPet['pet_id']; // Save selected pet ID
    } else {
        // New pet
        $pet_name = trim($_POST['pet_name']);
        $pet_age = $_POST['pet_age'];
        $pet_color = $_POST['pet_color'];
        $pet_breed = $_POST['pet_breed'];
        $pet_gender = $_POST['pet_gender'];

        if (empty($pet_name) || empty($pet_age) || empty($pet_color) || empty($pet_breed) || empty($pet_gender)) {
            $_SESSION['error'] = "Please fill in all pet information.";
            header("Location: PetInfo3.php");
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO petinfo (petowners_ID, pet_name, pet_age, pet_color, pet_breed, pet_gender) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isisss", $owner_id, $pet_name, $pet_age, $pet_color, $pet_breed, $pet_gender);

        if ($stmt->execute()) {
            $_SESSION['pet_id'] = $stmt->insert_id; // Save new pet ID
        } else {
            $_SESSION['error'] = "Failed to save pet information. Please try again.";
            header("Location: PetInfo3.php");
            exit;
        }

        $stmt->close();
    }

    // Redirect to booking page
    header("Location: BookDetails.php");
    exit;
}
?>

