
<?php

session_start();

include "../Controller/helpers.php";
requireAdmin();

include "../Model/DatabaseConnection.php";

$db = new DatabaseConnection();
$con = $db->openConnection();
$users = $db->getUsers($con);


//GENERAL MESSAGES 

$error = $_SESSION["userError"] ?? "";
$success = $_SESSION["successMessage"] ?? "";

unset($_SESSION["userError"]);
unset($_SESSION["successMessage"]);


//INDIVIDUAL FIELD ERRORS 

$nameError = $_SESSION["nameError"] ?? "";
$emailError = $_SESSION["emailError"] ?? "";
$passwordError = $_SESSION["passwordError"] ?? "";
$phoneError = $_SESSION["phoneError"] ?? "";
$roleError = $_SESSION["roleError"] ?? "";
$statusError = $_SESSION["statusError"] ?? "";


//PREVIOUSLY ENTERED VALUES 

$formAction = $_SESSION["userFormAction"] ?? "";
$editUserId = $_SESSION["editUserId"] ?? "";

$oldName = $_SESSION["oldUserName"] ?? "";
$oldEmail = $_SESSION["oldUserEmail"] ?? "";
$oldPhone = $_SESSION["oldUserPhone"] ?? "";
$oldRole = $_SESSION["oldUserRole"] ?? "user";
$oldStatus = $_SESSION["oldUserStatus"] ?? "active";



unset($_SESSION["nameError"]);
unset($_SESSION["emailError"]);
unset($_SESSION["passwordError"]);
unset($_SESSION["phoneError"]);
unset($_SESSION["roleError"]);
unset($_SESSION["statusError"]);

unset($_SESSION["userFormAction"]);
unset($_SESSION["editUserId"]);

unset($_SESSION["oldUserName"]);
unset($_SESSION["oldUserEmail"]);
unset($_SESSION["oldUserPhone"]);
unset($_SESSION["oldUserRole"]);
unset($_SESSION["oldUserStatus"]);


include "header.php";


if ($error) {
    echo '<div class="fail">' . $error . '</div>';
}

if ($success) {
    echo '<div class="success">' . $success . '</div>';
}

?>


<h1>Manage Users</h1>



<div
    class="form-card"
    style="margin-left: 0; max-width: 100%"
