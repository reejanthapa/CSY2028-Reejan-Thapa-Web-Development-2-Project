<?php
session_start();
require 'RTdb.php';

$RTCategoryId = $_GET['id'] ?? null;

if (!$RTCategoryId || !is_numeric($RTCategoryId)) {
    header('Location: index.php');
    exit;
}

// ✅ Get Category Name
$RTCatStmt = $RTPdo->prepare("SELECT * FROM category WHERE id = ?");
$RTCatStmt->execute([$RTCategoryId]);
$RTCategory = $RTCatStmt->fetch();

if (!$RTCategory) {
    echo "<p style='text-align:center;'>❌ Category not found.</p>";
    exit;
}

// ✅ Get Auctions in this category
$RTStmt = $RTPdo->prepare("
    SELECT auction.*, user.name AS seller_name 
    FROM auction 
    JOIN user ON auction.user_id = user.id 
    WHERE category_id = ?
    ORDER BY auction.id DESC
");
$RTStmt->execute([$RTCategoryId]);
$RTAuctions = $RTStmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($RTCategory['name']) ?> Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />
    <style>
        .authLink {
    background-color: #3665f3;
    color: white !important;
    padding: 0.5vw 1vw;
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

.rt-dropdown {
    position: relative;
}

.rt-dropdown-content {
    display: none;
    position: absolute;
    top: 2.5em;
    right: 0;
    background-color: white;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    min-width: 10em;
    z-index: 1000;
    border-radius: 0.5em;
    padding: 0.5em 0;
}

.rt-dropdown-content li {
    list-style-type: none;
}

.rt-dropdown-content li a {
    display: block;
    padding: 0.5em 1em;
    color: #444;
    text-decoration: none;
}

.rt-dropdown-content li a:hover {
    background-color: #f0f0f0;
}

.rt-dropdown:hover .rt-dropdown-content {
    display: block;
}

.categoryLink {
    padding: 0.2em 0.1em;
    display: inline-block;
    text-decoration: none;
    font-weight: bold;
    color: #000;
}

        </style>
</head>
<body>
<?php include 'RTheader.php'; ?>
    <main style="max-width: 900px; margin: auto;">
        <h1>Auctions in "<?= htmlspecialchars($RTCategory['name']) ?>"</h1>

        <ul class="carList">
            <?php if (count($RTAuctions) > 0): ?>
                <?php foreach ($RTAuctions as $auction): ?>
                    <li>
                        <img src="car.png" alt="Car image">
                        <article>
                            <h2><?= htmlspecialchars($auction['title']) ?></h2>
                            <p><?= nl2br(htmlspecialchars($auction['description'])) ?></p>
                            <p class="price">Current bid: £<?= number_format($auction['current_bid'], 2) ?></p>
                            <p>Auction created by <strong><?= htmlspecialchars($auction['seller_name']) ?></strong></p>
                            <p><em>Ends: <?= date('d M Y, H:i', strtotime($auction['end_time'])) ?></em></p>
                            <a href="RTviewAuction.php?id=<?= $auction['id'] ?>" class="more auctionLink">More &gt;&gt;</a>
                        </article>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No auctions found in this category.</p>
            <?php endif; ?>
        </ul>

        <div style="text-align:center; margin-top:2em;">
            <a href="index.php" class="authLink">← Back to Home</a>
        </div>
    </main>
    <?php include 'RTfooter.php'; ?>
</body>
</html>
