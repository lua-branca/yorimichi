<?php
session_start();

if (empty($_SESSION['is_allowed'])) {
    header('Location: access.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Event Location | Yorimichi Living</title>
    <link rel="icon" type="image/png" href="../assets/images/logo-transparent.png">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css?v=20251204-1">
    <script src="../js/analytics.js"></script>
    <style>
        .location-section {
            padding: 60px 0;
            text-align: center;
        }

        .container-narrow {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section-title {
            font-size: 2rem;
            margin-bottom: 40px;
        }

        .map-container {
            position: relative;
            padding-bottom: 56.25%;
            /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .access-info {
            background: white;
            padding: 40px;
            border-radius: 20px;
            text-align: left;
        }

        .access-info h3 {
            margin-bottom: 20px;
            font-size: 1.2rem;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
    </style>
</head>

<body>
    <header class="site-header">
        <div class="container header-container">
            <h1 class="logo">
                <a href="../index.html">
                    <img src="../assets/images/logo-transparent.png" alt="Yorimichi Living">
                </a>
            </h1>
        </div>
    </header>

    <main>
        <section class="location-section">
            <div class="container-narrow">
                <h2 class="section-title">会場アクセス</h2>

                <div class="map-container">
                    <iframe src="https://maps.google.com/maps?q=東京都千代田区神田淡路町1-15-12&t=&z=15&ie=UTF8&iwloc=&output=embed"
                        width="600" height="450" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

                <div class="access-info">
                    <h3>住所</h3>
                    <p>〒101-0063<br>東京都千代田区神田淡路町1-15-12</p>

                    <h3 style="margin-top: 30px;">アクセス</h3>
                    <p>
                        ・東京メトロ丸ノ内線「淡路町駅」A5出口より徒歩3分<br>
                        ・都営新宿線「小川町駅」A5出口より徒歩3分<br>
                        ・JR中央・総武線「御茶ノ水駅」聖橋口より徒歩5分
                    </p>

                    <?php
                    // Dynamic Calendar Logic
                    $evt_id = $_GET['evt'] ?? '';

                    // Default Event (12/21)
                    $cal_title = '料理研究家杉なまこ先生の手料理ホムパ';
                    $cal_dates = '20251221T040000Z/20251221T080000Z'; // 13:00-17:00 JST -> 04:00-08:00 UTC
                    $cal_loc = '東京都千代田区神田淡路町1-15-12';
                    $cal_details = 'よりみちリビングでのイベントです。';

                    // Switch logic for future events (Example)
                    // if ($evt_id === 'event_0110') { ... }
                    
                    $cal_url = "https://www.google.com/calendar/render?action=TEMPLATE";
                    $cal_url .= "&text=" . urlencode($cal_title);
                    $cal_url .= "&dates=" . $cal_dates;
                    $cal_url .= "&location=" . urlencode($cal_loc);
                    $cal_url .= "&details=" . urlencode($cal_details);
                    ?>

                    <a href="<?php echo htmlspecialchars($cal_url); ?>" target="_blank" class="btn btn-primary"
                        style="margin-top: 30px; width: 100%; display: block; text-align: center;">
                        📅 Googleカレンダーに追加
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p class="copyright" style="text-align: center;">&copy; 2025 Yorimichi Living</p>
        </div>
    </footer>
</body>

</html>