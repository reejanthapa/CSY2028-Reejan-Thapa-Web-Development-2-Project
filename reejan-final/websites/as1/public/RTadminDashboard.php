<?php
session_start();
require 'RTdb.php';

// 🔐 Redirect if not admin
if (!isset($_SESSION['RTRole']) || $_SESSION['RTRole'] !== 'admin') {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>RT Admin Dashboard</title>
    <link rel="stylesheet" href="carbuy.css">
    <style>
        .rt-dashboard {
            width: 80%;
            margin: 5vw auto;
            background-color: white;
            padding: 3em;
            border-radius: 1em;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .rt-dashboard h1 {
            color: #3665f3;
            text-align: center;
            margin-bottom: 2em;
        }

        .rt-dashboard ul {
            list-style: none;
            padding: 0;
        }

        .rt-dashboard li {
            margin: 1em 0;
        }

        .rt-dashboard a {
            display: inline-block;
            padding: 1em 2em;
            background-color: #3665f3;
            color: white;
            font-weight: bold;
            border-radius: 0.5em;
            text-decoration: none;
        }

        .rt-dashboard a:hover {
            background-color: #274dc1;
        }

        .rt-top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .rt-logout-btn {
            background-color: #e43137;
            color: white;
            padding: 0.6em 1.2em;
            border: none;
            border-radius: 0.5em;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }

        .rt-logout-btn:hover {
            background-color: #c11e26;
        }
    </style>
</head>
<body>
    <main>
        <div class="rt-dashboard">
            <div class="rt-top-bar">
                <h1>Welcome, <?= htmlspecialchars($_SESSION['RTUserName']) ?> 👑</h1>
                <a href="RTlogout.php" class="rt-logout-btn">Logout</a>
            </div>

            <ul>
                <li><a href="RTadminCategories.php">📁 Manage Categories</a></li>
                <!-- Optional future admin features -->
                <!-- <li><a href="#">🔍 View All Auctions</a></li> -->
                <!-- <li><a href="#">👥 Manage Users</a></li> -->
            </ul>
        </div>
    </main>
</body>
</html>
