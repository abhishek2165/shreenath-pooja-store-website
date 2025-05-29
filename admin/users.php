<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

include '../db.php';

$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// ✅ Fetch Users with Search + Pagination
$sql = "SELECT * FROM users WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%$search%";
$stmt->bind_param('sssii', $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / $limit);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>

<h2>User Management</h2>

<!-- ✅ Search Form -->
<form method="GET">
    <input type="text" name="search" placeholder="Search users..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>

<!-- ✅ Display Users -->
<table border="1">
    <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Mobile</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) : ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['first_name']) ?></td>
            <td><?= htmlspecialchars($row['last_name']) ?></td>
            <td><?= htmlspecialchars($row['mobile']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td>
                <form action="update_user.php" method="POST">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <select name="role" onchange="this.form.submit()">
                        <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="customer" <?= $row['role'] == 'customer' ? 'selected' : '' ?>>Customer</option>
                    </select>
                </form>
            </td>
            <td>
                <form action="update_user.php" method="POST">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <select name="status" onchange="this.form.submit()">
                        <option value="active" <?= $row['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="blocked" <?= $row['status'] == 'blocked' ? 'selected' : '' ?>>Blocked</option>
                    </select>
                </form>
            </td>
            <td>
                <a href="edit_user.php?id=<?= $row['id'] ?>">Edit</a>
                <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<!-- ✅ Pagination -->
<div>
    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
        <a href="?page=<?= $i ?>&search=<?= $search ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>

</body>
</html>
