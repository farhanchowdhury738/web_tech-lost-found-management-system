<?php
session_start();
include "../Model/DatabaseConnection.php";
$database = new DatabaseConnection();
$connection = $database->openConnection();
$search = $_POST["search"] ?? "";
$category = $_POST["category"] ?? "";
$items = $database->getItems($connection, "Found", $search, $category);
$categories = $database->getCategories($connection);
$claimError = $_SESSION["claimError"] ?? "";
unset($_SESSION["claimError"]);
include "header.php";
?>
<div class="page-intro"><div><span class="eyebrow">Community reports</span><h1>Found Items</h1><p class="muted">Browse belongings found by students and staff around campus.</p></div><?php if ($_SESSION["isLoggedIn"] ?? false): ?><a href="reportItem.php" class="btn">+ Report Found Item</a><?php endif; ?></div>
<?php if ($claimError): ?><div class="fail"><?php echo htmlspecialchars($claimError); ?></div><?php endif; ?>
<div class="search-panel">
  <form method="post" class="search-form">
    <div class="field"><label>Search item</label><input type="text" name="search" placeholder="e.g. wallet, phone, ID card" value="<?php echo htmlspecialchars($search); ?>"></div>
    <div class="field"><label>Category</label><select name="category"><option value="">All Categories</option><?php while ($cat = $categories->fetch_assoc()): ?><option value="<?php echo $cat["id"]; ?>" <?php if ($category == $cat["id"]) echo "selected"; ?>><?php echo htmlspecialchars($cat["name"]); ?></option><?php endwhile; ?></select></div>
    <button class="btn" type="submit">Search</button>
  </form>
</div>
<div class="result-head"><h2>Latest found reports</h2><span class="result-count"><?php echo ($items ? $items->num_rows : 0); ?> item(s)</span></div>
<div class="item-grid">
<?php if ($items && $items->num_rows > 0): while ($item = $items->fetch_assoc()): ?>
  <article class="item-card">
    <div class="item-card-media">
      <?php if ($item["image_path"]): ?><img src="<?php echo htmlspecialchars($item["image_path"]); ?>" alt="<?php echo htmlspecialchars($item["title"]); ?>"><?php else: ?><div class="no-image">No image available</div><?php endif; ?>
      <span class="item-type found">FOUND</span>
    </div>
    <div class="item-card-body">
      <a class="item-card-title" href="itemDetails.php?id=<?php echo $item["id"]; ?>"><?php echo htmlspecialchars($item["title"]); ?></a>
      <span class="badge"><?php echo htmlspecialchars($item["category_name"]); ?></span>
      <div class="item-card-meta"><span>📍 <?php echo htmlspecialchars($item["location"]); ?></span><span>📅 <?php echo htmlspecialchars($item["date_lost_found"]); ?></span></div>
      <div class="item-card-footer"><span class="status-pill <?php echo strtolower($item["status"]); ?>"><?php echo htmlspecialchars($item["status"]); ?></span><span style="display:flex;gap:10px"><a class="item-card-action" href="itemDetails.php?id=<?php echo $item["id"]; ?>">Details →</a><?php if (($_SESSION["isLoggedIn"] ?? false) && $item["status"] === "Open" && (int)$item["user_id"] !== (int)$_SESSION["loggedInUserId"]): ?><a class="item-card-action" href="claimItem.php?id=<?php echo $item["id"]; ?>">Claim</a><?php endif; ?></span></div>
    </div>
  </article>
<?php endwhile; else: ?><div class="card" style="grid-column:1/-1;text-align:center;padding:50px"><div class="feature-icon" style="margin:0 auto 12px">✓</div><h3>No found items found</h3><p class="muted">Try another search or category.</p></div><?php endif; ?>
</div>
<?php include "footer.php"; ?>
