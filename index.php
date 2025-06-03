<?php
session_start();
require_once __DIR__ . '/functions.php';

// Initialize messages
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: Handle email submission to send verification code
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = generateVerificationCode();
            $_SESSION['verification_code'] = $code;
            $_SESSION['email_to_verify'] = $email;

            sendVerificationEmail($email, $code);

            $message = "Verification code sent to your email.";
        } else {
            $message = "Please enter a valid email.";
        }
    }

    // Step 2: Handle verification code submission
    if (isset($_POST['verification_code'])) {
        $inputCode = trim($_POST['verification_code']);
        if (isset($_SESSION['verification_code'], $_SESSION['email_to_verify']) && $inputCode === $_SESSION['verification_code']) {
            // Code is correct, register email
            registerEmail($_SESSION['email_to_verify']);

            // Clear session verification data
            unset($_SESSION['verification_code'], $_SESSION['email_to_verify']);

            $message = "Email verified and registered successfully!";
        } else {
            $message = "Incorrect verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Email Verification</title>
</head>
<body>

<h1>Subscribe to GitHub Timeline Updates</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<!-- Email input form -->
<form method="post" action="">
    <input type="email" name="email" required placeholder="Enter your email" />
    <button id="submit-email" type="submit">Submit</button>
</form>

<!-- Verification code form -->
<form method="post" action="">
    <input type="text" name="verification_code" maxlength="6" required placeholder="Enter verification code" />
    <button id="submit-verification" type="submit">Verify</button>
</form>

</body>
</html>
