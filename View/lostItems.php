<?php
session_start();
include "../Model/DatabaseConnection.php";
$database = new DatabaseConnection();
$connection = $database->openConnection();
$search = $_GET["search"] ?? "";
$category = $_GET["category"] ?? "";
$items = $database->getItems($connection, "Lost", $search, $category);
$categories = $database->getCategories($connection);
include "header.php";
?>
<div class="page-intro"><div><span class="eyebrow">Browse reports</span><h1>Lost Items</h1><p class="muted">Search items reported missing around campus.</p></div><?php if ($_SESSION["isLoggedIn"] ?? false): ?><a href="reportItem.php" class="btn">+ Report Lost Item</a><?php endif; ?></div>
<div class="search-panel">
  <form method="get" class="search-form">
    <div class="field"><label>Search item</label><input type="text" name="search" placeholder="e.g. wallet, phone, calculator" value="<?php echo htmlspecialchars($search); ?>"></div>
    <div class="field"><label>Category</label><select name="category"><option value="">All Categories</option><?php while ($cat = $categories->fetch_assoc()): ?><option value="<?php echo $cat["id"]; ?>" <?php if ($category == $cat["id"]) echo "selected"; ?>><?php echo htmlspecialchars($cat["name"]); ?></option><?php endwhile; ?></select></div>
    <button class="btn" type="submit">Search</button>
  </form>
</div>
<div class="result-head"><h2>Latest lost reports</h2><span class="result-count"><?php echo ($items ? $items->num_rows : 0); ?> item(s)</span></div>
<div class="item-grid">
<?php if ($items && $items->num_rows > 0): while ($item = $items->fetch_assoc()): ?>
  <article class="item-card">
    <div class="item-card-media">
      <?php if ($item["image_path"]): ?><img src="<?php echo htmlspecialchars($item["image_path"]); ?>" alt="<?php echo htmlspecialchars($item["title"]); ?>"><?php else: ?><div class="no-image">No image available</div><?php endif; ?>
      <span class="item-type">LOST</span>
    </div>
    <div class="item-card-body">
      <a class="item-card-title" href="itemDetails.php?id=<?php echo $item["id"]; ?>"><?php echo htmlspecialchars($item["title"]); ?></a>
      <span class="badge"><?php echo htmlspecialchars($item["category_name"]); ?></span>
      <div class="item-card-meta"><span>📍 <?php echo htmlspecialchars($item["location"]); ?></span><span>📅 <?php echo htmlspecialchars($item["date_lost_found"]); ?></span></div>
      <div class="item-card-footer"><span class="status-pill <?php echo strtolower($item["status"]); ?>"><?php echo htmlspecialchars($item["status"]); ?></span><a class="item-card-action" href="itemDetails.php?id=<?php echo $item["id"]; ?>">View details →</a></div>
    </div>
  </article>
<?php endwhile; else: ?><div class="card" style="grid-column:1/-1;text-align:center;padding:50px"><div class="feature-icon" style="margin:0 auto 12px">⌕</div><h3>No lost items found</h3><p class="muted">Try another search or category.</p></div><?php endif; ?>
</div>
<?php include "footer.php"; ?>
