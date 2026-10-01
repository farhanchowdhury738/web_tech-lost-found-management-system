<?php
session_start();

include "../Controller/helpers.php";
requireAdmin();

include "../Model/DatabaseConnection.php";

$database = new DatabaseConnection();
$connection = $database->openConnection();

$cats = $database->getCategories($connection);

$error = $_SESSION["categoryError"] ?? "";
unset($_SESSION["categoryError"]);

include "header.php";
?>

<h1>Manage Categories</h1>

<?php
if ($error) {
    echo '<p>' . htmlspecialchars($error) . '</p>';
}
?>

<fieldset>
    <legend>Add Category</legend>

    <form action="../Controller/adminCategory.php" method="post">

        <input type="hidden" name="action" value="add">

        <table>
            <tr>
                <td>Name:</td>
                <td><input type="text" name="name"></td>
            </tr>

            <tr>
                <td>Description:</td>
                <td><textarea name="description"></textarea></td>
            </tr>

            <tr>
                <td></td>
                <td><input type="submit" value="Add Category"></td>
            </tr>
        </table>

    </form>
</fieldset>

<br>

<fieldset>
    <legend>Categories</legend>

    <table class="table table-list category-list">

        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Update</th>
            <th>Delete</th>
        </tr>

        <?php while ($c = $cats->fetch_assoc()): ?>

            <tr>
                <td>
                    <form action="../Controller/adminCategory.php" method="post">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $c["id"]; ?>">
                        <input type="text" name="name" value="<?php echo htmlspecialchars($c["name"]); ?>">
                </td>

                <td>
                    <textarea name="description"><?php echo htmlspecialchars($c["description"]); ?></textarea>
                </td>

                <td>
                    <input type="submit" value="Update">
                    </form>
                </td>

                <td>
                    <form action="../Controller/adminCategory.php" method="post">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $c["id"]; ?>">
                        <input type="submit" value="Delete">
                    </form>
                </td>
            </tr>

        <?php endwhile; ?>

    </table>
</fieldset>

<?php include "footer.php"; ?>