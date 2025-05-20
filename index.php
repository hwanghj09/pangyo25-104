<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('935011793004-ehl34ubtiapitqb3f8dphkb9niu4g1dt.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://pghs104.dothome.co.kr/callback.php');
$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);

$loginUrl = $client->createAuthUrl();

// 암호화 설정
define('ENCRYPTION_KEY', 'b72Jx9Lz5H3qYw8pA6sD2mKcVfP4G7QX');
define('ENCRYPTION_IV', 'mZ7f8wJpD6sK3Yq4'); // 16바이트 IV

// 쿠키 암호화 함수
function encryptCookie($value) {
    return base64_encode(openssl_encrypt($value, 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV));
}

// 쿠키 복호화 함수
function decryptCookie($value) {
    return openssl_decrypt(base64_decode($value), 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}

// 로그인 여부 확인
$userEmail = isset($_COOKIE['user_email']) ? decryptCookie($_COOKIE['user_email']) : null;
$userName = isset($_COOKIE['user_name']) ? decryptCookie($_COOKIE['user_name']) : null;

// 로그인된 사용자가 있다면 user_level을 데이터베이스에서 가져오기
$userLevel = null;
if ($userEmail) {
    // DB 연결
    $conn = new mysqli('localhost', 'pghs104', 'qwaszx77^^', 'pghs104');

    if ($conn->connect_error) {
        die('연결 실패: ' . $conn->connect_error);
    }

    // 사용자 정보 가져오기
    $userQuery = "SELECT level FROM users WHERE email = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param('s', $userEmail);
    $stmt->execute();
    $stmt->bind_result($userLevel);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>판교고 1-4반</title>
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

        .login-btn, .menu-btn {
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

        .login-btn:hover, .menu-btn:hover {
            background-color: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
        }

        main {
            padding: 30px 0;
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
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

        .notice-list {
            margin-bottom: 30px;
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
            border-bottom: 1px solid var(--light-gray);
            transition: var(--transition);
        }

        .notice-header:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }

        .notice-title {
            font-weight: 600;
            flex-grow: 1;
        }

        .notice-icon {
            color: var(--primary-color);
            font-size: 18px;
            transition: var(--transition);
        }

        .notice-content {
            padding: 15px 20px;
            background-color: rgba(52, 152, 219, 0.03);
            border-top: 1px solid var(--light-gray);
            line-height: 1.7;
        }

        .menu-section {
            margin-top: 30px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .menu-card {
            background-color: var(--white);
            border-radius: var(--radius);
            padding: 20px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
            text-decoration: none;
            color: var(--text-color);
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .menu-icon {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .menu-title {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .menu-desc {
            font-size: 14px;
            color: #666;
        }

        .admin-section {
            margin-top: 40px;
            background-color: var(--white);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .admin-title {
            font-size: 20px;
            margin-bottom: 15px;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-menu {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .admin-btn {
            background-color: var(--light-gray);
            color: var(--text-color);
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

        .admin-btn:hover {
            background-color: var(--primary-color);
            color: var(--white);
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

            .menu-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
    <script>
        // 공지사항 클릭 시 내용 토글 함수
        function toggleContent(id) {
            var content = document.getElementById('content_' + id);
            var icon = document.getElementById('icon_' + id);
            
            if (content.style.display === 'none' || content.style.display === '') {
                content.style.display = 'block';
                icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
            } else {
                content.style.display = 'none';
                icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        }
    </script>
</head>

<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>판교고 1-4반</h1>
                </div>
                <div class="user-area">
                    <?php if ($userName): ?>
                        <div class="user-profile">
                            <span><?php echo htmlspecialchars($userName); ?>님</span>
                        </div>
                        <a href="logout.php" class="menu-btn">
                            <i class="fas fa-sign-out-alt"></i> 로그아웃
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $loginUrl; ?>" class="login-btn">
                            <i class="fab fa-google"></i> 구글로 로그인
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <ins class="kakao_ad_area" style="display:none;"
data-ad-unit = "DAN-U5hZzuDEjCmVbpMR"
data-ad-width = "320"
data-ad-height = "50"></ins>
<script type="text/javascript" src="//t1.daumcdn.net/kas/static/ba.min.js" async></script>
    <main class="container">
        <section class="notice-section">
            <h2 class="section-title">최근 공지</h2>
            <div class="notice-list">
                <?php
                // DB 연결
                $conn = new mysqli('localhost', 'pghs104', 'qwaszx77^^', 'pghs104');

                if ($conn->connect_error) {
                    die('연결 실패: ' . $conn->connect_error);
                }

                // 최근 공지 3개 가져오기
                $noticeQuery = "SELECT * FROM notices ORDER BY created_at DESC LIMIT 3";
                $noticeResult = $conn->query($noticeQuery);

                if ($noticeResult->num_rows > 0) {
                    while ($notice = $noticeResult->fetch_assoc()) {
                        $noticeId = $notice['id'];
                        $noticeTitle = htmlspecialchars($notice['title']);
                        $noticeContent = nl2br(htmlspecialchars($notice['content']));
                        echo "<div class='notice-item'>";
                        echo "<div class='notice-header' onclick='toggleContent($noticeId)'>";
                        echo "<h3 class='notice-title'>$noticeTitle</h3>";
                        echo "<i id='icon_$noticeId' class='notice-icon fas fa-chevron-down'></i>";
                        echo "</div>";
                        echo "<div id='content_$noticeId' class='notice-content' style='display:none;'>$noticeContent</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>등록된 공지가 없습니다.</p>";
                }
                ?>
            </div>
            
            <?php if ($userName): ?>
                <div class="menu-section">
                    <h2 class="section-title">메뉴</h2>
                    <div class="menu-grid">
                        <a href="notices.php" class="menu-card">
                            <div class="menu-icon"><i class="fas fa-bullhorn"></i></div>
                            <h3 class="menu-title">공지사항</h3>
                            <p class="menu-desc">학급 공지사항을 확인하세요</p>
                        </a>
                        <a href="suggest.php" class="menu-card">
                            <div class="menu-icon"><i class="fas fa-lightbulb"></i></div>
                            <h3 class="menu-title">건의사항</h3>
                            <p class="menu-desc">학급 발전을 위한 아이디어를 제안하세요</p>
                        </a>
                        <a href="calendar.php" class="menu-card">
                            <div class="menu-icon"><i class="fas fa-calendar-alt"></i></div>
                            <h3 class="menu-title">학급 일정</h3>
                            <p class="menu-desc">시험 및 행사 일정을 확인하세요</p>
                        </a>
                        <a href="https://www.instagram.com/pghs104?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="menu-card">
                            <div class="menu-icon"><i class="fas fa-images"></i></div>
                            <h3 class="menu-title">인스타</h3>
                            <p class="menu-desc">학급 활동 낭만 넘치는 사진을 확인하세요</p>
                        </a>
                        <a href="manage_user_info.php" class="menu-card">
                            <div class="menu-icon"><i class="fas fa-calendar-alt"></i></div>
                            <h3 class="menu-title">개인정보</h3>
                            <p class="menu-desc">개인정보를 추가하세요</p>
                        </a>
                        <a href="view_public_users.php" class="menu-card">
                            <div class="menu-icon"><i class="fas fa-calendar-alt"></i></div>
                            <h3 class="menu-title">정보</h3>
                            <p class="menu-desc">친구들의 정보를 확인하세요</p>
                        </a>
                        
                    </div>
                </div>
                
                <?php if ($userLevel >= 5): ?>
                    <div class="admin-section">
                        <h2 class="admin-title"><i class="fas fa-shield-alt"></i> 관리자 메뉴</h2>
                        <div class="admin-menu">
                            <a href="manage_users.php" class="admin-btn">
                                <i class="fas fa-users"></i> 사용자 관리
                            </a>
                            <a href="manage_notices.php" class="admin-btn">
                                <i class="fas fa-clipboard-list"></i> 공지사항 관리
                            </a>
                            <a href="manage_suggestions.php" class="admin-btn">
                                <i class="fas fa-comments"></i> 건의사항 관리
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="not-logged-in" style="text-align: center; margin: 50px 0;">
                    <p style="margin-bottom: 20px; font-size: 18px;">학급 페이지의 모든 기능을 이용하려면 로그인하세요.</p>
                    <a href="<?php echo $loginUrl; ?>" class="login-btn" style="padding: 12px 25px; font-size: 16px;">
                        <i class="fab fa-google"></i> 구글로 로그인
                    </a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="container">

            <p>&copy; 2025 판교고등학교 1-4반. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>