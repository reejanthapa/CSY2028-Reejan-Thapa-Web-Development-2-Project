<?php
session_start();
require 'RTdb.php';

if (!isset($_SESSION['RTUserId']) || !isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$RTAuctionId = $_GET['id'];
$RTUserId = $_SESSION['RTUserId'];

$RTStmt = $RTPdo->prepare("DELETE FROM auction WHERE id = ? AND user_id = ?");
$RTStmt->execute([$RTAuctionId, $RTUserId]);

header('Location: index.php');
exit;
?>
