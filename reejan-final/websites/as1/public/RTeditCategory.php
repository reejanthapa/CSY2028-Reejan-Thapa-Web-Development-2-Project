<?php
session_start();
require 'RTdb.php';

if (!isset($_SESSION['RTRole']) || $_SESSION['RTRole'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$RTCategoryId = $_GET['id'] ?? null;
$RTCategoryName = '';
$RTSuccessMessage = '';
$RTErrorMessage = '';

if ($RTCategoryId) {
    $RTStmt = $RTPdo->prepare('SELECT * FROM category WHERE id = ?');
    $RTStmt->execute([$RTCategoryId]);
    $RTCategory = $RTStmt->fetch();

    if (!$RTCategory) {
        $RTErrorMessage = '❌ Category not found.';
    } else {
        $RTCategoryName = $RTCategory['name'];
    }
} else {
    $RTErrorMessage = '❌ No category ID provided.';
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $RTUpdatedName = trim($_POST['RTCategoryName']);

    if (!empty($RTUpdatedName)) {
        $RTUpdate = $RTPdo->prepare('UPDATE category SET name = ? WHERE id = ?');
        $RTUpdate->execute([$RTUpdatedName, $RTCategoryId]);
        $RTSuccessMessage = '✅ Category updated successfully!';
        $RTCategoryName = $RTUpdatedName;
    } else {
        $RTErrorMessage = '❌ Please enter a category name.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
    <link rel="stylesheet" href="carbuy.css">
    <style>
        .rt-editcat-container {
            width: 60%;
            margin: 5vw auto;
            background-color: white;
            padding: 2em;
            border-radius: 1em;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #3665f3;
            margin-bottom: 2em;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1.5em;
        }

        label {
            font-weight: bold;
        }

        input[type="text"] {
            padding: 0.8em;
            border-radius: 0.5em;
            border: 1px solid #ccc;
            font-size: 1em;
        }

        input[type="submit"] {
            background-color: #3665f3;
            color: white;
            padding: 0.8em;
            font-size: 1em;
            border: none;
            border-radius: 0.5em;
            cursor: pointer;
            width: 50%;
            align-self: center;
        }

        input[type="submit"]:hover {
            background-color: #274dc1;
        }

        .rt-message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 1em;
        }

        .rt-success {
            color: green;
        }

        .rt-error {
            color: red;
        }

        .rt-back {
            text-align: center;
            margin-top: 2em;
        }

        .rt-back a {
            color: #3665f3;
            font-weight: bold;
            text-decoration: none;
        }

        .rt-back a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main>
        <div class="rt-editcat-container">
            <h1>✏️ Edit Category</h1>

            <?php if ($RTSuccessMessage): ?>
                <p class="rt-message rt-success"><?= $RTSuccessMessage ?></p>
            <?php endif; ?>

            <?php if ($RTErrorMessage): ?>
                <p class="rt-message rt-error"><?= $RTErrorMessage ?></p>
            <?php endif; ?>

            <?php if (!$RTErrorMessage): ?>
                <form method="POST" action="RTeditCategory.php?id=<?= htmlspecialchars($RTCategoryId) ?>">
                    <label for="RTCategoryName">Category Name:</label>
                    <input type="text" name="RTCategoryName" id="RTCategoryName" value="<?= htmlspecialchars($RTCategoryName) ?>" required>
                    <input type="submit" value="Update Category">
                </form>
            <?php endif; ?>

            <div class="rt-back">
                <a href="RTadminCategories.php">← Back to Category List</a>
            </div>
        </div>
    </main>
</body>
</html>
