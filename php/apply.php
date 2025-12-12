<?php
// Configuration
$ADMIN_EMAIL = 'support@yorimichi-living.com'; // Replace with actual admin email if different
// TODO: USER TO UPDATE THESE URLs
$STRIPE_PAYMENT_LINK = 'https://buy.stripe.com/4gMaEXfpOdyAdr49HkcAo00';
$GAS_SCRIPT_URL = 'https://script.google.com/macros/s/AKfycbwJ-0uWCJsY5GPIzpH7MkZtNtSB2fUrGRRXqAYrWGj5_Ly4JeduDk9Q3z5nE3TTh16Mdw/exec';

// Basic Sanitization
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Function to send data to Google Sheets via GAS
function sendToGas($url, $data)
{
    if ($url === 'YOUR_GAS_SCRIPT_URL_HERE' || empty($url))
        return;

    $json_data = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json_data)
    ]);

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Security: Simple Honeypot Check
    // If the hidden field 'confirm_code' is filled, it's likely a bot.
    // We treat it as success (or just exit) to avoid processing.
    if (!empty($_POST['confirm_code'])) {
        exit;
    }

    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $event_name = trim($_POST['event_name'] ?? '');
    $member_type = trim($_POST['member_type'] ?? '');
    $introducer = trim($_POST['introducer'] ?? '');
    $count = trim($_POST['count'] ?? '');
    $allergies = trim($_POST['allergies'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');

    // Validate required fields
    if (empty($name) || empty($email) || empty($event_name) || empty($member_type) || empty($count)) {
        echo "必須項目が入力されていません。";
        exit;
    }

    // Determine Stripe Link based on Event (if multiple events in future, use switch)
// Basic Logic for "12/21 料理研究家杉なまこ先生の手料理ホムパ"
    // In the future, map event_name to ID
    $evt_id_param = '';
    if (strpos($event_name, '12/21') !== false) {
        $evt_id_param = '?evt=20251221';
    }

    // Send to Google Sheets (GAS)
    $gas_data = [
        'event_name' => $event_name,
        'name' => $name,
        'email' => $email,
        'member_type' => $member_type,
        'introducer' => $introducer,
        'count' => $count,
        'allergies' => $allergies,
        'remarks' => $remarks
    ];
    sendToGas($GAS_SCRIPT_URL, $gas_data);

    // Email Subject/Body
    $subject = "【よりみちリビング】イベントお申し込み受け付けました";

    $body = "{$name} 様\n\n";
    $body .= "よりみちリビングへのイベントお申し込みありがとうございます。\n";
    $body .= "以下の内容で受け付けいたしました。\n\n";
    $body .= "--------------------------------------------------\n";
    $body .= "■イベント名：{$event_name}\n";
    $body .= "■お名前：{$name}\n";
    $body .= "■メールアドレス：{$email}\n";
    $body .= "■会員種別：{$member_type}\n";
    if ($member_type === 'ゲスト' && !empty($introducer)) {
        $body .= "■紹介者名：{$introducer}\n";
    }
    $body .= "■参加人数：{$count}\n";
    if (!empty($allergies)) {
        $body .= "■アレルギー・食事制限：\n{$allergies}\n";
    }
    if (!empty($remarks)) {
        $body .= "■備考：\n{$remarks}\n";
    }
    $body .= "--------------------------------------------------\n\n";

    // Unified Payment Flow for ALL users
    $body .= "※参加費のお支払いをもって予約確定となります。\n";
    $body .= "このまま自動的に決済画面へ移動しますが、もし移動しない場合は以下のURLよりお支払いください。\n";
    if ($STRIPE_PAYMENT_LINK !== 'YOUR_STRIPE_PAYMENT_LINK_HERE') {
        $body .= $STRIPE_PAYMENT_LINK . "\n\n";
    } else {
        $body .= "(決済リンク準備中)\n\n";
    }

    $body .= "■当日の会場について\n";
    $body .= "以下のページより地図とアクセス方法をご確認いただけます。\n";
    $body .= "URL: https://yorimichi-living.com/php/access.php{$evt_id_param}\n";
    $body .= "合言葉: yorimichi1221\n\n";

    $body .= "当日お会いできるのを楽しみにしております。\n\n";
    $body .= "よりみちリビング\n";
    $body .= "https://yorimichi-living.com/";

    // Send Admin Email
    $admin_subject = "【新規申込】{$event_name} ({$name}様)";
    $admin_body = "Webサイトより新しいイベント申し込みがありました。\n\n" . $body;

    $headers = "From: support@yorimichi-living.com" . "\r\n" .
        "Reply-To: " . $email;

    // Mail to Admin
    mb_send_mail($ADMIN_EMAIL, $admin_subject, $admin_body, $headers);

    // Mail to User
    $user_headers = "From: " . $ADMIN_EMAIL;
    mb_send_mail($email, $subject, $body, $user_headers);

    // Redirect to Success Page with Stripe Link param (For ALL users)
    if ($STRIPE_PAYMENT_LINK !== 'YOUR_STRIPE_PAYMENT_LINK_HERE') {
        header("Location: success.php?redirect=" . urlencode($STRIPE_PAYMENT_LINK));
    } else {
        // Fallback for testing/placeholder
        header("Location: success.php?payment_link_missing=true");
    }
    exit;

} else {
    // Not POST
    header("Location: ../events/apply.html");
    exit;
}
?>