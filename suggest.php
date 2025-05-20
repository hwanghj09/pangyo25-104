<?php
// 암호화 설정
define('ENCRYPTION_KEY', 'b72Jx9Lz5H3qYw8pA6sD2mKcVfP4G7QX');
define('ENCRYPTION_IV', 'mZ7f8wJpD6sK3Yq4'); // 16바이트 IV

// 쿠키 복호화 함수
function decryptCookie($value) {
    return openssl_decrypt(base64_decode($value), 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}

// 로그인 여부 확인
$userName = isset($_COOKIE['user_name']) ? decryptCookie($_COOKIE['user_name']) : null;
$userLevel = isset($_COOKIE['user_level']) ? decryptCookie($_COOKIE['user_level']) : null; // 레벨 정보

// 건의사항 제출 처리
$submissionSuccess = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['suggestion'])) {
    $suggestion = $_POST['suggestion'];

    // DB 연결
    $conn = new mysqli('localhost', 'pghs104', 'qwaszx77^^', 'pghs104');
    if ($conn->connect_error) {
        die('연결 실패: ' . $conn->connect_error);
    }

    // 건의사항 삽입
    $stmt = $conn->prepare("INSERT INTO suggestions (user_name, suggestion) VALUES (?, ?)");
    $stmt->bind_param("ss", $userName, $suggestion);
    $stmt->execute();
    $stmt->close();

    $submissionSuccess = true;
}

?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>건의사항 - 판교고 1-4반</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --bg-color: #f8f9fa;
            --text-color: #333;
            --light-gray: #e9ecef;
            --white: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --radius: 8px;
            --transition: all 0.3s ease;
            --success-color: #2ecc71;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            padding: 0;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background-color: var(--primary-color);
            color: var(--white);
            padding: 20px 0;
            box-shadow: var(--shadow);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .user-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .menu-btn {
            background-color: var(--white);
            color: var(--primary-color);
            border: none;
            padding: 10px 15px;
            border-radius: var(--radius);
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .menu-btn:hover {
            background-color: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
        }

        main {
            padding: 30px 0;
            min-height: calc(100vh - 180px);
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
        }

        .suggestion-form {
            background-color: var(--white);
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--light-gray);
            border-radius: var(--radius);
            font-size: 16px;
            transition: var(--transition);
            min-height: 150px;
            resize: vertical;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        .form-submit {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 12px 20px;
            border-radius: var(--radius);
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .form-submit:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .form-instructions {
            background-color: rgba(52, 152, 219, 0.1);
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 var(--radius) var(--radius) 0;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--secondary-color);
            transform: translateX(-5px);
        }

        .login-message {
            background-color: var(--white);
            border-radius: var(--radius);
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow);
        }

        .login-message p {
            margin-bottom: 20px;
            font-size: 18px;
        }

        .alert {
            padding: 15px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease-in-out;
        }

        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            border-left: 4px solid var(--success-color);
            color: #27ae60;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        footer {
            background-color: var(--primary-color);
            color: var(--white);
            text-align: center;
            padding: 20px 0;
            margin-top: 50px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1 onclick="location.href='index.php'">판교고 1-4반</h1>
                </div>
                <div class="user-area">
                    <?php if ($userName): ?>
                        <div class="user-profile">
                            <div class="avatar">
                                <?php echo substr($userName, 0, 1); ?>
                            </div>
                            <span><?php echo htmlspecialchars($userName); ?>님</span>
                        </div>
                        <a href="logout.php" class="menu-btn">
                            <i class="fas fa-sign-out-alt"></i> 로그아웃
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="menu-btn">
                            <i class="fas fa-sign-in-alt"></i> 로그인
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> 메인으로 돌아가기
        </a>

        <h2 class="section-title">
            <i class="fas fa-lightbulb"></i> 건의사항
        </h2>

        <?php if ($submissionSuccess): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> 건의사항이 성공적으로 제출되었습니다. 소중한 의견 감사합니다.
            </div>
        <?php endif; ?>

        <?php if ($userName): ?>
            <div class="suggestion-form">
                <div class="form-instructions">
                    <p><i class="fas fa-info-circle"></i> 학급 운영에 대한 건의사항이나 개선점을 자유롭게 작성해주세요. 모든 의견은 소중히 검토됩니다.</p>
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label for="suggestion" class="form-label">건의 내용</label>
                        <textarea name="suggestion" id="suggestion" class="form-textarea" rows="8" placeholder="학급 운영에 대한 건의사항을 작성해주세요." required></textarea>
                    </div>
                    <button type="submit" class="form-submit">
                        <i class="fas fa-paper-plane"></i> 건의사항 제출하기
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="login-message">
                <i class="fas fa-lock" style="font-size: 48px; color: var(--primary-color); margin-bottom: 20px;"></i>
                <p>건의사항을 작성하려면 먼저 로그인해주세요.</p>
                <a href="login.php" class="form-submit">
                    <i class="fas fa-sign-in-alt"></i> 로그인하기
                </a>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 판교고등학교 1-4반. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>