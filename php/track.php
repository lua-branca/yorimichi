<?php
// php/track.php
// Custom Analytics Logger

// Set timezone
date_default_timezone_set('Asia/Tokyo');

// Log file path
$logFile = __DIR__ . '/../access_log.csv';

// If file doesn't exist, create it with header
if (!file_exists($logFile)) {
    $header = "Timestamp,IP,URL,Referrer,UserAgent,DeviceType\n";
    file_put_contents($logFile, $header);
}

// Get data
$timestamp = date('Y-m-d H:i:s');
$ip = $_SERVER['REMOTE_ADDR'];
$url = $_POST['url'] ?? $_SERVER['HTTP_REFERER'] ?? '-';
$referrer = $_POST['referrer'] ?? '-';
$userAgent = $_SERVER['HTTP_USER_AGENT'];

// Improve Referrer cleaning
if (empty($referrer) || $referrer == 'null' || $referrer == 'undefined') {
    $referrer = 'Direct / Bookmark';
}

// Simple Device Check
$deviceType = 'PC';
if (preg_match('/(iPhone|iPod|Android.*Mobile|Windows Phone)/i', $userAgent)) {
    $deviceType = 'Mobile';
} elseif (preg_match('/(iPad|Android(?!.*Mobile))/i', $userAgent)) {
    $deviceType = 'Tablet';
}

// Simple GeoIP (Using external API is slow, so we skip it for logging speed. 
// We will do resolution in the dashboard or just rely on IP for now)

// Escape for CSV
function csv_escape($str)
{
    if (strpos($str, ',') !== false || strpos($str, '"') !== false || strpos($str, "\n") !== false) {
        $str = '"' . str_replace('"', '""', $str) . '"';
    }
    return $str;
}

// Format line
$line = sprintf(
    "%s,%s,%s,%s,%s,%s\n",
    csv_escape($timestamp),
    csv_escape($ip),
    csv_escape($url),
    csv_escape($referrer),
    csv_escape($userAgent),
    csv_escape($deviceType)
);

// Lock and Write
file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);

// Return pixel or JSON
header('Content-Type: application/json');
echo json_encode(['status' => 'ok']);
exit;
?>