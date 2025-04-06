<?php
session_start();
require 'RTdb.php';

// 🔐 Only for logged-in users
if (!isset($_SESSION['RTUserId']) || $_SESSION['RTRole'] !== 'user') {
    header('Location: RTlogin.php');
    exit;
}

$RTUserId = $_SESSION['RTUserId'];

// ✅ Fetch user's auctions
$RTStmt = $RTPdo->prepare("
    SELECT auction.*, category.name AS category_name
    FROM auction
    JOIN category ON auction.category_id = category.id
    WHERE auction.user_id = ?
    ORDER BY auction.id DESC
");
$RTStmt->execute([$RTUserId]);
$RTMyAuctions = $RTStmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Listings - RT Auctions</title>
    <link rel="stylesheet" href="carbuy.css">
    <style>
        main {
            max-width: 1000px;
            margin: auto;
            padding: 2em;
        }

        h1 {
            text-align: center;
            color: #3665f3;
            margin-bottom: 2em;
        }

        .rt-listing {
            border: 1px solid #ccc;
            border-radius: 0.5em;
            padding: 1.5em;
            margin-bottom: 1.5em;
            background-color: #fff;
        }

        .rt-listing h2 {
            margin-top: 0;
        }

        .rt-buttons {
            margin-top: 1em;
        }

        .rt-buttons a {
            display: inline-block;
            margin-right: 1em;
            padding: 0.5em 1.2em;
            border-radius: 0.5em;
            color: white;
            background-color: #3665f3;
            text-decoration: none;
        }

        .rt-buttons a.delete {
            background-color: #e43137;
        }

        .rt-buttons a:hover {
            opacity: 0.85;
        }

        .rt-empty {
            text-align: center;
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>
    <main>
        <h1>📄 My Auction Listings</h1>

        <?php if (count($RTMyAuctions) > 0): ?>
            <?php foreach ($RTMyAuctions as $auction): ?>
                <div class="rt-listing">
                    <h2><?= htmlspecialchars($auction['title']) ?></h2>
                    <p><strong>Category:</strong> <?= htmlspecialchars($auction['category_name']) ?></p>
                    <p><strong>Current Bid:</strong> £<?= number_format($auction['current_bid'], 2) ?></p>
                    <p><strong>Ends:</strong> <?= date('d M Y, H:i', strtotime($auction['end_time'])) ?></p>

                    <div class="rt-buttons">
                        <a href="RTeditAuction.php?id=<?= $auction['id'] ?>">✏️ Edit</a>
                        <a href="RTdeleteAuction.php?id=<?= $auction['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this auction?');">🗑️ Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="rt-empty">You haven’t listed any auctions yet.</p>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 2em;">
            <a href="index.php" class="authLink">← Back to Home</a>
        </div>
    </main>
</body>
</html>
