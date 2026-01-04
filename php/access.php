<?php
session_start();

// Password configuration
$evt = $_GET['evt'] ?? $_POST['evt'] ?? '';

// Default Password (Namako Event)
$PASSWORD = 'yorimichi1221';

// Dynamic Password Logic
if ($evt === '20260127') {
    $PASSWORD = 'yorimichi0127';
}

$error = '';

$evt = $_GET['evt'] ?? $_POST['evt'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_password = $_POST['password'] ?? '';

    if ($input_password === $PASSWORD) {
        $_SESSION['is_allowed'] = true;
        // Redirect with event ID if present
        $redirect_url = 'location.php';
        if (!empty($evt)) {
            $redirect_url .= '?evt=' . urlencode($evt);
        }
        header("Location: $redirect_url");
        exit;
    } else {
        $error = 'パスワードが違います';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Guest Access | Yorimichi Living</title>
    <link rel="icon" type="image/png" href="../assets/images/logo-transparent.png">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css?v=20251204-1">
    <script src="../js/analytics.js"></script>
    <style>
        .login-section {
            padding: 100px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-title {
            font-size: 1.5rem;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
        }

        .error-msg {
            color: #e74c3c;
            margin-bottom: 20px;
            font-size: 0.9rem;
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
        <section class="login-section">
            <div class="login-card">
                <h2 class="login-title">限定コンテンツ</h2>
                <p style="margin-bottom: 20px; font-size: 0.9rem;">パスワードを入力してください</p>

                <?php if ($error): ?>
                    <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="パスワード" required>
                        <input type="hidden" name="evt" value="<?php echo htmlspecialchars($evt); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-submit">送信</button>
                </form>
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