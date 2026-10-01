<?php
session_start();
include "../Model/DatabaseConnection.php";
include "helpers.php";
requireAdmin();

$claim_id = $_POST["claim_id"] ?? "";
$status = $_POST["status"] ?? "";
$allowed = ["Pending", "Approved", "Rejected", "Returned"];
if (!$claim_id || !in_array($status, $allowed)) {
    $_SESSION["claimAdminError"] = "Invalid claim update";
    redirect("../View/adminClaims.php");
}

$database = new DatabaseConnection();
$connection = $database->openConnection();
if ($database->updateClaimStatus($connection, $claim_id, $status)) {

    if ($database->updateItemStatusByClaimStatus($connection, $claim_id, $status)) {
        $_SESSION["successMessage"] = "Claim status updated";
    } else {
        $_SESSION["claimAdminError"] = "Claim updated but item status could not be updated";
    }

} else {
    $_SESSION["claimAdminError"] = "Could not update claim status";
}
redirect("../View/adminClaims.php");
?>