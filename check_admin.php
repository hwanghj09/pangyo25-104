<?php
require_once 'vendor/autoload.php';

// 쿠키 암호화 관련 함수들
define('ENCRYPTION_KEY', 'b72Jx9Lz5H3qYw8pA6sD2mKcVfP4G7QX');
define('ENCRYPTION_IV', 'mZ7f8wJpD6sK3Yq4'); // 16바이트 IV

function encryptCookie($value) {
    return base64_encode(openssl_encrypt($value, 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV));
}

function decryptCookie($value) {
    return openssl_decrypt(base64_decode($value), 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}

// 로그인 여부 확인
$userName = isset($_COOKIE['user_name']) ? decryptCookie($_COOKIE['user_name']) : null;

if (!$userName) {
    die('로그인 정보가 없습니다.');
}

// DB 연결
$conn = new mysqli('localhost', 'pghs104', 'qwaszx77^^', 'pghs104');

if ($conn->connect_error) {
    die('연결 실패: ' . $conn->connect_error);
}

// 사용자의 user_level 가져오기
$userLevelQuery = "SELECT level FROM users WHERE name = ?";
$stmt = $conn->prepare($userLevelQuery);
$stmt->bind_param('s', $userName);
$stmt->execute();
$stmt->bind_result($userLevel);
$stmt->fetch();
$stmt->close();

// 관리자 권한 확인
if (!$userLevel || $userLevel < 5) {
    die('관리자 권한이 없습니다.');
}
?>
