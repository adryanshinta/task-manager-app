<?php
session_start();
if (isset($_SESSION['user_id'])) {
  header("Location: dashboard.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Task Manager App</title>
  <link rel="stylesheet" type="text/css" href="css/style_index.css">
</head>
<body>
  <div class="container">
    <h1>Welcome to Task Manager App</h1>
    <a href="login.php">Login</a> <h5>or</h5> <a href="register.php">Register</a>
  </div>
</body>
</html>
