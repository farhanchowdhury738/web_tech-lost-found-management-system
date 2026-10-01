<?php
session_start();
include "../Controller/helpers.php";
requireAdmin();
include "header.php";
?>
<div class="page-intro"><div><span class="eyebrow">Administration</span><h1>Admin Dashboard</h1><p class="muted">Keep the campus lost and found platform organized.</p></div></div>
<div class="admin-dashboard-grid">
  <a href="adminUsers.php"><button type="button" class="admin-button"><span style="font-size:34px">♙</span>Manage Users<small>Accounts and access</small></button></a>
  <a href="adminItems.php"><button type="button" class="admin-button"><span style="font-size:34px">▣</span>Manage Items<small>Lost and found reports</small></button></a>
  <a href="adminCategories.php"><button type="button" class="admin-button"><span style="font-size:34px">◇</span>Manage Categories<small>Organize item types</small></button></a>
  <a href="adminClaims.php"><button type="button" class="admin-button"><span style="font-size:34px">✓</span>Manage Claims<small>Review claim requests</small></button></a>
</div>
<?php include "footer.php"; ?>
