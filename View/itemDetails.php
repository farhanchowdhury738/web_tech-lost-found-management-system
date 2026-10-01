<?php
session_start();
include "../Model/DatabaseConnection.php";
$database = new DatabaseConnection();
$connection = $database->openConnection();
$item = $database->getItemById($connection, $_GET["id"] ?? 0);
include "header.php";
if (!$item) { echo '<div class="fail">Item not found.</div>'; include "footer.php"; exit(); }
?>
<div class="page-intro"><div><span class="eyebrow">Item details</span><p class="muted" style="margin-top:8px">Review the report information before taking action.</p></div><a class="btn secondary" href="javascript:history.back()">← Back</a></div>
<div class="detail-card">
  <div class="detail-media"><?php if ($item["image_path"]): ?><img src="<?php echo htmlspecialchars($item["image_path"]); ?>" alt="<?php echo htmlspecialchars($item["title"]); ?>"><?php else: ?><div class="no-image">No image available</div><?php endif; ?></div>
  <div class="detail-info">
    <div><span class="badge"><?php echo htmlspecialchars($item["type"]); ?></span> <span class="status-pill <?php echo strtolower($item["status"]); ?>"><?php echo htmlspecialchars($item["status"]); ?></span></div>
    <h1><?php echo htmlspecialchars($item["title"]); ?></h1>
    <div class="detail-list">
      <div class="detail-row"><strong>Category</strong><span><?php echo htmlspecialchars($item["category_name"]); ?></span></div>
      <div class="detail-row"><strong>Location</strong><span><?php echo htmlspecialchars($item["location"]); ?></span></div>
      <div class="detail-row"><strong>Date</strong><span><?php echo htmlspecialchars($item["date_lost_found"]); ?></span></div>
      <div class="detail-row"><strong>Reported by</strong><span><?php echo htmlspecialchars($item["reporter_name"]); ?></span></div>
      <div class="detail-row"><strong>Contact</strong><span><?php echo htmlspecialchars($item["contact_info"]); ?></span></div>
    </div>
    <div class="description-box"><h3>Description</h3><p style="margin-bottom:0"><?php echo nl2br(htmlspecialchars($item["description"])); ?></p></div>
    <?php if ($item["type"] === "Found" && ($_SESSION["isLoggedIn"] ?? false) && $item["status"] === "Open" && (int)$item["user_id"] !== (int)$_SESSION["loggedInUserId"]): ?><a class="btn" href="claimItem.php?id=<?php echo $item["id"]; ?>">Claim This Item →</a><?php endif; ?>
  </div>
</div>
<?php include "footer.php"; ?>
