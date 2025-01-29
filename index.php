<?php
include('config.php'); // Include the database connection

// Handle adding a new task
if (isset($_POST['add_task'])) {
    $task = mysqli_real_escape_string($db, $_POST['task']);
    $query = "INSERT INTO tasks (task) VALUES ('$task')";
    mysqli_query($db, $query);
}

// Handle deleting a task
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    $query = "DELETE FROM tasks WHERE id = $task_id";
    mysqli_query($db, $query);
}

// Handle updating task status to completed
if (isset($_GET['complete'])) {
    $task_id = $_GET['complete'];
    $query = "UPDATE tasks SET status = 'completed' WHERE id = $task_id";
    mysqli_query($db, $query);
}

// Handle editing task
if (isset($_POST['edit_task'])) {
    $task_id = $_POST['task_id'];
    $task = mysqli_real_escape_string($db, $_POST['task']);
    $query = "UPDATE tasks SET task = '$task' WHERE id = $task_id";
    mysqli_query($db, $query);
}

// Fetch all tasks with sorting (optional sorting by status)
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$query = "SELECT * FROM tasks ORDER BY $sort DESC";
$result = mysqli_query($db, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>To-Do List</h1>

        <!-- Add Task Form -->
        <form method="POST">
            <input type="text" name="task" placeholder="Enter your task" required>
            <button type="submit" name="add_task">Add Task</button>
        </form>

        <h2>Tasks:</h2>
        <!-- Sorting options -->
        <div class="sort-options">
            <a href="index.php?sort=created_at">Sort by Date</a> | 
            <a href="index.php?sort=status">Sort by Status</a>
        </div>

        <ul>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <li class="<?php echo $row['status'] == 'completed' ? 'completed' : ''; ?>">
                    <?php echo htmlspecialchars($row['task']); ?>

                    <!-- Mark as completed -->
                    <?php if ($row['status'] == 'pending'): ?>
                        <a href="index.php?complete=<?php echo $row['id']; ?>">Complete</a>
                    <?php endif; ?>

                    <!-- Edit task -->
                    <a href="#" class="edit-task" data-task-id="<?php echo $row['id']; ?>" data-task="<?php echo htmlspecialchars($row['task']); ?>">Edit</a>

                    <!-- Delete task -->
                    <a href="index.php?delete=<?php echo $row['id']; ?>">Delete</a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <!-- Edit Task Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Task</h3>
            <form method="POST">
                <input type="hidden" name="task_id" id="task_id">
                <input type="text" name="task" id="task_text" required>
                <button type="submit" name="edit_task">Update Task</button>
                <button type="button" id="closeModal">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        // Open edit task modal
        const editLinks = document.querySelectorAll('.edit-task');
        const modal = document.getElementById('editModal');
        const closeModal = document.getElementById('closeModal');
        const taskIdInput = document.getElementById('task_id');
        const taskTextInput = document.getElementById('task_text');

        editLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                taskIdInput.value = this.dataset.taskId;
                taskTextInput.value = this.dataset.task;
                modal.style.display = 'block';
            });
        });

        // Close modal
        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    </script>
</body>
</html>