>
    <h3>Add User</h3>

    <form
        action="../Controller/adminUser.php"
        method="post"
    >
        <input
            type="hidden"
            name="action"
            value="add"
        >

        <div class="grid">


            <div class="field">
                <label>Name</label>

                <input
                    type="text"
                    name="name"
                    value="<?php
                    if ($formAction === "add") {
                        echo $oldName;
                    }
                    ?>"
                >

                <?php
                if ($formAction === "add" && $nameError) {
                ?>
                    <span class="error">
                        <?php echo $nameError; ?>
                    </span>
                <?php
                }
                ?>
            </div>


            <div class="field">
                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    value="<?php
                    if ($formAction === "add") {
                        echo $oldEmail;
                    }
                    ?>"
                >

                <?php
                if ($formAction === "add" && $emailError) {
                ?>
                    <span class="error">
                        <?php echo $emailError; ?>
                    </span>
                <?php
                }
                ?>
            </div>



            <div class="field">
                <label>Password</label>

                <input
                    type="password"
                    name="password"
                >

                <?php
                if (
                    $formAction === "add" &&
                    $passwordError
                ) {
                ?>
                    <span class="error">
                        <?php echo $passwordError; ?>
                    </span>
                <?php
                }
                ?>
            </div>



            <div class="field">
                <label>Phone</label>

                <input
                    type="text"
                    name="phone"
                    value="<?php
                    if ($formAction === "add") {
                        echo $oldPhone;
                    }
                    ?>"
                >

                <?php
                if ($formAction === "add" && $phoneError) {
                ?>
                    <span class="error">
                        <?php echo $phoneError; ?>
                    </span>
                <?php
                }
                ?>
            </div>



            <div class="field">
                <label>Role</label>

                <select name="role">

                    <option
                        value="user"
                        <?php
                        if (
                            $formAction !== "add" ||
                            $oldRole === "user"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        User
                    </option>

                    <option
                        value="admin"
                        <?php
                        if (
                            $formAction === "add" &&
                            $oldRole === "admin"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Admin
                    </option>

                </select>

                <?php
                if ($formAction === "add" && $roleError) {
                ?>
                    <span class="error">
                        <?php echo $roleError; ?>
                    </span>
                <?php
                }
                ?>
            </div>



            <div class="field">
                <label>Status</label>

                <select name="status">

                    <option
                        value="active"
                        <?php
                        if (
                            $formAction !== "add" ||
                            $oldStatus === "active"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Active
                    </option>

                    <option
                        value="blocked"
                        <?php
                        if (
                            $formAction === "add" &&
                            $oldStatus === "blocked"
                        ) {
                            echo "selected";
                        }
                        ?>
                    >
                        Blocked
                    </option>

                </select>

                <?php
                if (
                    $formAction === "add" &&
                    $statusError
                ) {
                ?>
                    <span class="error">
                        <?php echo $statusError; ?>
                    </span>
                <?php
                }
                ?>
            </div>

        </div>

        <button class="plain-button">
            Add User
        </button>
    </form>
</div>



<div class="table-wrap">

    <table class="table table-list admin-users-list">

        <tr>
            <th>Name</th>
            <th>Email</th>
            <th00>Phone</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php

        while ($u = $users->fetch_assoc()) {

            $thisEditHasError =
                $formAction === "update" &&
                $editUserId == $u["id"];

        ?>

            <tr>

                <td>
                    <?php echo $u["name"]; ?>
                </td>

                <td>
                    <?php echo $u["email"]; ?>
                </td>

                <td>
                    <?php echo $u["phone"]; ?>
                </td>

                <td>
                    <?php echo $u["role"]; ?>
                </td>

                <td>
                    <?php echo $u["status"]; ?>
                </td>

                <td>

                    <details
                        <?php
                        if ($thisEditHasError) {
                            echo "open";
                        }
                        ?>
                    >

                        <summary>Edit</summary>

                        <form
                            action="../Controller/adminUser.php"
                            method="post"
                        >

                            <input
                                type="hidden"
                                name="action"
                                value="update"
                            >

                            <input
                                type="hidden"
                                name="id"
                                value="<?php echo $u["id"]; ?>"
                            >


                            <div class="field">

                                <label>Name</label>

                                <input
                                    type="text"
                                    name="name"
                                    value="<?php
                                    if ($thisEditHasError) {
                                        echo $oldName;
                                    } else {
                                        echo $u["name"];
                                    }
                                    ?>"
                                >

                                <?php
                                if (
                                    $thisEditHasError &&
                                    $nameError
                                ) {
                                ?>
                                    <span class="error">
                                        <?php echo $nameError; ?>
                                    </span>
                                <?php
                                }
                                ?>

                            </div>



                            <div class="field">

                                <label>Email</label>

                                <input
                                    type="email"
                                    name="email"
                                    value="<?php
                                    if ($thisEditHasError) {
                                        echo $oldEmail;
                                    } else {
                                        echo $u["email"];
                                    }
                                    ?>"
                                >

                                <?php
                                if (
                                    $thisEditHasError &&
                                    $emailError
                                ) {
                                ?>
                                    <span class="error">
                                        <?php echo $emailError; ?>
                                    </span>
                                <?php
                                }
                                ?>

                            </div>



                            <div class="field">

                                <label>Phone</label>

                                <input
                                    type="text"
                                    name="phone"
                                    value="<?php
                                    if ($thisEditHasError) {
                                        echo $oldPhone;
                                    } else {
                                        echo $u["phone"];
                                    }
                                    ?>"
                                >

                                <?php
                                if (
                                    $thisEditHasError &&
                                    $phoneError
                                ) {
                                ?>
                                    <span class="error">
                                        <?php echo $phoneError; ?>
                                    </span>
                                <?php
                                }
                                ?>

                            </div>



                            <div class="field">

                                <label>Role</label>

                                <?php
                                if ($thisEditHasError) {
                                    $editRole = $oldRole;
                                } else {
                                    $editRole = $u["role"];
                                }
                                ?>

                                <select name="role">

                                    <option
                                        value="user"
                                        <?php
                                        if ($editRole === "user") {
                                            echo "selected";
                                        }
                                        ?>
                                    >
                                        User
                                    </option>

                                    <option
                                        value="admin"
                                        <?php
                                        if ($editRole === "admin") {
                                            echo "selected";
                                        }
                                        ?>
                                    >
                                        Admin
                                    </option>

                                </select>

                                <?php
                                if (
                                    $thisEditHasError &&
                                    $roleError
                                ) {
                                ?>
                                    <span class="error">
                                        <?php echo $roleError; ?>
                                    </span>
                                <?php
                                }
                                ?>

                            </div>



                            <div class="field">

                                <label>Status</label>

                                <?php
                                if ($thisEditHasError) {
                                    $editStatus = $oldStatus;
                                } else {
                                    $editStatus = $u["status"];
                                }
                                ?>

                                <select name="status">

                                    <option
                                        value="active"
                                        <?php
                                        if (
                                            $editStatus === "active"
                                        ) {
                                            echo "selected";
                                        }
                                        ?>
                                    >
                                        Active
                                    </option>

                                    <option
                                        value="blocked"
                                        <?php
                                        if (
                                            $editStatus === "blocked"
                                        ) {
                                            echo "selected";
                                        }
                                        ?>
                                    >
                                        Blocked
                                    </option>

                                </select>

                                <?php
                                if (
                                    $thisEditHasError &&
                                    $statusError
                                ) {
                                ?>
                                    <span class="error">
                                        <?php echo $statusError; ?>
                                    </span>
                                <?php
                                }
                                ?>

                            </div>

                            <button class="plain-button">
                                Update
                            </button>

                        </form>

                    </details>


                    <?php

                    if (
                        $u["id"] !=
                        $_SESSION["loggedInUserId"]
                    ) {

                    ?>

                        <form
                            action="../Controller/adminUser.php"
                            method="post"
                        >
                            <input
                                type="hidden"
                                name="action"
                                value="delete"
                            >

                            <input
                                type="hidden"
                                name="id"
                                value="<?php echo $u["id"]; ?>"
                            >

                            <button
                                class="plain-button"
                                onclick="
                                    return confirm(
                                        'Delete this user?'
                                    )
                                "
                            >
                                Delete
                            </button>
                        </form>

                    <?php

                    } else {

                        echo '<span class="muted">
                            Current admin
                        </span>';
                    }

                    ?>

                </td>

            </tr>

        <?php
        }
        ?>

    </table>

</div>


<?php include "footer.php"; ?>