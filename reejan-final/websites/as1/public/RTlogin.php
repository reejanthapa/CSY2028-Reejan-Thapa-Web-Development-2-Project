<?php
session_start();
require 'RTdb.php';
$RTLoginMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $RTEmail = $_POST['RTEmail'];
    $RTPassword = $_POST['RTPassword'];

   // ✅ Hardcoded admin login
if ($RTEmail === 'reejan@gmail.com' && $RTPassword === 'reejanthapa') {
    $_SESSION['RTUserId'] = 0; // No DB ID
    $_SESSION['RTUserName'] = 'Reejan Thapa';
    $_SESSION['RTRole'] = 'admin';
    header('Location: RTadminDashboard.php');
    exit;
}

    // ✅ Check in database
    $RTStmt = $RTPdo->prepare('SELECT * FROM user WHERE email = ?');
    $RTStmt->execute([$RTEmail]);
    $RTUser = $RTStmt->fetch();

    if ($RTUser && password_verify($RTPassword, $RTUser['password'])) {
        $_SESSION['RTUserId'] = $RTUser['id'];
        $_SESSION['RTUserName'] = $RTUser['name'];
        $_SESSION['RTRole'] = $RTUser['role'];

        if ($RTUser['role'] === 'admin') {
            header('Location: RTadminDashboard.php');

        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        $RTLoginMessage = '❌ Invalid email or password.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>RT Login</title>
    <link rel="stylesheet" href="carbuy.css">
    <style>
        .rt-login-container {
            width: 70%;
            margin: 5vw auto;
            padding: 2em;
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
            border-radius: 1em;
        }

        .rt-login-container h1 {
            text-align: center;
            margin-bottom: 2em;
            color: #3665f3;
        }

        .rt-form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5em;
        }

        .rt-form-group label {
            font-weight: bold;
            margin-bottom: 0.5em;
        }

        .rt-form-group input {
            padding: 0.8em;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 0.5em;
        }

        .rt-form-submit {
            text-align: center;
        }

        .rt-form-submit input[type="submit"] {
            background-color: #3665f3;
            color: white;
            padding: 0.8em 2em;
            font-size: 1em;
            border: none;
            border-radius: 0.5em;
            cursor: pointer;
        }

        .rt-form-submit input[type="submit"]:hover {
            background-color: #274dc1;
        }

        .rt-message {
            text-align: center;
            margin-top: 1em;
            font-weight: bold;
            color: red;
        }

        </style>
</head>
<body>

    <main>
        <div class="rt-login-container">
            <h1>RT User Login</h1>

            <?php if ($RTLoginMessage): ?>
                <p class="rt-message"><?= htmlspecialchars($RTLoginMessage) ?></p>
            <?php endif; ?>

            <form method="POST" action="RTlogin.php">
                <div class="rt-form-group">
                    <label for="RTEmail">Email Address</label>
                    <input type="email" id="RTEmail" name="RTEmail" required>
                </div>

                <div class="rt-form-group">
                    <label for="RTPassword">Password</label>
                    <input type="password" id="RTPassword" name="RTPassword" required>
                </div>

                <div class="rt-form-submit">
                    <input type="submit" value="RT Login">
                </div>
            </form>
        </div>
    </main>
</body>
</html>
