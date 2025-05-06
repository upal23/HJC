<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'blogging_platform';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add user functionality
if (isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Validation
    if (empty($name) || empty($email)) {
        $error = "Name and email are required!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $email);
        if ($stmt->execute()) {
            $success = "User added successfully!";
        } else {
            $error = "Failed to add user!";
        }
    }
}

// Fetch users for display
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// Fetch posts for display
$sql_posts = "SELECT * FROM posts";
$posts_result = $conn->query($sql_posts);

// Fetch settings
$sql_settings = "SELECT * FROM settings";
$settings_result = $conn->query($sql_settings);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Panel - Blogging Platform</title>
  <style>
    /* Your existing styles here */
  </style>
</head>
<body>
  <header>
    <h1>Admin Panel - Blogging Platform</h1>
  </header>
  <div class="container">
    <nav>
      <a href="#" onclick="showPanel('users')">Users</a>
      <a href="#" onclick="showPanel('content')">Posts</a>
      <a href="#" onclick="showPanel('settings')">Settings</a>
    </nav>
    <main>
      <section id="users" class="panel active">
        <h2>User Management</h2>
        <?php if (isset($error)): ?>
          <div style="color: red;"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
          <div style="color: green;"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST">
          <input type="text" name="name" placeholder="Name" required />
          <input type="email" name="email" placeholder="Email" required />
          <button type="submit" name="add_user">Add User</button>
        </form>
        <table id="userTable">
          <thead>
            <tr><th>Name</th><th>Email</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo $user['name']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><button>Delete</button></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>
      <section id="content" class="panel">
        <h2>Content Moderation</h2>
        <table>
          <thead>
            <tr><th>Title</th><th>Status</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php while ($post = $posts_result->fetch_assoc()): ?>
              <tr>
                <td><?php echo $post['title']; ?></td>
                <td><?php echo $post['status']; ?></td>
                <td><button>Edit</button></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>
      <section id="settings" class="panel">
        <h2>System Settings</h2>
        <?php while ($setting = $settings_result->fetch_assoc()): ?>
          <label>
            <input type="checkbox" <?php echo ($setting['value'] === '1' ? 'checked' : ''); ?> /> 
            <?php echo $setting['name']; ?>
          </label>
        <?php endwhile; ?>
        <button onclick="alert('Settings saved')">Save</button>
      </section>
    </main>
  </div>

  <script>
    function showPanel(id) {
      document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
      document.getElementById(id).classList.add('active');
    }
    function filterTable(e, tableId) {
      const term = e.target.value.toLowerCase();
      const rows = document.querySelectorAll(`#${tableId} tbody tr`);
      rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
      });
    }
  </script>
</body>
</html>

<?php $conn->close(); ?>
