<?php
session_start();
require_once __DIR__ . '/functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle unsubscribe email submission
    if (isset($_POST['unsubscribe_email'])) {
        $email = trim($_POST['unsubscribe_email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = generateVerificationCode();
            $_SESSION['unsubscribe_code'] = $code;
            $_SESSION['email_to_unsubscribe'] = $email;

            sendUnsubscribeVerificationEmail($email, $code);  // We'll add this function in functions.php

            $message = "Unsubscribe verification code sent to your email.";
        } else {
            $message = "Please enter a valid email.";
        }
    }

    // Handle unsubscribe code verification
    if (isset($_POST['unsubscribe_verification_code'])) {
        $inputCode = trim($_POST['unsubscribe_verification_code']);
        if (isset($_SESSION['unsubscribe_code'], $_SESSION['email_to_unsubscribe']) && $inputCode === $_SESSION['unsubscribe_code']) {
            unsubscribeEmail($_SESSION['email_to_unsubscribe']);

            unset($_SESSION['unsubscribe_code'], $_SESSION['email_to_unsubscribe']);

            $message = "You have been unsubscribed successfully.";
        } else {
            $message = "Incorrect unsubscribe verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Unsubscribe from GitHub Updates</title>
</head>
<body>

<h1>Unsubscribe from GitHub Timeline Updates</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<!-- Email input form -->
<form method="post" action="">
    <input type="email" name="unsubscribe_email" required placeholder="Enter your email to unsubscribe" />
    <button id="submit-unsubscribe" type="submit">Unsubscribe</button>
</form>

<!-- Unsubscribe verification code form -->
<form method="post" action="">
    <input type="text" name="unsubscribe_verification_code" maxlength="6" placeholder="Enter unsubscribe code" />
    <button id="verify-unsubscribe" type="submit">Verify</button>
</form>

</body>
</html>
