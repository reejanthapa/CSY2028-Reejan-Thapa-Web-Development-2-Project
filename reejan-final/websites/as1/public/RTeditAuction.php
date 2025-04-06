<?php
session_start();
require 'RTdb.php';

if (!isset($_SESSION['RTUserId'])) {
    header('Location: RTlogin.php');
    exit;
}

$RTAuctionId = $_GET['id'] ?? null;
$RTUserId = $_SESSION['RTUserId'];

$RTSuccessMessage = '';
$RTErrorMessage = '';

if (!$RTAuctionId || !is_numeric($RTAuctionId)) {
    header('Location: index.php');
    exit;
}

$RTCatStmt = $RTPdo->query("SELECT * FROM category ORDER BY name");
$RTCategories = $RTCatStmt->fetchAll();

$RTStmt = $RTPdo->prepare("SELECT * FROM auction WHERE id = ? AND user_id = ?");
$RTStmt->execute([$RTAuctionId, $RTUserId]);
$RTAuction = $RTStmt->fetch();

if (!$RTAuction) {
    die("⛔ You are not authorized to edit this auction.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $RTTitle = trim($_POST['RTTitle']);
    $RTCategoryId = $_POST['RTCategoryId'];
    $RTDescription = trim($_POST['RTDescription']);
    $RTCurrentBid = floatval($_POST['RTCurrentBid']);
    $RTEndTime = $_POST['RTEndTime'];

    if (!empty($RTTitle) && $RTCurrentBid > 0 && !empty($RTDescription) && !empty($RTEndTime)) {
        $RTUpdate = $RTPdo->prepare("UPDATE auction SET title = ?, category_id = ?, description = ?, current_bid = ?, end_time = ? WHERE id = ? AND user_id = ?");
        $RTUpdate->execute([$RTTitle, $RTCategoryId, $RTDescription, $RTCurrentBid, $RTEndTime, $RTAuctionId, $RTUserId]);

        $RTSuccessMessage = "✅ Auction updated successfully!";
        $RTAuction['title'] = $RTTitle;
        $RTAuction['category_id'] = $RTCategoryId;
        $RTAuction['description'] = $RTDescription;
        $RTAuction['current_bid'] = $RTCurrentBid;
        $RTAuction['end_time'] = $RTEndTime;
    } else {
        $RTErrorMessage = "❌ Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Auction</title>
    <link rel="stylesheet" href="carbuy.css">
    <style>
        .rt-container { width: 700%; margin: 5vw auto; background: #fff; padding: 2em; border-radius: 1em; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .rt-message { text-align: center; font-weight: bold; }
        .rt-success { color: green; }
        .rt-error { color: red; }
        form { display: flex; flex-direction: column; gap: 1.5em; }
        label { font-weight: bold; }
        input, textarea, select { padding: 0.8em; border: 1px solid #ccc; border-radius: 0.5em; }
        .btn-row { display: flex; justify-content: center; gap: 1em; }
        .btn { padding: 0.8em 2em; border: none; border-radius: 0.5em; cursor: pointer; }
        .btn-update { background: #3665f3; color: white; }
        .btn-cancel { background: #aaa; color: white; text-decoration: none; display: inline-block; line-height: 2.2em; }
        .btn-update:hover { background: #274dc1; }
        .btn-cancel:hover { background: #888; }
    </style>
</head>
<body>
<main>
    <div class="rt-container">
        <h1>📝 Edit Auction</h1>

        <?php if ($RTSuccessMessage): ?>
            <p class="rt-message rt-success"><?= $RTSuccessMessage ?></p>
        <?php elseif ($RTErrorMessage): ?>
            <p class="rt-message rt-error"><?= $RTErrorMessage ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="RTTitle">Title:</label>
            <input type="text" name="RTTitle" value="<?= htmlspecialchars($RTAuction['title']) ?>" required>

            <label for="RTCategoryId">Category:</label>
            <select name="RTCategoryId" required>
                <?php foreach ($RTCategories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $RTAuction['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="RTDescription">Description:</label>
            <textarea name="RTDescription" required><?= htmlspecialchars($RTAuction['description']) ?></textarea>

            <label for="RTCurrentBid">Current Bid (£):</label>
            <input type="number" name="RTCurrentBid" step="0.01" value="<?= htmlspecialchars($RTAuction['current_bid']) ?>" required>

            <label for="RTEndTime">End Time:</label>
            <input type="datetime-local" name="RTEndTime" value="<?= date('Y-m-d\TH:i', strtotime($RTAuction['end_time'])) ?>" required>

            <div class="btn-row">
                <input type="submit" class="btn btn-update" value="Update">
                <a href="index.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</main>
</body>
</html>
