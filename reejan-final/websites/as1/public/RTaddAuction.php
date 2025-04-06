<?php
session_start();
require 'RTdb.php';

// 🔐 Only allow logged-in users
if (!isset($_SESSION['RTUserId'])) {
    header('Location: RTlogin.php');
    exit;
}

$RTSuccessMessage = '';
$RTErrorMessage = '';

// Get categories for dropdown
$RTCatStmt = $RTPdo->query('SELECT * FROM category ORDER BY name');
$RTCategories = $RTCatStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $RTTitle = trim($_POST['RTTitle']);
    $RTCategoryId = $_POST['RTCategoryId'];
    $RTDescription = trim($_POST['RTDescription']);
    $RTCurrentBid = floatval($_POST['RTCurrentBid']);
    $RTEndTime = $_POST['RTEndTime'];

    $RTImagePath = '';

    // ✅ Handle file upload
    if (isset($_FILES['RTImage']) && $_FILES['RTImage']['error'] === UPLOAD_ERR_OK) {
        $RTTmpPath = $_FILES['RTImage']['tmp_name'];
        $RTFileName = basename($_FILES['RTImage']['name']);
        $RTTargetPath = 'uploads/' . time() . '_' . $RTFileName;

        if (move_uploaded_file($RTTmpPath, $RTTargetPath)) {
            $RTImagePath = $RTTargetPath;
        } else {
            $RTErrorMessage = '❌ Failed to upload image.';
        }
    }

    if (!empty($RTTitle) && !empty($RTCategoryId) && !empty($RTDescription) && $RTCurrentBid > 0 && !empty($RTEndTime)) {
        $RTStmt = $RTPdo->prepare('
            INSERT INTO auction (title, description, category_id, user_id, current_bid, end_time, image)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        $RTStmt->execute([$RTTitle, $RTDescription, $RTCategoryId, $_SESSION['RTUserId'], $RTCurrentBid, $RTEndTime, $RTImagePath]);

        $RTSuccessMessage = '✅ Car listing added successfully!';
    } else {
        $RTErrorMessage = '❌ Please fill in all fields correctly.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Car Listing</title>
    <link rel="stylesheet" href="carbuy.css">
    <style>
        .rt-add-container {
            width: 70%;
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

        input, textarea, select {
            padding: 0.8em;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 0.5em;
        }

        input[type="submit"] {
            background-color: #3665f3;
            color: white;
            width: 50%;
            align-self: center;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #274dc1;
        }

        .rt-message {
            text-align: center;
            font-weight: bold;
        }

        .rt-success {
            color: green;
        }

        .rt-error {
            color: red;
        }

        .rt-cancel-btn {
            display: inline-block;
            text-align: center;
            padding: 0.8em 2em;
            font-size: 1em;
            border: none;
            border-radius: 0.5em;
            background-color: #aaa;
            color: white;
            text-decoration: none;
            line-height: 2.2em;
        }

        .rt-cancel-btn:hover {
            background-color: #888;
        }

        .authLink {
            background-color: #3665f3;
            color: white !important;
            padding: 0.3vw 1vw;
            margin: 0 0.5vw;
            border-radius: 0.5em;
            font-weight: bold;
            display: inline-block;
            text-align: center;
        }

        .authLink:hover {
            background-color: #274dc1;
            text-decoration: none;
        }

        nav ul {
            display: flex;
            flex-wrap: wrap;
            list-style-type: none;
            align-items: center;
        }

        nav ul li {
            margin: 0 0.7em;
            position: relative;
        }

    </style>
</head>
<body>

<main>
    <div class="rt-add-container">
        <h1>➕ Add New Car Listing</h1>

        <?php if ($RTSuccessMessage): ?>
            <p class="rt-message rt-success"><?= $RTSuccessMessage ?></p>
        <?php endif; ?>

        <?php if ($RTErrorMessage): ?>
            <p class="rt-message rt-error"><?= $RTErrorMessage ?></p>
        <?php endif; ?>

        <form method="POST" action="RTaddAuction.php" enctype="multipart/form-data">
            <label for="RTTitle">Title:</label>
            <input type="text" name="RTTitle" id="RTTitle" required>

            <label for="RTCategoryId">Category:</label>
            <select name="RTCategoryId" id="RTCategoryId" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($RTCategories as $cat): ?>
                    <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="RTDescription">Description:</label>
            <textarea name="RTDescription" id="RTDescription" required></textarea>

            <label for="RTCurrentBid">Starting Bid (£):</label>
            <input type="number" name="RTCurrentBid" step="0.01" required>

            <label for="RTEndTime">Auction End Time:</label>
            <input type="datetime-local" name="RTEndTime" required>

            <label for="RTImage">Upload Car Image:</label>
            <input type="file" name="RTImage" id="RTImage" accept="image/*" required>

            <div style="display: flex; justify-content: center; gap: 1em;">
                <input type="submit" value="Add Listing">
                <a href="index.php" class="rt-cancel-btn">Cancel</a>
            </div>
        </form>
    </div>

    <?php include 'RTfooter.php'; ?>
</main>
</body>
</html>
