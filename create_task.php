<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

// Include config file
require_once 'includes/config.php';

// Validate task name
if (empty(trim($_POST['task_name']))) {
  $response = array('success' => false, 'error' => 'Please enter a task name.');
} else {
  // Insert new task into the database
  $sql = 'INSERT INTO tasks (user_id, task_name) VALUES (?, ?)';
  if ($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(1, $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(2, $_POST['task_name'], PDO::PARAM_STR);
    if ($stmt->execute()) {
      $response = array('success' => true, 'task_id' => $pdo->lastInsertId());
    } else {
      $response = array('success' => false, 'error' => 'Something went wrong. Please try again later.');
    }
  } else {
    $response = array('success' => false, 'error' => 'Something went wrong. Please try again later.');
  }
}

// Close connection
unset($pdo);

// Return JSON response
echo json_encode($response);
