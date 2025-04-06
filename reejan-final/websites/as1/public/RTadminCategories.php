<?php
session_start();
require 'RTdb.php';

// 🔐 Only allow access if admin
if (!isset($_SESSION['RTRole']) || $_SESSION['RTRole'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Fetch all categories
$RTStmt = $RTPdo->query('SELECT * FROM category ORDER BY name');
$RTCategories = $RTStmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>RT Admin - Category Management</title>
    <link rel="stylesheet" href="carbuy.css">
    <style>
        .rt-admin-container {
            width: 80%;
            margin: 5vw auto;
            background-color: white;
            padding: 2em;
            border-radius: 1em;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .rt-admin-container h1 {
            color: #3665f3;
            text-align: center;
            margin-bottom: 2em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1em;
        }

        th, td {
            padding: 1em;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f6f6f6;
        }

        .rt-btn {
            padding: 0.5em 1em;
            background-color: #3665f3;
            color: white;
            text-decoration: none;
            border-radius: 0.5em;
            font-weight: bold;
        }

        .rt-btn:hover {
            background-color: #274dc1;
        }

        .rt-action-links a {
            margin-right: 1em;
            color: #3665f3;
            font-weight: bold;
        }

        .rt-action-links a:hover {
            text-decoration: underline;
        }

        .rt-top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
    <main>
        <div class="rt-admin-container">
            <div class="rt-top-bar">
                <h1>RT Admin - Manage Categories</h1>
                <a href="RTaddCategory.php" class="rt-btn">➕ Add Category</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($RTCategories as $RTCategory): ?>
                        <tr>
                            <td><?= htmlspecialchars($RTCategory['name']) ?></td>
                            <td class="rt-action-links">
                                <a href="RTeditCategory.php?id=<?= $RTCategory['id'] ?>">Edit</a>
                                <a href="RTdeleteCategory.php?id=<?= $RTCategory['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="rt-back">
                <a href="RTadminDashboard.php">← Back to Dashboard</a>
            </div>
        </div>
    </main>
</body>
</html>
