<?php
$redirect_url = $_GET['redirect'] ?? '';
if (!empty($redirect_url) && !preg_match('/^https?:\/\//', $redirect_url)) {
    $redirect_url = ''; // Invalid URL safety check
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Success | Yorimichi Living</title>
    <link rel="icon" type="image/png" href="../assets/images/logo-transparent.png">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=Outfit:wght@300;400;500&display=swap"
        rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="../css/style.css?v=20251204-1">
    <!-- Analytics -->
    <script src="../js/analytics.js"></script>
    <style>
        .success-section {
            padding: 100px 0;
            text-align: center;
        }

        .success-card {
            background: white;
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin: 0 auto;
        }

        .success-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            display: block;
        }

        .success-title {
            font-family: 'Noto Sans JP', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #333;
        }

        .success-text {
            font-size: 1rem;
            line-height: 1.8;
            color: #666;
            margin-bottom: 30px;
        }
    </style>
    <?php if ($redirect_url): ?>
        <script>
            setTimeout(function () {
                window.location.href = '<?php echo $redirect_url; ?>';
            }, 3000); // 3 seconds delay
        </script>
    <?php endif; ?>
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
        <section class="success-section">
            <div class="container">
                <div class="success-card">
                    <span class="success-icon">🎉</span>
                    <h2 class="success-title">お申し込み完了</h2>

                    <?php if ($redirect_url): ?>
                        <p class="success-text">
                            イベントへのお申し込みありがとうございます。<br>
                            決済画面へ移動します（3秒後に自動で切り替わります）。
                        </p>
                        <p>
                            <a href="<?php echo $redirect_url; ?>" class="btn btn-primary">今すぐ支払う</a>
                        </p>
                    <?php else: ?>
                        <p class="success-text">
                            イベントへのお申し込みありがとうございます。<br>
                            ご登録いただいたメールアドレスへ確認メールをお送りしました。<br>
                            当日お会いできるのを楽しみにしております。
                        </p>
                        <a href="../index.html" class="btn btn-primary">トップページへ戻る</a>
                    <?php endif; ?>

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