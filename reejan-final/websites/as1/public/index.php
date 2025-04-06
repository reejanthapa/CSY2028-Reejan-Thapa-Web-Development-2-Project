<?php
session_start();
require 'RTdb.php';

// ✅ Fetch all categories
$RTCatStmt = $RTPdo->query('SELECT * FROM category ORDER BY name');
$RTCategories = $RTCatStmt->fetchAll();

$RTMainCategories = array_slice($RTCategories, 0, 6);
$RTExtraCategories = array_slice($RTCategories, 6);

// ✅ Fetch 10 latest auctions
$RTStmt = $RTPdo->query("
    SELECT auction.*, category.name AS category_name, user.name AS seller_name
    FROM auction
    JOIN category ON auction.category_id = category.id
    JOIN user ON auction.user_id = user.id
    ORDER BY auction.id DESC
    LIMIT 10
");
$RTAuctions = $RTStmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />
   <style>
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
	padding: 0.3em 0.4em;
	display: inline-block;
	text-decoration: none;
	font-weight: bold;
	color: #000;
}
    </style>
</head>

<body>
<?php include 'RTheader.php'; ?>

    <main>
        <?php if (isset($_SESSION['RTUserName'])): ?>
            <p style="text-align: center; font-size: 1.2em; margin-bottom: 1em;">
                👋 Welcome, <?= htmlspecialchars($_SESSION['RTUserName']); ?>!
            </p>
        <?php endif; ?>

        <?php if (isset($_SESSION['RTUserId']) && $_SESSION['RTRole'] === 'user'): ?>
            <div style="text-align: center; margin-bottom: 2em;">
                <a href="RTmyListings.php" class="authLink" style="display: inline-block; padding: 0.5em 1.5em; font-size: 1em;">
                    📄 View My Listings
                </a>
            </div>
        <?php endif; ?>

        <h1>Latest Car Listings</h1>
        <ul class="carList">
            <?php foreach ($RTAuctions as $auction): ?>
                <li>
                <?php if (!empty($auction['image']) && file_exists($auction['image'])): ?>
    <img src="<?= htmlspecialchars($auction['image']) ?>" alt="Car image">
<?php else: ?>
    <img src="car.png" alt="Car image">
<?php endif; ?>
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

        <?php include 'RTfooter.php'; ?>
    </main>
</body>
</html>
