<?php
session_start();
require 'RTdb.php';

// 🔐 Only admins allowed
if (!isset($_SESSION['RTRole']) || $_SESSION['RTRole'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$RTCategoryId = $_GET['id'] ?? null;
$RTCategoryName = '';
$RTError = '';
$RTSuccess = '';

// Get category name for confirmation
if ($RTCategoryId) {
    $RTStmt = $RTPdo->prepare('SELECT * FROM category WHERE id = ?');
    $RTStmt->execute([$RTCategoryId]);
    $RTCategory = $RTStmt->fetch();

    if ($RTCategory) {
        $RTCategoryName = $RTCategory['name'];
    } else {
        $RTError = '❌ Category not found.';
    }
} else {
    $RTError = '❌ No category selected.';
}

// If confirmed via POST, delete the category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $RTDelete = $RTPdo->prepare('DELETE FROM category WHERE id = ?');
    $RTDelete->execute([$RTCategoryId]);

    // Redirect to categories page after delete
    header('Location: RTadminCategories.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Category</title>
    <link rel="stylesheet" href="carbuy.css">
    <style>
        .rt-deletecat-container {
            width: 60%;
            margin: 5vw auto;
            background-color: white;
            padding: 2em;
            border-radius: 1em;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        h1 {
            color: #e43137;
            margin-bottom: 1.5em;
        }

        .rt-warning {
            color: #e43137;
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 2em;
        }

        form {
            display: flex;
            justify-content: center;
            gap: 2em;
        }

        .rt-btn {
            padding: 0.7em 1.5em;
            font-size: 1em;
            font-weight: bold;
            border: none;
            border-radius: 0.5em;
            cursor: pointer;
            text-decoration: none;
        }

        .rt-btn-delete {
            background-color: #e43137;
            color: white;
        }

        .rt-btn-delete:hover {
            background-color: #c11e26;
        }

        .rt-btn-cancel {
            background-color: #aaa;
            color: white;
        }

        .rt-btn-cancel:hover {
            background-color: #888;
        }

        .rt-error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <main>
        <div class="rt-deletecat-container">
            <?php if ($RTError): ?>
                <p class="rt-error"><?= $RTError ?></p>
                <a href="RTadminCategories.php" class="rt-btn rt-btn-cancel">← Back</a>
            <?php else: ?>
                <h1>Delete Category</h1>
                <p class="rt-warning">Are you sure you want to delete "<strong><?= htmlspecialchars($RTCategoryName) ?></strong>"?</p>

                <form method="POST" action="RTdeleteCategory.php?id=<?= htmlspecialchars($RTCategoryId) ?>">
                    <input type="submit" value="Yes, Delete" class="rt-btn rt-btn-delete">
                    <a href="RTadminCategories.php" class="rt-btn rt-btn-cancel">Cancel</a>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
