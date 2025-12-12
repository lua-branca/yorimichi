<?php
header('Content-Type: application/json; charset=utf-8');

// Configuration
$ADMIN_EMAIL = 'support@yorimichi-living.com';
// The GAS Web App URL for the Contact Form (different from Event Form)
$GAS_SCRIPT_URL = 'https://script.google.com/macros/s/AKfycbwWncIpV4fsbO91QLlJdSInUTiTUa9z58l4bEysnG1Bomm_55Li81yoWZDvOJ4ZUMpb-g/exec';

// Security: Simple Honeypot Check
// If the hidden field 'confirm_code' is filled, it's likely a bot.
if (!empty($_POST['confirm_code'])) {
    // Return success to confuse the bot, but do nothing.
    echo json_encode(['result' => 'success', 'bot_detected' => true]);
    exit;
}

// Basic Sanitization
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Function to send data to GAS
function sendToGas($url, $data)
{
    if (empty($url))
        return;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    // Send as POST fields since the original script likely expects standard POST, 
    // but verify if JSON is better. js/script.js sent FormData.
    // Let's send regular POST fields to match typical GAS `e.parameter` handling.
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($type)) {
        echo json_encode(['result' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['result' => 'error', 'message' => 'Invalid email format']);
        exit;
    }

    // Send to Google Sheets (GAS)
    // GAS script usually expects name, email, type, message
    $gas_data = [
        'name' => $name,
        'email' => $email,
        'type' => $type,
        'message' => $message
    ];
    // We do this in background (fire and forget) or wait? 
    // curl_exec waits. That's fine.
    sendToGas($GAS_SCRIPT_URL, $gas_data);

    // Email Subject/Body
    $subject = "【よりみちリビング】お問い合わせありがとうございます";

    $body = "{$name} 様\n\n";
    $body .= "この度はお問い合わせいただき、誠にありがとうございます。\n";
    $body .= "以下の内容でメッセージを受け付けました。\n\n";
    $body .= "--------------------------------------------------\n";
    $body .= "■お名前：{$name}\n";
    $body .= "■メールアドレス：{$email}\n";
    $body .= "■お問い合わせ種別：{$type}\n";
    $body .= "■メッセージ：\n{$message}\n";
    $body .= "--------------------------------------------------\n\n";
    $body .= "内容を確認の上、担当者より順次ご連絡させていただきます。\n";
    $body .= "今しばらくお待ちいただけますようお願い申し上げます。\n\n";
    $body .= "よりみちリビング\n";
    $body .= "https://yorimichi-living.com/";

    // Admin Notification
    $admin_subject = "【よりみちリビング】新しいお問い合わせがありました（{$name}様）";
    $admin_body = "Webサイトより新しいお問い合わせがありました。\n\n" . $body;

    $headers = "From: support@yorimichi-living.com" . "\r\n" .
        "Reply-To: " . $email;

    // Send to Admin
    mb_send_mail($ADMIN_EMAIL, $admin_subject, $admin_body, $headers);

    // Send to User
    $user_headers = "From: support@yorimichi-living.com";
    mb_send_mail($email, $subject, $body, $user_headers);

    // Return JSON success
    echo json_encode(['result' => 'success']);
    exit;

} else {
    echo json_encode(['result' => 'error', 'message' => 'Invalid Request']);
    exit;
}
?>