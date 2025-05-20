<?php
define('ENCRYPTION_KEY', 'b72Jx9Lz5H3qYw8pA6sD2mKcVfP4G7QX');
define('ENCRYPTION_IV', 'mZ7f8wJpD6sK3Yq4'); // 16바이트 IV

// DB 연결
$conn = new mysqli('localhost', 'pghs104', 'qwaszx77^^', 'pghs104');
if ($conn->connect_error) {
    die('연결 실패: ' . $conn->connect_error);
}

// 쿠키에서 암호화된 사용자 이름 가져오기
if (!isset($_COOKIE['user_name'])) {
    die('로그인이 필요합니다.');
}

$encrypted_user_name = $_COOKIE['user_name'];

// 복호화
$user_name = openssl_decrypt(
    base64_decode($encrypted_user_name), // 암호화된 데이터를 base64 디코딩
    'aes-256-cbc', // 사용한 암호화 알고리즘
    ENCRYPTION_KEY, // 암호화 키
    0, // 기본 옵션
    ENCRYPTION_IV // 초기화 벡터(IV)
);

if ($user_name === false) {
    die('복호화 실패');
}

// 사용자 정보 가져오기
$query = "SELECT phone, instagram, birthday, privacy_setting FROM users WHERE name = '$user_name'";
$result = $conn->query($query);
$user = $result->fetch_assoc();

// 정보 업데이트 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $conn->real_escape_string($_POST['phone']);
    $instagram = isset($_POST['instagram']) ? $conn->real_escape_string($_POST['instagram']) : ''; // 인스타그램 필드가 비어있을 경우 빈 문자열로 처리
    $birthday = $conn->real_escape_string($_POST['birthday']);
    $privacy_setting = $conn->real_escape_string($_POST['privacy_setting']); // 공개/비공개 설정

    // 인스타그램이 비어있을 때 NULL로 저장할 수 있도록 처리
    $updateQuery = "UPDATE users SET phone = '$phone', instagram = '" . ($instagram ? $instagram : 'NULL') . "', birthday = '$birthday', privacy_setting = '$privacy_setting' WHERE name = '$user_name'";

    if ($conn->query($updateQuery)) {
        echo '<script>alert("정보가 업데이트되었습니다."); window.location.href = "manage_user_info.php";</script>';
    } else {
        echo '업데이트 실패: ' . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>개인정보 수정</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            text-align: center;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007BFF;
        }
    </style>
</head>

<body>
    <h1>개인정보 수정</h1>
    <form action="" method="post">
        <label>전화번호: <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required></label><br>
        <label>인스타그램: <input type="text" name="instagram" value="<?php echo htmlspecialchars($user['instagram'] ?? ''); ?>"></label><br> <!-- 인스타그램 필드는 optional -->
        <label>생일: <input type="date" name="birthday" value="<?php echo htmlspecialchars($user['birthday'] ?? ''); ?>" required></label><br>
        
        <label>공개 여부:
            <select name="privacy_setting" required>
                <option value="public" <?php echo ($user['privacy_setting'] === 'public') ? 'selected' : ''; ?>>공개</option>
                <option value="private" <?php echo ($user['privacy_setting'] === 'private') ? 'selected' : ''; ?>>비공개</option>
            </select>
        </label><br>

        <button type="submit">정보 업데이트</button>
    </form>

    <a href="index.php">홈으로 돌아가기</a>
</body>

</html>
