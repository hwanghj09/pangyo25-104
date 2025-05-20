<?php
require_once 'vendor/autoload.php';

// Google 클라이언트 설정
$client = new Google_Client();
$client->setClientId('935011793004-ehl34ubtiapitqb3f8dphkb9niu4g1dt.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://pghs104.dothome.co.kr/callback.php');
$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);

// 데이터베이스 연결
$conn = new mysqli('localhost', 'pghs104', 'qwaszx77^^', 'pghs104');

if ($conn->connect_error) {
    die('연결 실패: ' . $conn->connect_error);
}

// 쿠키에서 사용자 이름 복호화
$user_name = null;
$userLevel = 0;
$noticeSubmitted = false;

if (isset($_COOKIE['user_name'])) {
    $user_name = decryptCookie($_COOKIE['user_name']);
    // 구글 ID가 저장된 쿠키도 확인
    if (isset($_COOKIE['google_id'])) {
        $google_id = $_COOKIE['google_id'];
        
        // 구글 ID를 기반으로 사용자 레벨 조회
        $stmt = $conn->prepare("SELECT level FROM users WHERE google_id = ?");
        $stmt->bind_param("s", $google_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // 유저 데이터가 있는지 확인
        $userData = $result->fetch_assoc();
        if ($userData) {
            $userLevel = $userData['level'];
        } else {
            $errorMessage = "사용자 정보를 찾을 수 없습니다.";
        }
    }
} else {
    // 쿠키에 정보가 없다면 로그인 페이지로 리다이렉트
    header('Location: ' . $client->createAuthUrl());
    exit();
}

// 공지사항 등록
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['content'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    // 유저의 level 확인
    if ($userLevel >= 5) {
        $stmt = $conn->prepare("INSERT INTO notices (title, content, author) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $content, $user_name);

        if ($stmt->execute()) {
            $noticeSubmitted = true;
        } else {
            $errorMessage = "공지사항 등록 실패: " . $stmt->error;
        }
    } else {
        $errorMessage = "공지사항을 등록할 권한이 없습니다.";
    }
}

function decryptCookie($encryptedData) {
    // 복호화 로직
    $key = 'b72Jx9Lz5H3qYw8pA6sD2mKcVfP4G7QX';
    $iv = 'mZ7f8wJpD6sK3Yq4';
    
    // 복호화
    $decryptedData = openssl_decrypt(base64_decode($encryptedData), 'AES-256-CBC', $key, 0, $iv);
    
    return $decryptedData;
}

// 공지사항 목록 출력
$noticeResult = $conn->query("SELECT * FROM notices ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>공지사항 관리 - 판교고 1-4반</title>
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
            --danger-color: #e74c3c;
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

        .card {
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

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--light-gray);
            border-radius: var(--radius);
            font-size: 16px;
            transition: var(--transition);
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

        .form-input:focus, .form-textarea:focus {
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

        .notice-list {
            margin-top: 30px;
        }

        .notice-item {
            background-color: var(--white);
            border-radius: var(--radius);
            margin-bottom: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .notice-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .notice-header {
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--white);
            transition: var(--transition);
        }

        .notice-header:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }

        .notice-title {
            font-weight: 600;
            flex-grow: 1;
        }

        .notice-date {
            font-size: 14px;
            color: #777;
            margin-right: 15px;
        }

        .notice-icon {
            color: var(--primary-color);
            transition: var(--transition);
        }

        .notice-content {
            padding: 20px;
            background-color: rgba(52, 152, 219, 0.03);
            border-top: 1px solid var(--light-gray);
            display: none;
        }

        .notice-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--light-gray);
            font-size: 14px;
            color: #777;
        }

        .notice-text {
            line-height: 1.7;
        }

        .notice-close {
            text-align: right;
            margin-top: 15px;
        }

        .notice-close-btn {
            color: var(--primary-color);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: var(--transition);
        }

        .notice-close-btn:hover {
            color: var(--secondary-color);
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

        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            border-left: 4px solid var(--danger-color);
            color: #c0392b;
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

        .auth-warning {
            background-color: rgba(231, 76, 60, 0.1);
            padding: 30px;
            text-align: center;
            border-radius: var(--radius);
        }

        .auth-warning i {
            font-size: 48px;
            color: var(--danger-color);
            margin-bottom: 15px;
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
                    <?php if ($user_name): ?>
                        <div class="user-profile">
                            <div class="avatar">
                                <?php echo substr($user_name, 0, 1); ?>
                            </div>
                            <span><?php echo htmlspecialchars($user_name); ?>님</span>
                        </div>
                        <a href="logout.php" class="menu-btn">
                            <i class="fas fa-sign-out-alt"></i> 로그아웃
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="container">                <div class="user-area">
                    <?php if (isset($user_name)): ?>
                        <div class="user-profile">
                            <div class="avatar"><?= strtoupper(substr($user_name, 0, 1)) ?></div>
                            <span><?= htmlspecialchars($user_name) ?>님</span>
                            <a href="logout.php" class="menu-btn">로그아웃</a>
                        </div>
                    <?php else: ?>
                        <a href="<?= $client->createAuthUrl() ?>" class="menu-btn">구글 로그인</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <h2 class="section-title">공지사항 관리</h2>
            
            <?php if ($noticeSubmitted): ?>
                <div class="alert alert-success">
                    공지사항이 성공적으로 등록되었습니다.
                </div>
            <?php elseif (isset($errorMessage)): ?>
                <div class="alert alert-danger">
                    <?= $errorMessage ?>
                </div>
            <?php endif; ?>

            <?php if ($userLevel >= 5): ?>
                <div class="card">
                    <h3 class="section-title">공지사항 등록</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="title" class="form-label">제목</label>
                            <input type="text" id="title" name="title" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="content" class="form-label">내용</label>
                            <textarea id="content" name="content" class="form-textarea" required></textarea>
                        </div>
                        <button type="submit" class="form-submit">등록</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="auth-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>공지사항을 등록할 권한이 없습니다.</p>
                </div>
            <?php endif; ?>

            <div class="notice-list">
                <h3 class="section-title">공지사항 목록</h3>

                <?php while ($notice = $noticeResult->fetch_assoc()): ?>
                    <div class="notice-item">
                        <div class="notice-header">
                            <span class="notice-title"><?= htmlspecialchars($notice['title']) ?></span>
                            <span class="notice-date"><?= date('Y-m-d', strtotime($notice['created_at'])) ?></span>
                        </div>
                        <div class="notice-content">
                            <p class="notice-text"><?= nl2br(htmlspecialchars($notice['content'])) ?></p>
                            <div class="notice-meta">
                                <span>작성자: <?= htmlspecialchars($notice['author']) ?></span>
                            </div>
                            <div class="notice-close">
                                <button class="notice-close-btn" onclick="this.closest('.notice-item').querySelector('.notice-content').style.display = 'none';">
                                    <i class="fas fa-times"></i> 닫기
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 판교고 1-4반</p>
    </footer>
    
    <script>
        // 공지사항 클릭 시 내용 토글
        document.querySelectorAll('.notice-header').forEach(header => {
            header.addEventListener('click', () => {
                const content = header.nextElementSibling;
                content.style.display = (content.style.display === 'none' || content.style.display === '') ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>
