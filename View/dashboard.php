<?php
session_start();
if (!($_SESSION["isLoggedIn"] ?? false)) { header("Location: login.php"); exit(); }
include "header.php";
?>
<div class="page-intro"><div><span class="eyebrow">My workspace</span><h1>Welcome back, <?php echo htmlspecialchars($_SESSION["loggedInUsername"]); ?>.</h1><p class="muted">Manage your reports and claims from one simple dashboard.</p></div><a class="btn" href="reportItem.php">+ Report an Item</a></div>
<div class="admin-dashboard-grid" style="grid-template-columns:repeat(3,1fr)">
  <a href="reportItem.php"><button type="button" class="admin-button"><span style="font-size:32px">＋</span>Report an Item<small>Tell the campus what you lost or found.</small></button></a>
  <a href="myItems.php"><button type="button" class="admin-button"><span style="font-size:32px">▣</span>My Reported Items<small>View and track your submitted reports.</small></button></a>
  <a href="myClaims.php"><button type="button" class="admin-button"><span style="font-size:32px">✓</span>My Claims<small>Check the status of your claims.</small></button></a>
</div>
<?php include "footer.php"; ?>
