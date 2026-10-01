<?php
session_start();
include "../Model/DatabaseConnection.php";
include "helpers.php";
requireAdmin();

$action = $_POST["action"] ?? "";
$database = new DatabaseConnection();
$connection = $database->openConnection();

if ($action === "add") {
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    if (!$name) {
        $_SESSION["categoryError"] = "Category name is required";
    } else {
        $database->addCategory($connection, $name, $description);
        $_SESSION["successMessage"] = "Category added";
    }
} elseif ($action === "update") {
    $id = $_POST["id"] ?? "";
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    if (!$id || !$name) {
        $_SESSION["categoryError"] = "Category information is required";
    } else {
        $database->updateCategory($connection, $id, $name, $description);
        $_SESSION["successMessage"] = "Category updated";
    }
} elseif ($action === "delete") {
    $id = $_POST["id"] ?? "";
    if ($id) {
        if ($database->deleteCategory($connection, $id)) {
            $_SESSION["successMessage"] = "Category deleted";
        } else {
            $_SESSION["categoryError"] = "Category cannot be deleted if items use it";
        }
    }
}
redirect("../View/adminCategories.php");
