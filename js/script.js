document.addEventListener('DOMContentLoaded', () => {
  // Get the task form element
  const taskForm = document.getElementById('task-form');

  // Add event listener for form submission
  taskForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const taskInput = document.getElementById('task-input');
    const task = taskInput.value.trim();

    if (task !== '') {
      addTask(task);
      taskInput.value = '';
    }
  });

  // Add event listener for task deletion
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('delete-task')) {
      const taskId = e.target.getAttribute('data-task-id');
      deleteTask(taskId);
    }
  });

  // Function to add a new task
  function addTask(task) {
    const xhr = new XMLHttpRequest();
    const url = 'add_task.php';
    const params = `task=${task}`;

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4 && xhr.status === 200) {
        const response = JSON.parse(xhr.responseText);
        if (response.success) {
          const taskList = document.getElementById('task-list');
          const newTask = createTaskElement(response.task_id, task);
          taskList.appendChild(newTask);
        } else {
          console.log(response.message);
        }
      }
    };

    xhr.send(params);
  }

  // Function to delete a task
  function deleteTask(taskId) {
    const xhr = new XMLHttpRequest();
    const url = 'delete_task.php';
    const params = `task_id=${taskId}`;

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4 && xhr.status === 200) {
        const response = JSON.parse(xhr.responseText);
        if (response.success) {
          const taskElement = document.querySelector(`[data-task-id="${taskId}"]`);
          if (taskElement) {
            taskElement.remove();
          }
        } else {
          console.log(response.message);
        }
      }
    };

    xhr.send(params);
  }

  // Function to create a task element
  function createTaskElement(taskId, taskName) {
    const li = document.createElement('li');
    li.setAttribute('data-task-id', taskId);

    const taskContent = document.createElement('span');
    taskContent.textContent = taskName;

    const deleteButton = document.createElement('button');
    deleteButton.textContent = 'Delete';
    deleteButton.classList.add('delete-task');
    deleteButton.setAttribute('data-task-id', taskId);

    li.appendChild(taskContent);
    li.appendChild(deleteButton);

    return li;
  }
});
