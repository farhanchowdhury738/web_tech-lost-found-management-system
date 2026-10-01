<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
$isLoggedIn = $_SESSION["isLoggedIn"] ?? false;
$role = $_SESSION["role"] ?? "user";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Khoja-Khuji · Campus Lost &amp; Found</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<nav class="nav">
  <a class="brand" href="home.php">Khoja-Khuji</a>
  <button class="nav-toggle" type="button" aria-label="Toggle navigation" onclick="document.querySelector('.navlinks').classList.toggle('open')">☰</button>
  <div class="navlinks">
    <a href="home.php">Home</a>
    <a href="lostItems.php">Lost Items</a>
    <a href="foundItems.php">Found Items</a>
    <?php if ($isLoggedIn) {
      if ($role == "admin") echo '<a href="adminDashboard.php">Dashboard</a>'; else echo '<a href="dashboard.php">Dashboard</a>';
      echo '<a href="reportItem.php">Report Item</a><a href="profile.php">Profile</a><a href="../Controller/logout.php">Logout</a>';
    } else {
      echo '<a href="login.php">Log in</a><a class="btn small" href="registration.php">Sign up</a>';
    } ?>
  </div>
</nav>
<div class="container">
<?php
$successMessage = $_SESSION["successMessage"] ?? "";
if ($successMessage != "") { echo '<p class="success-message">' . $successMessage . '</p>'; unset($_SESSION["successMessage"]); }
?>
