<?php
session_start();
if (!($_SESSION["isLoggedIn"] ?? false)) {
    header("Location: login.php");
    exit();
}
include "../Model/DatabaseConnection.php";
$db = new DatabaseConnection();
$con = $db->openConnection();
$items = $db->getUserItems($con, $_SESSION["loggedInUserId"]);
include "header.php";
?>
<div class="top-actions">
    <h1>My Reported Items</h1><a class="btn" href="reportItem.php">Report New Item</a>
</div>
<div class="table-wrap">
    <table class="table table-list my-items-list">
        <tr>
            <th>Item</th>
            <th>Type</th>
            <th>Category</th>
            <th>Status</th>
            <th>Date</th>
        </tr><?php while ($i = $items->fetch_assoc()): ?>
            <tr>
                <td><a href="itemDetails.php?id=<?php echo $i["id"]; ?>"><?php echo htmlspecialchars($i["title"]); ?></a>
                </td>
                <td><?php echo htmlspecialchars($i["type"]); ?></td>
                <td><?php echo htmlspecialchars($i["category_name"]); ?></td>
                <td><?php echo htmlspecialchars($i["status"]); ?></td>
                <td><?php echo htmlspecialchars($i["date_lost_found"]); ?></td>
            </tr><?php endwhile; ?>
    </table>
</div>
<?php include "footer.php"; ?>