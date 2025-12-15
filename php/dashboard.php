<?php
// php/dashboard.php
session_start();

$PASSWORD = 'yorimichi-minami';

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: dashboard.php");
    exit;
}

// Login logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['password']) && $_POST['password'] === $PASSWORD) {
        $_SESSION['is_admin_logged_in'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "パスワードが違います";
    }
}

// Check Login
if (!isset($_SESSION['is_admin_logged_in']) || $_SESSION['is_admin_logged_in'] !== true) {
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login</title>
        <style>
            body {
                font-family: sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background: #f5f5f5;
            }

            .login-box {
                background: white;
                padding: 2rem;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                width: 300px;
                text-align: center;
            }

            input {
                width: 100%;
                padding: 10px;
                margin: 10px 0;
                box-sizing: border-box;
            }

            button {
                width: 100%;
                padding: 10px;
                background: #333;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }

            .error {
                color: red;
                font-size: 0.9em;
            }
        </style>
    </head>

    <body>
        <div class="login-box">
            <h2>管理者ログイン</h2>
            <?php if (isset($error))
                echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="合言葉" required>
                <button type="submit">ログイン</button>
            </form>
        </div>
    </body>

    </html>
    <?php
    exit;
}

// --- Dashboard Logic ---

$logFile = __DIR__ . '/../access_log.csv';
$logs = [];
if (file_exists($logFile)) {
    $rows = array_map('str_getcsv', file($logFile));
    $header = array_shift($rows); // Remove header
    $logs = array_reverse($rows); // Newest first
}

// Totals
$total_pv = count($logs);
$today_pv = 0;
$today = date('Y-m-d');

// Stats
$referrers = [];
$pages = [];
$devices = [];

foreach ($logs as $log) {
    if (count($log) < 6)
        continue;

    // safe access
    $ts = $log[0];
    $visitor_ip = $log[1]; // Renamed to avoid collision
    $url = $log[2];
    $ref = $log[3];
    $dev = $log[5];

    // Today PV
    if (strpos($ts, $today) === 0) {
        $today_pv++;
    }

    // Clean URL (remove domain, keep path)
    $path = parse_url($url, PHP_URL_PATH);
    if (!isset($pages[$path]))
        $pages[$path] = 0;
    $pages[$path]++;

    // Referrer
    if (!isset($referrers[$ref]))
        $referrers[$ref] = 0;
    $referrers[$ref]++;

    // Device
    if (!isset($devices[$dev]))
        $devices[$dev] = 0;
    $devices[$dev]++;

    // Daily PV
    $d = substr($ts, 0, 10);
    if (isset($daily[$d])) {
        $daily[$d]++;
    }

    // Count IPs
    if (!isset($ips[$visitor_ip]))
        $ips[$visitor_ip] = 0;
    $ips[$visitor_ip]++;
}

// Sort Top 10 IPs
arsort($ips);
$top_ips = array_slice(array_keys($ips), 0, 10);
$top_ips_json = json_encode($top_ips);

arsort($pages);
arsort($referrers);
arsort($devices);

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yorimichi Dashboard</title>
    <!-- Chart.js for beautiful graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .logout {
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .card h3 {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .card .sub {
            font-size: 0.9rem;
            color: #27ae60;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .row {
                grid-template-columns: 1fr;
            }
        }

        .list-group {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .list-item .name {
            font-weight: 500;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 70%;
        }

        .list-item .count {
            font-weight: bold;
            color: #555;
        }

        .viz-bar {
            background: #eef2f7;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
            width: 100%;
        }

        .viz-fill {
            background: #3498db;
            height: 100%;
        }

        .geo-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .geo-flag {
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>よりみちリビング アクセス状況</h1>
            <a href="?logout=true" class="logout">ログアウト</a>
        </header>

        <div class="card-grid">
            <div class="card">
                <h3>今日のアクセス (PV)</h3>
                <div class="number"><?= $today_pv ?></div>
                <div class="sub">Total: <?= $total_pv ?> PV</div>
            </div>
            <div class="card">
                <h3>デバイス比率</h3>
                <?php
                $top_device = key($devices);
                $top_device_count = current($devices);
                if (!$top_device)
                    echo '<div class="number">-</div>';
                else
                    echo '<div class="number" style="font-size:1.5rem">' . $top_device . ' <span style="font-size:1rem;color:#888">(' . round($top_device_count / $total_pv * 100) . '%)</span></div>';
                ?>
            </div>
        </div>

        <!-- Trend Chart -->
        <div class="card" style="margin-bottom: 30px;">
            <h3>日別アクセス推移 (過去30日)</h3>
            <canvas id="trendChart" style="max-height: 300px;"></canvas>
        </div>

        <div class="row">
            <div class="card">
                <h3>人気ページランキング</h3>
                <ul class="list-group">
                    <?php
                    $i = 0;
                    foreach ($pages as $p => $c) {
                        if ($i++ >= 5)
                            break;
                        $percent = ($total_pv > 0) ? ($c / $total_pv) * 100 : 0;
                        echo "<li class='list-item'>
                                <div style='width:100%'>
                                    <div style='display:flex; justify-content:space-between'>
                                        <span class='name'>$p</span>
                                        <span class='count'>$c</span>
                                    </div>
                                    <div class='viz-bar'><div class='viz-fill' style='width:{$percent}%'></div></div>
                                </div>
                              </li>";
                    }
                    if (empty($pages))
                        echo "<li>データがありません</li>";
                    ?>
                </ul>
            </div>

            <div class="card">
                <h3>推定エリア (Top IPs)</h3>
                <div id="geo-list">読み込み中...</div>
            </div>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h3>リファラー (アクセス元)</h3>
            <ul class="list-group">
                <?php
                $i = 0;
                foreach ($referrers as $r => $c) {
                    if ($i++ >= 5)
                        break;
                    $percent = ($total_pv > 0) ? ($c / $total_pv) * 100 : 0;
                    $disp = $r;
                    if (strlen($disp) > 50)
                        $disp = substr($disp, 0, 50) . '...';
                    echo "<li class='list-item'>
                            <div style='width:100%'>
                                <div style='display:flex; justify-content:space-between'>
                                    <span class='name' title='$r'>$disp</span>
                                    <span class='count'>$c</span>
                                </div>
                                <div class='viz-bar'><div class='viz-fill' style='width:{$percent}%; background:#2ecc71'></div></div>
                            </div>
                          </li>";
                }
                if (empty($referrers))
                    echo "<li>データがありません</li>";
                ?>
            </ul>
        </div>

        <p style="text-align:center; color:#999; margin-top:50px; font-size:0.8rem;">
            Simple Analytics for Yorimichi Living<br>
            Server Time: <?= date('Y-m-d H:i:s') ?>
        </p>
    </div>

    <script>
        // --- 1. Trend Chart ---
        const dailyData = <?= json_encode($daily) ?>;
        // Reverse to show Old -> New