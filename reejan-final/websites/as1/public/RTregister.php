<?php
session_start();
require 'RTdb.php';
$RTMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $RTEmail = $_POST['RTEmail'];
    $RTName = $_POST['RTName'];
    $RTPassword = password_hash($_POST['RTPassword'], PASSWORD_DEFAULT);

    function RTRegisterUser($RTPdo, $RTEmail, $RTPassword, $RTName) {
        $RTStmt = $RTPdo->prepare('INSERT INTO user (email, password, name) VALUES (?, ?, ?)');
        $RTStmt->execute([$RTEmail, $RTPassword, $RTName]);
    }

    try {
        RTRegisterUser($RTPdo, $RTEmail, $RTPassword, $RTName);
        $RTMessage = '🎉 RT Registration successful!';
    } catch (PDOException $e) {
        $RTMessage = '❌ RT Registration failed: ' . (str_contains($e->getMessage(), 'Integrity constraint violation')
            ? 'Email already exists.'
            : $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>RT Register</title>
    <link rel="stylesheet" href="carbuy.css">
    <style>
        .rt-register-container {
            width: 70%;
            margin: 5vw auto;
            padding: 2em;
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
            border-radius: 1em;
        }

        .rt-register-container h1 {
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
            color: green;
        }

        .rt-error {
            color: red;
        }

        
        

    </style>
</head>
<body>

    <main>
        <div class="rt-register-container">
            <h1>Register on RT Auctions</h1>

            <?php if ($RTMessage): ?>
                <p class="rt-message <?= str_starts_with($RTMessage, '❌') ? 'rt-error' : '' ?>">
                    <?= htmlspecialchars($RTMessage) ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="RTregister.php">
                <div class="rt-form-group">
                    <label for="RTEmail">Email Address</label>
                    <input type="email" id="RTEmail" name="RTEmail" required>
                </div>

                <div class="rt-form-group">
                    <label for="RTName">Full Name</label>
                    <input type="text" id="RTName" name="RTName" required>
                </div>

                <div class="rt-form-group">
                    <label for="RTPassword">Password</label>
                    <input type="password" id="RTPassword" name="RTPassword" required>
                </div>

                <div class="rt-form-submit">
                    <input type="submit" value="Create RT Account">
                </div>
            </form>
        </div>
    </main>
</body>
</html>
