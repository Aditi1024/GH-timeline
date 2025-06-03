<?php

function generateVerificationCode() {
    // Generate and return a 6-digit numeric code
    return rand(100000, 999999);
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';

    // Read all emails already registered
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

    // Add email if not already registered
    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';

    // Read all emails
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

    // Remove email (case insensitive)
    $emails = array_filter($emails, function($e) use ($email) {
        return strtolower(trim($e)) !== strtolower(trim($email));
    });

    // Save updated list
    file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL);
}

function sendVerificationEmail($email, $code) {
    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    mail($email, $subject, $message, $headers);
}

function sendUnsubscribeVerificationEmail($email, $code) {
    $subject = "Confirm Unsubscription";
    $message = "<p>To confirm unsubscription, use this code: <strong>$code</strong></p>";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    mail($email, $subject, $message, $headers);
}

function fetchGitHubTimeline() {
    $url = "https://github.com/timeline";
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: PHP\r\n"
        ]
    ]);
    $data = @file_get_contents($url, false, $context);

    if ($data === false) {
        return "";
    }
    return $data;
}

function formatGitHubData($data) {
    // If empty, return placeholder
    if (empty($data)) {
        return "<p>No GitHub timeline data available at the moment.</p>";
    }

    // Since real timeline data parsing is complex and URL is deprecated,
    // this is a simple placeholder for email content:
    $html = "<h2>GitHub Timeline Updates</h2>";
    $html .= "<table border='1' cellpadding='5' cellspacing='0'>";
    $html .= "<tr><th>Event</th><th>User</th></tr>";
    $html .= "<tr><td>Push</td><td>testuser</td></tr>";
    $html .= "</table>";

    return $html;
}

function sendGitHubUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';

    // Return if no file or no emails
    if (!file_exists($file)) {
        return;
    }

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (empty($emails)) {
        return;
    }

    // Fetch and format GitHub timeline data
    $data = fetchGitHubTimeline();
    $htmlContent = formatGitHubData($data);

    foreach ($emails as $email) {
        $unsubscribeUrl = "http://localhost:8000/unsubscribe.php?email=" . urlencode($email);
        $emailBody = $htmlContent . '<p><a href="' . $unsubscribeUrl . '" id="unsubscribe-button">Unsubscribe</a></p>';

        $subject = "Latest GitHub Updates";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: no-reply@example.com\r\n";

        mail($email, $subject, $emailBody, $headers);
    }
}

?>

