<?php
session_start();
include "../Model/DatabaseConnection.php";
include "helpers.php";
requireLogin();

$item_id = $_POST["item_id"] ?? "";
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$description = trim($_POST["description"] ?? "");
$additional_info = trim($_POST["additional_info"] ?? "");

$_SESSION["claim_old"] = $_POST;
$_SESSION["claim_item_id"] = $item_id;
$hasError = false;

if (!$item_id || !is_numeric($item_id)) {
    $_SESSION["claimError"] = "Invalid item";
    redirect("../View/foundItems.php");
}

$database = new DatabaseConnection();
$connection = $database->openConnection();
$item = $database->getItemById($connection, $item_id);

if (
    !$item ||
    $item["type"] !== "Found" ||
    $item["status"] !== "Open"
) {
    $_SESSION["claimError"] = "This item is currently unavailable for claiming";
    redirect("../View/foundItems.php");
}

if ((int) $item["user_id"] === (int) $_SESSION["loggedInUserId"]) {
    $_SESSION["claimError"] = "You cannot claim your own found report";
    redirect("../View/foundItems.php");
}

if (!$name) {
    $_SESSION["claimNameError"] = "Name is required";
    $hasError = true;
} else
    unset($_SESSION["claimNameError"]);

if (!$email) {
    $_SESSION["claimEmailError"] = "Email is required";
    $hasError = true;
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["claimEmailError"] = "Enter a valid email";
    $hasError = true;
} else
    unset($_SESSION["claimEmailError"]);

if (!$phone) {
    $_SESSION["claimPhoneError"] = "Phone number is required";
    $hasError = true;
} elseif (!preg_match('/^[0-9+ -]{7,20}$/', $phone)) {
    $_SESSION["claimPhoneError"] = "Enter a valid phone number";
    $hasError = true;
} else
    unset($_SESSION["claimPhoneError"]);

if (!$description) {
    $_SESSION["claimDescriptionError"] = "Describe the item to prove ownership";
    $hasError = true;
} elseif (strlen($description) < 10) {
    $_SESSION["claimDescriptionError"] = "Description must be at least 10 characters";
    $hasError = true;
} else
    unset($_SESSION["claimDescriptionError"]);

$proofPath = "";
if (isset($_FILES["proof"]) && $_FILES["proof"]["error"] !== UPLOAD_ERR_NO_FILE) {
    $proofPath = saveUploadedFile($_FILES["proof"], ["jpg", "jpeg", "png", "pdf", "webp"]);
    if (strpos($proofPath, "ERROR:") === 0) {
        $_SESSION["claimProofError"] = substr($proofPath, 7);
        $hasError = true;
        $proofPath = "";
    } else
        unset($_SESSION["claimProofError"]);
}

if ($hasError) {
    redirect("../View/claimItem.php?id=" . $item_id);
}

$result = $database->addClaim($connection, $item_id, $_SESSION["loggedInUserId"], $name, $email, $phone, $description, $proofPath, $additional_info);

if ($result) {
    $database->updateItemStatus($connection, $item_id, "Claimed");
    unset($_SESSION["claim_old"], $_SESSION["claim_item_id"]);
    $_SESSION["successMessage"] = "Claim submitted successfully. Please wait for admin review.";
    redirect("../View/myClaims.php");
}

$_SESSION["claimError"] = "Could not submit claim";
redirect("../View/claimItem.php?id=" . $item_id);
?>