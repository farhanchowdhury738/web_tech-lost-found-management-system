<?php
session_start();
include "../Controller/helpers.php";
requireAdmin();
include "../Model/DatabaseConnection.php";
$db = new DatabaseConnection();
$con = $db->openConnection();
$cats = $db->getCategories($con);
$type = $_GET["type"] ?? "";
$items = $db->getItems($con, $type);
$error = $_SESSION["adminItemError"] ?? "";
unset($_SESSION["adminItemError"]);
include "header.php";
if ($error)
    echo '<div class="fail">' . htmlspecialchars($error) . '</div>';
?>
<h1>Manage Items</h1>
<div class="top-actions">
    <p class="muted">Manage all lost and found reports.</p>
    <div><a class="btn small" href="adminItems.php?type=Lost">Lost Items</a> <a class="btn small"
            href="adminItems.php?type=Found">Found Items</a> <a class="btn secondary small" href="adminItems.php">All
            Items</a></div>
</div>
<div class="form-card" style="margin-left:0;max-width:100%">
    <h3>Add Item</h3>
    <form action="../Controller/adminItem.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">
        <div class="grid">
            <div class="field"><label>Type</label><select name="type">
                    <option value="">Select type</option>
                    <option>Lost</option>
                    <option>Found</option>
                </select></div>
            <div class="field"><label>Title</label><input name="title"></div>
            <div class="field"><label>Category</label><select name="category_id">
                    <option value="">Select category</option><?php while ($cat = $cats->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endwhile; ?>
                </select></div>
            <div class="field"><label>Location</label><input name="location"></div>
            <div class="field"><label>Date</label><input type="date" name="date_lost_found"></div>
            <div class="field"><label>Contact Information</label><input name="contact_info"></div>
        </div>
        <div class="field"><label>Description</label><textarea name="description"></textarea></div>
        <div class="field"><label>Image</label><input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"></div>
        <button class="btn">Add Item</button>
    </form>
</div>
<div class="table-wrap">
    <table class="table table-list admin-items-list">
        <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Type</th>
            <th>Category</th>
            <th>Reporter</th>
            <th>Location</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($i = $items->fetch_assoc()): ?>
            <tr>
                <td><?php if ($i["image_path"]): ?><img src="<?php echo htmlspecialchars($i["image_path"]); ?>"
                            style="width:70px;height:50px;object-fit:cover"><?php else: ?>No image<?php endif; ?></td>
                <td><?php echo htmlspecialchars($i["title"]); ?><br><small><?php echo htmlspecialchars($i["description"]); ?></small>
                </td>
                <td><?php echo htmlspecialchars($i["type"]); ?></td>
                <td><?php echo htmlspecialchars($i["category_name"]); ?></td>
                <td><?php echo htmlspecialchars($i["reporter_name"]); ?></td>
                <td><?php echo htmlspecialchars($i["location"]); ?></td>
                <td><?php echo htmlspecialchars($i["date_lost_found"]); ?></td>
                <td><?php echo htmlspecialchars($i["status"]); ?></td>
                <td>
                    <details>
                        <summary>Edit</summary>
                        <form action="../Controller/adminItem.php" method="post" enctype="multipart/form-data"
                            style="min-width:260px;margin-top:8px">
                            <input type="hidden" name="action" value="update"><input type="hidden" name="id"
                                value="<?php echo $i['id']; ?>"><input type="hidden" name="old_image"
                                value="<?php echo htmlspecialchars($i['image_path']); ?>">
                            <div class="field"><label>Type</label><select name="type">
                                    <option <?php echo $i['type'] === 'Lost' ? 'selected' : ''; ?>>Lost</option>
                                    <option <?php echo $i['type'] === 'Found' ? 'selected' : ''; ?>>Found</option>
                                </select></div>
                            <div class="field"><label>Title</label><input name="title"
                                    value="<?php echo htmlspecialchars($i['title']); ?>"></div>
                            <div class="field"><label>Category</label><select
                                    name="category_id"><?php $cats2 = $db->getCategories($con);
                                    while ($cat2 = $cats2->fetch_assoc()): ?>
                                        <option value="<?php echo $cat2['id']; ?>" <?php echo $i['category_id'] == $cat2['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat2['name']); ?></option><?php endwhile; ?>
                                </select></div>
                            <div class="field"><label>Location</label><input name="location"
                                    value="<?php echo htmlspecialchars($i['location']); ?>"></div>
                            <div class="field"><label>Date</label><input type="date" name="date_lost_found"
                                    value="<?php echo htmlspecialchars($i['date_lost_found']); ?>"></div>
                            <div class="field"><label>Description</label><textarea
                                    name="description"><?php echo htmlspecialchars($i['description']); ?></textarea></div>
                            <div class="field"><label>Contact</label><input name="contact_info"
                                    value="<?php echo htmlspecialchars($i['contact_info']); ?>"></div>
                            <div class="field"><label>Status</label><select
                                    name="status"><?php foreach (['Open', 'Claimed', 'Found', 'Returned', 'Closed'] as $st): ?>
                                        <option <?php echo $i['status'] === $st ? 'selected' : ''; ?>><?php echo $st; ?></option>
                                    <?php endforeach; ?>
                                </select></div>
                            <div class="field"><label>Replace Image</label><input type="file" name="image"
                                    accept=".jpg,.jpeg,.png,.webp"></div>
                            <button class="btn small">Update</button>
                        </form>
                    </details>
                    <form action="../Controller/adminItem.php" method="post" style="margin-top:8px"><input type="hidden"
                            name="action" value="delete"><input type="hidden" name="id"
                            value="<?php echo $i['id']; ?>"><button class="btn danger small"
                            onclick="return confirm('Delete this item?')">Delete</button></form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php include "footer.php"; ?>