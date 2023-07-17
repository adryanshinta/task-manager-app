<?php
session_start();
session_unset();
session_destroy();
header("Location: /task-manager-app/index.php");
exit();
?>
