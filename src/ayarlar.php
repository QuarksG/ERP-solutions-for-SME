<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Admin Users</h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
        </tr>
        </thead>
        <tbody>
        <!-- Example rows -->
        <tr>
            <td>Admin User 1</td>
            <td>admin1@example.com</td>
            <td>Admin</td>
        </tr>
        <tr>
            <td>Admin User 2</td>
            <td>admin2@example.com</td>
            <td>Admin</td>
        </tr>
        <!-- Repeat rows as needed -->
        </tbody>
    </table>

    <h2>Change Password</h2>
    <form method="POST">
        <div class="form-group">
            <label for="current_password">Current Password:</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="confirm_new_password">Confirm New Password:</label>
            <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
        </div>
        <button type="submit" class="btn btn-primary" name="change_password">Change Password</button>
    </form>

    <h2>Delete Users</h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <!-- Example rows -->
        <tr>
            <td>Regular User 1</td>
            <td>user1@example.com</td>
            <td>User</td>
            <td>
                <button type="button" class="btn btn-danger">Delete</button>
            </td>
        </tr>
        <tr>
            <td>Regular User 2</td>
            <td>user2@example.com</td>
            <td>User</td>
            <td>
                <button type="button" class="btn btn-danger">Delete</button>
            </td>
        </tr>
        <!-- Repeat rows as needed -->
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
