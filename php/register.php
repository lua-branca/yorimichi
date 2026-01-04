<?php
header('Content-Type: application/json; charset=utf-8');

// Configuration
$ADMIN_EMAIL = 'support@yorimichi-living.com';
$GAS_SCRIPT_URL = 'https://script.google.com/macros/s/AKfycbwOE7xWtSyj7uPjY4x8GOk8-nKKrNigSRZlWNEJwJhAg9xAZ0IypEfL37X4Q3E5Bh-v/exec';

// Honeypot
if (!empty($_POST['confirm_code'])) {
    echo json_encode(['result' => 'success', 'bot_detected' => true]);
    exit;
}

function sendToGas($url, $data)
{
    if (empty($url))
        return;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get Data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $referrer = trim($_POST['referrer'] ?? '');
    $participation_type = trim($_POST['participation_type'] ?? '');
    $fb_account = trim($_POST['fb_account'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($age) || empty($address) || empty($referrer) || empty($participation_type)) {
        echo json_encode(['result' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['result' => 'error', 'message' => 'Invalid email format']);
        exit;
    }

    // Participation Style Label
    $types_map = [
        'host' => 'ホスト（料理を振る舞いたい）',
        'guest' => 'ゲスト（料理を楽しみたい）',
        'both' => '両方'
    ];
    $participation_label = $types_map[$participation_type] ?? $participation_type;

    // Send to Google Sheets (GAS)
    // Sending all fields. If GAS columns don't exist, they might be ignored depending on GAS implementation.
    $gas_data = [
        'form_type' => 'register', // Identifier for GAS if it supports switching
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'age' => $age,
        'address' => $address,
        'referrer' => $referrer,
        'participation_type' => $participation_label,
        'fb_account' => $fb_account,
        'message' => $message
    ];
    sendToGas($GAS_SCRIPT_URL, $gas_data);

    // --- Admin Notification Email ---
    $admin_subject = "【よりみちリビング】新規メンバー登録申込（{$name}様）";
    $admin_body = "Webサイトより新規メンバー登録のお申し込みがありました。\n\n";
    $admin_body .= "【登録内容】\n";
    $admin_body .= "--------------------------------------------------\n";
    $admin_body .= "■お名前：{$name}\n";
    $admin_body .= "■メールアドレス：{$email}\n";
    $admin_body .= "■電話番号：{$phone}\n";
    $admin_body .= "■ご年代：{$age}\n";
    $admin_body .= "■お住まい：{$address}\n";
    $admin_body .= "■紹介者：{$referrer}\n";
    $admin_body .= "■参加スタイル：{$participation_label}\n";
    $admin_body .= "■Facebook：{$fb_account}\n";
    $admin_body .= "■ひとこと・備考：\n{$message}\n";
    $admin_body .= "--------------------------------------------------\n\n";
    $admin_body .= "■管理用スプレッドシート\n";
    $admin_body .= "https://docs.google.com/spreadsheets/d/1haWEAqOZ7Bc2N_5G7dlpqGxGHBuVAdklEzmgJyhDZs4/edit?gid=0#gid=0\n\n";
    $admin_body .= "紹介者を確認の上、承認作業を進めてください。\n";

    $headers = "From: support@yorimichi-living.com" . "\r\n" .
        "Reply-To: " . $email;

    mb_send_mail($ADMIN_EMAIL, $admin_subject, $admin_body, $headers);

    // --- User Auto-Reply Email ---
    $user_subject = "【よりみちリビング】メンバー登録のお申し込みありがとうございます";
    $user_body = "{$name} 様\n\n";
    $user_body .= "この度は「よりみちリビング」へのメンバー登録にお申し込みいただき、\n";
    $user_body .= "誠にありがとうございます。\n\n";
    $user_body .= "以下の内容で受け付けいたしました。\n\n";
    $user_body .= "--------------------------------------------------\n";
    $user_body .= "■お紹介者様：{$referrer}\n";
    $user_body .= "■参加スタイル：{$participation_label}\n";
    $user_body .= "--------------------------------------------------\n\n";
    $user_body .= "【今後の流れについて】\n";
    $user_body .= "運営事務局にて、ご入力いただいた紹介者様の確認を行わせていただきます。\n\n";
    $user_body .= "1. Facebookアカウントをご入力いただいた方\n";
    $user_body .= "   確認後、メンバー限定のFacebookグループへ招待をお送りします。\n\n";
    $user_body .= "2. Facebookアカウントをお持ちでない方\n";
    $user_body .= "   確認後、本メールアドレス宛に今後のイベント案内等をお送りさせていただきます。\n\n";
    $user_body .= "確認まで数日いただく場合がございますが、今しばらくお待ちください。\n\n";
    $user_body .= "--------------------------------------------------\n";
    $user_body .= "よりみちリビング 運営事務局\n";
    $user_body .= "https://yorimichi-living.com/";

    $user_headers = "From: support@yorimichi-living.com";

    mb_send_mail($email, $user_subject, $user_body, $user_headers);

    echo json_encode(['result' => 'success']);
    exit;

} else {
    echo json_encode(['result' => 'error', 'message' => 'Invalid Request']);
    exit;
}
?>