<?php
session_start();
if (!($_SESSION["isLoggedIn"] ?? false)) {
    header("Location: login.php");
    exit();
}
include "../Model/DatabaseConnection.php";
$db = new DatabaseConnection();
$con = $db->openConnection();
$claims = $db->getUserClaims($con, $_SESSION["loggedInUserId"]);
include "header.php";
?>
<h1>My Claims</h1>
<div class="table-wrap">
    <table class="table table-list my-claims-list">
        <tr>
            <th>Item</th>
            <th>Type</th>
            <th>Status</th>
            <th>Submitted</th>
        </tr><?php while ($c = $claims->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($c["item_title"]); ?></td>
                <td><?php echo htmlspecialchars($c["item_type"]); ?></td>
                <td><?php echo htmlspecialchars($c["status"]); ?></td>
                <td><?php echo htmlspecialchars($c["created_at"]); ?></td>
            </tr><?php endwhile; ?>
    </table>
</div>
<?php include "footer.php"; ?>