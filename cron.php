<?php
require_once __DIR__ . '/functions.php';

$data = fetchGitHubTimeline();
$html = formatGitHubData($data);
sendGitHubUpdatesToSubscribers($html);
