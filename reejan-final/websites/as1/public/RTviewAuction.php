<?php
session_start();
require 'RTdb.php';

$RTAuctionId = $_GET['id'] ?? null;
$RTUserId = $_SESSION['RTUserId'] ?? null;

if (!$RTAuctionId || !is_numeric($RTAuctionId)) {
    header('Location: index.php');
    exit;
}

// ✅ Get Auction Details
$RTStmt = $RTPdo->prepare("
    SELECT auction.*, category.name AS category_name, user.name AS seller_name
    FROM auction
    JOIN category ON auction.category_id = category.id
    JOIN user ON auction.user_id = user.id
    WHERE auction.id = ?
");
$RTStmt->execute([$RTAuctionId]);
$RTAuction = $RTStmt->fetch();

if (!$RTAuction) {
    echo "<p style='text-align:center;'>❌ Auction not found.</p>";
    exit;
}

// ✅ Handle Review
if (isset($_POST['RTReviewText']) && $RTUserId) {
    $RTReviewText = trim($_POST['RTReviewText']);
    if ($RTReviewText) {
        $RTReviewStmt = $RTPdo->prepare("INSERT INTO review (user_id, auction_id, content, review_date) VALUES (?, ?, ?, NOW())");
        $RTReviewStmt->execute([$RTUserId, $RTAuctionId, $RTReviewText]);
    }
}

// ✅ Handle Bid
if (isset($_POST['RTBidAmount']) && $RTUserId && $RTUserId != $RTAuction['user_id']) {
    $RTBid = floatval($_POST['RTBidAmount']);
    if ($RTBid > $RTAuction['current_bid']) {
        // Insert into bid table
        $RTInsertBid = $RTPdo->prepare("INSERT INTO bid (user_id, auction_id, amount) VALUES (?, ?, ?)");
        $RTInsertBid->execute([$RTUserId, $RTAuctionId, $RTBid]);

        // Update current bid
        $RTUpdateAuction = $RTPdo->prepare("UPDATE auction SET current_bid = ? WHERE id = ?");
        $RTUpdateAuction->execute([$RTBid, $RTAuctionId]);

        $RTAuction['current_bid'] = $RTBid;
    }
}

// ✅ Fetch Reviews
$RTReviewFetch = $RTPdo->prepare("
    SELECT review.content, review.review_date, user.name 
    FROM review 
    JOIN user ON review.user_id = user.id 
    WHERE auction_id = ? 
    ORDER BY review.review_date DESC
");
$RTReviewFetch->execute([$RTAuctionId]);
$RTReviews = $RTReviewFetch->fetchAll();

// ✅ Fetch Bid History
$RTBidsStmt = $RTPdo->prepare("
    SELECT bid.amount, bid.bid_time, user.name 
    FROM bid 
    JOIN user ON bid.user_id = user.id 
    WHERE bid.auction_id = ?
    ORDER BY bid.bid_time DESC
");
$RTBidsStmt->execute([$RTAuctionId]);
$RTBids = $RTBidsStmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($RTAuction['title']) ?> - Auction Details</title>
    <link rel="stylesheet" href="carbuy.css">
    <style>
        main { max-width: 900px; margin: auto; padding: 2em; background: white; border-radius: 1em; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #3665f3; text-align: center; }
        .rt-section { margin-bottom: 2em; }
        .rt-price { font-size: 2em; font-weight: bold; color: red; }
        .rt-label { font-weight: bold; margin-top: 1em; display: block; }
        .rt-review-box, .rt-bid-box { margin-top: 1em; }
        textarea, input[type="number"], input[type="submit"] {
            width: 100%; padding: 0.8em; border-radius: 0.5em; border: 1px solid #ccc; margin-top: 0.5em;
        }
        input[type="submit"] {
            background-color: #3665f3;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #274dc1;
        }
        ul { list-style: none; padding-left: 0; }
        ul li { margin-bottom: 0.5em; }
        .authLink {
            background-color: #3665f3;
            color: white !important;
            padding: 0.5em 1em;
            margin-top: 2em;
            display: inline-block;
            border-radius: 0.5em;
            font-weight: bold;
            text-decoration: none;
        }
        .authLink:hover {
            background-color: #274dc1;
        }
    </style>
</head>
<body>
    <main>
        <h1><?= htmlspecialchars($RTAuction['title']) ?></h1>

        <div class="rt-section">
            <p><strong>Category:</strong> <?= htmlspecialchars($RTAuction['category_name']) ?></p>
            <p><strong>Seller:</strong> <?= htmlspecialchars($RTAuction['seller_name']) ?></p>
            <p><strong>Description:</strong><br> <?= nl2br(htmlspecialchars($RTAuction['description'])) ?></p>
            <p class="rt-price">Current Bid: £<?= number_format($RTAuction['current_bid'], 2) ?></p>
            <p><strong>Ends:</strong> <?= date('d M Y, H:i', strtotime($RTAuction['end_time'])) ?></p>
        </div>

        <?php if (isset($_SESSION['RTUserId']) && $_SESSION['RTUserId'] != $RTAuction['user_id']): ?>
            <div class="rt-bid-box">
                <form method="POST">
                    <label for="RTBidAmount" class="rt-label">Place a Bid:</label>
                    <input type="number" name="RTBidAmount" step="0.01" min="<?= $RTAuction['current_bid'] + 0.01 ?>" required>
                    <input type="submit" value="Place Bid">
                </form>
            </div>
        <?php endif; ?>

        <div class="rt-section">
            <h2>Bid History</h2>
            <ul>
                <?php if (count($RTBids) > 0): ?>
                    <?php foreach ($RTBids as $bid): ?>
                        <li>
                            💰 £<?= number_format($bid['amount'], 2) ?>
                            by <strong><?= htmlspecialchars($bid['name']) ?></strong>
                            on <?= date('d M Y, H:i', strtotime($bid['bid_time'])) ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No bids yet.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="rt-section">
            <h2>Reviews</h2>
            <ul>
                <?php foreach ($RTReviews as $review): ?>
                    <li>
                        <strong><?= htmlspecialchars($review['name']) ?>:</strong>
                        <?= htmlspecialchars($review['content']) ?>
                        <em>(<?= date('d M Y', strtotime($review['review_date'])) ?>)</em>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($RTUserId): ?>
                <div class="rt-review-box">
                    <form method="POST">
                        <label for="RTReviewText" class="rt-label">Add Your Review:</label>
                        <textarea name="RTReviewText" required></textarea>
                        <input type="submit" value="Add Review">
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div style="text-align:center;">
            <a href="index.php" class="authLink">← Back to Listings</a>
        </div>
    </main>
</body>
</html>
