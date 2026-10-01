
<?php

session_start();

include "../Model/DatabaseConnection.php";
include "helpers.php";

requireAdmin();

$action = $_POST["action"] ?? "status";

$database = new DatabaseConnection();
$connection = $database->openConnection();


// ADD OR UPDATE USER 

if ($action === "add" || $action === "update") {

    $id = $_POST["id"] ?? "";
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $phone = trim($_POST["phone"] ?? "");
    $role = $_POST["role"] ?? "";
    $status = $_POST["status"] ?? "";

    $hasNameError = true;
    $hasEmailError = true;
    $hasPasswordError = true;
    $hasPhoneError = true;
    $hasRoleError = true;
    $hasStatusError = true;


    //NAME VALIDATION

    if (!$name) {
        $_SESSION["nameError"] =
            "Name is required";
    } elseif (strlen($name) < 2) {
        $_SESSION["nameError"] =
            "Name must be at least 2 characters";
    } else {
        unset($_SESSION["nameError"]);
        $hasNameError = false;
    }


    //EMAIL VALIDATION

    if (!$email) {
        $_SESSION["emailError"] =
            "Email is required";
    } elseif (
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $_SESSION["emailError"] =
            "Enter a valid email address";
    } elseif (
        $action === "add" &&
        $database->emailExists($connection, $email)
    ) {
        $_SESSION["emailError"] =
            "Email already exists";
    } else {
        unset($_SESSION["emailError"]);
        $hasEmailError = false;
    }


    //PASSWORD VALIDATION 

    if ($action === "add" && !$password) {
        $_SESSION["passwordError"] =
            "Password is required";
    } elseif (
        $password &&
        strlen($password) < 6
    ) {
        $_SESSION["passwordError"] =
            "Password must be at least 6 characters";
    } else {
        unset($_SESSION["passwordError"]);
        $hasPasswordError = false;
    }


    //PHONE VALIDATION

    if (!$phone) {
        $_SESSION["phoneError"] =
            "Phone number is required";
    } elseif (
        !preg_match("/^[0-9]+$/", $phone)
    ) {
        $_SESSION["phoneError"] =
            "Phone number can contain only digits";
    } elseif (strlen($phone) !== 11) {
        $_SESSION["phoneError"] =
            "Phone number must be exactly 11 digits";
    } else {
        unset($_SESSION["phoneError"]);
        $hasPhoneError = false;
    }


    //ROLE VALIDATION 

    if (!$role) {
        $_SESSION["roleError"] =
            "Role is required";
    } elseif (
        $role !== "user" &&
        $role !== "admin"
    ) {
        $_SESSION["roleError"] =
            "Invalid role";
    } else {
        unset($_SESSION["roleError"]);
        $hasRoleError = false;
    }


    //STATUS VALIDATION 

    if (!$status) {
        $_SESSION["statusError"] =
            "Status is required";
    } elseif (
        $status !== "active" &&
        $status !== "blocked"
    ) {
        $_SESSION["statusError"] =
            "Invalid status";
    } else {
        unset($_SESSION["statusError"]);
        $hasStatusError = false;
    }


    

    if (
        $hasNameError ||
        $hasEmailError ||
        $hasPasswordError ||
        $hasPhoneError ||
        $hasRoleError ||
        $hasStatusError
    ) {

        $_SESSION["userFormAction"] = $action;
        $_SESSION["editUserId"] = $id;

        $_SESSION["oldUserName"] = $name;
        $_SESSION["oldUserEmail"] = $email;
        $_SESSION["oldUserPhone"] = $phone;
        $_SESSION["oldUserRole"] = $role;
        $_SESSION["oldUserStatus"] = $status;

        redirect("../View/adminUsers.php");
    }


    //ADD USER 

    if ($action === "add") {

        if (
            $database->addUser(
                $connection,
                $name,
                $email,
                $password,
                $phone,
                $role,
                $status
            )
        ) {
            $_SESSION["successMessage"] =
                "User added successfully";
        } else {
            $_SESSION["userError"] =
                "Could not add user";
        }
    }


    // UPDATE USER 

    else {

        if (
            $id == $_SESSION["loggedInUserId"] &&
            $role !== "admin"
        ) {
            $_SESSION["userError"] =
                "You cannot remove your own admin role";

            $_SESSION["userFormAction"] = "update";
            $_SESSION["editUserId"] = $id;

            $_SESSION["oldUserName"] = $name;
            $_SESSION["oldUserEmail"] = $email;
            $_SESSION["oldUserPhone"] = $phone;
            $_SESSION["oldUserRole"] = $role;
            $_SESSION["oldUserStatus"] = $status;

        } elseif (
            $database->updateUser(
                $connection,
                $id,
                $name,
                $email,
                $phone,
                $role,
                $status
            )
        ) {
            $_SESSION["successMessage"] =
                "User updated successfully";

            

            unset($_SESSION["userFormAction"]);
            unset($_SESSION["editUserId"]);

            unset($_SESSION["oldUserName"]);
            unset($_SESSION["oldUserEmail"]);
            unset($_SESSION["oldUserPhone"]);
            unset($_SESSION["oldUserRole"]);
            unset($_SESSION["oldUserStatus"]);

        } else {
            $_SESSION["userError"] =
                "Could not update user";

            $_SESSION["userFormAction"] = "update";
            $_SESSION["editUserId"] = $id;

            $_SESSION["oldUserName"] = $name;
            $_SESSION["oldUserEmail"] = $email;
            $_SESSION["oldUserPhone"] = $phone;
            $_SESSION["oldUserRole"] = $role;
            $_SESSION["oldUserStatus"] = $status;
        }
    }
}


//DELETE USER 

elseif ($action === "delete") {

    $id = $_POST["id"] ?? "";

    if (!$id || !is_numeric($id)) {
        $_SESSION["userError"] =
            "Invalid user";

    } elseif (
        (int)$id ===
        (int)$_SESSION["loggedInUserId"]
    ) {
        $_SESSION["userError"] =
            "You cannot delete your own account";

    } elseif (
        $database->deleteUser($connection, $id)
    ) {
        $_SESSION["successMessage"] =
            "User deleted successfully";

    } else {
        $_SESSION["userError"] =
            "Could not delete user";
    }
}


// UPDATE USER STATUS 

else {

    $id = $_POST["id"] ?? "";
    $status = $_POST["status"] ?? "";

    if (
        !$id ||
        (
            $status !== "active" &&
            $status !== "blocked"
        )
    ) {
        $_SESSION["userError"] =
            "Invalid user status update";

    } elseif (
        $database->updateUserStatus(
            $connection,
            $id,
            $status
        )
    ) {
        $_SESSION["successMessage"] =
            "User status updated";

    } else {
        $_SESSION["userError"] =
            "Could not update user status";
    }
}


redirect("../View/adminUsers.php");

?>




