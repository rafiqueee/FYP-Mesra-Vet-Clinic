<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['petowners_ID'])) {
    header("Location: testmainscroll1.html");
    exit;
}

$owner_id = $_SESSION['petowners_ID'];
$selectedPets = $_POST['selected_pets'] ?? [];
$new_pet_name = trim($_POST['new_pet_name'] ?? '');

if ($new_pet_name) {
    $stmt = $conn->prepare("INSERT INTO petinfo (petowners_ID, pet_name, pet_age, pet_color, pet_breed, pet_gender) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "isssss",
        $owner_id,
        $new_pet_name,
        $_POST['new_pet_age'],
        $_POST['new_pet_color'],
        $_POST['new_pet_breed'],
        $_POST['new_pet_gender']
    );
    if ($stmt->execute()) {
        $new_pet_id = $stmt->insert_id;
        $selectedPets[] = $new_pet_id;
    }
    $stmt->close();
}

if (empty($selectedPets)) {
    $_SESSION['error'] = "Please select at least one pet or add a new pet.";
    header("Location: petinfotest.php");
    exit;
}

$_SESSION['booking_pets'] = $selectedPets;
header("Location: BookDetails.php");
exit;
?>
