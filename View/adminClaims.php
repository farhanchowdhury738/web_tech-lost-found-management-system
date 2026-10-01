
<?php
session_start();

include "../Controller/helpers.php";
requireAdmin();

include "../Model/DatabaseConnection.php";

$db = new DatabaseConnection();
$con = $db->openConnection();

$claims = $db->getClaims($con);

$error = $_SESSION["claimAdminError"] ?? "";
unset($_SESSION["claimAdminError"]);

include "header.php";

if ($error) {
    echo '<div class="fail">' . htmlspecialchars($error) . '</div>';
}
?>

<h1>Manage Claims</h1>

<div class="table-wrap">
    <table class="table table-list admin-claims-list">
        <tr>
            <th>Item</th>
            <th>Claimant</th>
            <th>Email</th>
            <th>Description</th>
            <th>Status</th>
            <th>Proof</th>
            <th>Action</th>
        </tr>

        <?php while ($c = $claims->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php echo htmlspecialchars($c["item_title"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($c["user_name"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($c["user_email"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($c["description"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($c["status"]); ?>
                </td>

                <td>
                    <?php if ($c["proof_path"]): ?>
                        <a href="<?php echo htmlspecialchars($c["proof_path"]); ?>" target="_blank">
                            View Proof
                        </a>
                    <?php else: ?>
                        No proof
                    <?php endif; ?>
                </td>

                <td>
                    <form action="../Controller/adminClaim.php" method="post">
                        <input
                            type="hidden"
                            name="claim_id"
                            value="<?php echo $c["id"]; ?>"
                        >

                        <select name="status">
                            <option <?php echo $c["status"] === 'Pending' ? 'selected' : ''; ?>>
                                Pending
                            </option>

                            <option <?php echo $c["status"] === 'Approved' ? 'selected' : ''; ?>>
                                Approved
                            </option>

                            <option <?php echo $c["status"] === 'Rejected' ? 'selected' : ''; ?>>
                                Rejected
                            </option>

                            <option <?php echo $c["status"] === 'Returned' ? 'selected' : ''; ?>>
                                Returned
                            </option>
                        </select>

                        <button class="btn small">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include "footer.php"; ?>

