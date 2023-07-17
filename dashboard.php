<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

// Include config file
require_once 'includes/config.php';

// Get user's tasks from the database
$user_id = $_SESSION['user_id'];

// Get incomplete tasks
$incomplete_tasks = [];
$sql = 'SELECT id, task_name FROM tasks WHERE user_id = ? AND completed = 0 ORDER BY id DESC';
if ($stmt = $pdo->prepare($sql)) {
  $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
  if ($stmt->execute()) {
    while ($row = $stmt->fetch()) {
      $incomplete_tasks[] = $row;
    }
  }
}

// Get completed tasks
$completed_tasks = [];
$sql = 'SELECT id, task_name FROM tasks WHERE user_id = ? AND completed = 1 ORDER BY id DESC';
if ($stmt = $pdo->prepare($sql)) {
  $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
  if ($stmt->execute()) {
    while ($row = $stmt->fetch()) {
      $completed_tasks[] = $row;
    }
  }
}

// Close connection
unset($pdo);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Task Manager App - Dashboard</title>
  <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
  <header>
    <div class="container">
      <h1>Task Manager App</h1>
      <a href="includes/logout.php" class="logout">Logout</a>
    </div>
  </header>
  <div class="container">
    <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>
    <div class="task-form">
      <form id="create-task-form">
        <input type="text" name="task_name" placeholder="Enter task name">
        <button type="submit">Create Task</button>
      </form>
    </div>
    <h3>Incomplete Tasks</h3>
    <ul id="incomplete-tasks">
      <?php foreach ($incomplete_tasks as $task) : ?>
        <li data-task-id="<?php echo $task['id']; ?>">
          <span><?php echo $task['task_name']; ?></span>
          <div class="task-actions">
            <button class="complete-button">Complete</button>
            <button class="delete-button">Delete</button>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <h3>Completed Tasks</h3>
    <ul id="completed-tasks">
      <?php foreach ($completed_tasks as $task) : ?>
        <li data-task-id="<?php echo $task['id']; ?>">
          <span><?php echo $task['task_name']; ?></span>
          <div class="task-actions">
            <button class="delete-button">Delete</button>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <!-- <footer>
    <div class="container">
      <p>UAS KDSI - Task Manager App &copy; 2023</p>
    </div>
  </footer> -->
  <script src="js/script.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      // Handle create task form submission
      $('#create-task-form').submit(function(e) {
        e.preventDefault();
        var taskName = $('input[name="task_name"]').val();
        $.ajax({
          url: 'create_task.php',
          type: 'POST',
          data: { task_name: taskName },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              var taskId = response.task_id;
              var taskItem = '<li data-task-id="' + taskId + '">' +
                '<span>' + taskName + '</span>' +
                '<div class="task-actions">' +
                '<button class="complete-button">Complete</button>' +
                '<button class="delete-button">Delete</button>' +
                '</div>' +
                '</li>';
              $('#incomplete-tasks').append(taskItem);
              $('input[name="task_name"]').val('');
            } else {
              alert(response.error);
            }
          }
        });
      });

      // Handle complete button click
      $(document).on('click', '.complete-button', function() {
        var taskId = $(this).closest('li').data('task-id');
        var taskName = $(this).closest('li').find('span').text();
        $.ajax({
          url: 'update_task_status.php',
          type: 'POST',
          data: { task_id: taskId, task_name: taskName },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              var completedTaskItem = '<li data-task-id="' + taskId + '">' +
                '<span>' + taskName + '</span>' +
                '<div class="task-actions">' +
                '<button class="delete-button">Delete</button>' +
                '</div>' +
                '</li>';
              $('#completed-tasks').append(completedTaskItem);
              $('li[data-task-id="' + taskId + '"]').remove();
            } else {
              alert(response.error);
            }
          }
        });
      });

      // Handle delete button click
      $(document).on('click', '.delete-button', function() {
        var taskId = $(this).closest('li').data('task-id');
        $.ajax({
          url: 'delete_task.php',
          type: 'POST',
          data: { task_id: taskId },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              $('li[data-task-id="' + taskId + '"]').remove();
            } else {
              alert(response.error);
            }
          }
        });
      });
    });
  </script>
</body>
</html>
