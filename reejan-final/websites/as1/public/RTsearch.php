<?php
session_start();
require 'RTdb.php';

$RTQuery = $_GET['query'] ?? '';
$RTAuctions = [];

if ($RTQuery) {
    $RTStmt = $RTPdo->prepare("
        SELECT auction.*, category.name AS category_name, user.name AS seller_name
        FROM auction
        JOIN category ON auction.category_id = category.id
        JOIN user ON auction.user_id = user.id
        WHERE auction.title LIKE ? OR auction.description LIKE ?
        ORDER BY auction.id DESC
    ");
    $RTStmt->execute(["%$RTQuery%", "%$RTQuery%"]);
    $RTAuctions = $RTStmt->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results for '<?= htmlspecialchars($RTQuery) ?>'</title>
    <link rel="stylesheet" href="carbuy.css">
</head>
<body>
    <main style="max-width: 900px; margin: auto;">
        <h1>Search Results for '<?= htmlspecialchars($RTQuery) ?>'</h1>

        <?php if (count($RTAuctions) > 0): ?>
            <ul class="carList">
                <?php foreach ($RTAuctions as $auction): ?>
                    <li>
                        <img src="car.png" alt="Car image">
                        <article>
                            <h2><?= htmlspecialchars($auction['title']) ?></h2>
                            <h3><?= htmlspecialchars($auction['category_name']) ?></h3>
                            <p><?= nl2br(htmlspecialchars($auction['description'])) ?></p>
                            <p class="price">Current bid: £<?= number_format($auction['current_bid'], 2) ?></p>
                            <p>Auction created by <strong><?= htmlspecialchars($auction['seller_name']) ?></strong></p>
                            <p><em>Ends: <?= date('d M Y, H:i', strtotime($auction['end_time'])) ?></em></p>
                            <a href="RTviewAuction.php?id=<?= $auction['id'] ?>" class="more auctionLink">More &gt;&gt;</a>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No auctions found matching your search.</p>
        <?php endif; ?>

        <div style="text-align:center; margin-top:2em;">
            <a href="index.php" class="authLink">← Back to Home</a>
        </div>
    </main>
</body>
</html>
