<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require 'RTdb.php';

// Fetch categories
$RTCatStmt = $RTPdo->query('SELECT * FROM category ORDER BY name');
$RTCategories = $RTCatStmt->fetchAll();
$RTMainCategories = array_slice($RTCategories, 0, 6);
$RTExtraCategories = array_slice($RTCategories, 6);
?>

<header>
    <h1>
        <span class="C">C</span><span class="a">a</span><span class="r">r</span>
        <span class="b">b</span><span class="u">u</span><span class="y">y</span>
    </h1>

    <form action="RTsearch.php" method="GET">
        <input type="text" name="search" placeholder="Search for a car" />
        <input type="submit" value="Search" />
    </form>
</header>

<nav>
    <ul>
        <?php foreach ($RTMainCategories as $cat): ?>
            <li><a class="categoryLink" href="RTcategory.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
        <?php endforeach; ?>

        <?php if (count($RTExtraCategories) > 0): ?>
            <li class="rt-dropdown">
                <a class="categoryLink" href="#">More ▼</a>
                <ul class="rt-dropdown-content">
                    <?php foreach ($RTExtraCategories as $cat): ?>
                        <li><a class="categoryLink" href="RTcategory.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (isset($_SESSION['RTUserName'])): ?>
            <?php if ($_SESSION['RTRole'] === 'user'): ?>
                <li><a class="authLink" href="RTaddAuction.php">Add Auction</a></li>
            <?php endif; ?>
            <li style="margin-left:auto;"><a class="authLink" href="RTlogout.php">Logout</a></li>
        <?php else: ?>
            <li style="margin-left:auto;"><a class="authLink" href="RTlogin.php">Login</a></li>
            <li><a class="authLink" href="RTregister.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

<img src="banners/1.jpg" alt="Banner" />
