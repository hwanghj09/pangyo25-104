<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('935011793004-ehl34ubtiapitqb3f8dphkb9niu4g1dt.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://pghs104.dothome.co.kr/callback.php');

if (!isset($_GET['code'])) {
    die('인증 코드가 없습니다.');
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
if (isset($token['error'])) {
    die('로그인 실패: ' . $token['error']);
}

$client->setAccessToken($token['access_token']);
$oauth2Service = new Google_Service_Oauth2($client);
$userInfo = $oauth2Service->userinfo->get();

$conn = new mysqli('localhost', 'pghs104', 'qwaszx77^^', 'pghs104');
if ($conn->connect_error) {
    die('연결 실패: ' . $conn->connect_error);
}

// 사용자 정보
$googleId = $userInfo->id;
$email = $userInfo->email;
$name = $userInfo->name;
$picture = $userInfo->picture;

// 암호화 설정
define('ENCRYPTION_KEY', 'b72Jx9Lz5H3qYw8pA6sD2mKcVfP4G7QX');
define('ENCRYPTION_IV', 'mZ7f8wJpD6sK3Yq4');

// 암호화 함수
function encryptCookie($value) {
    return base64_encode(openssl_encrypt($value, 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV));
}

// 복호화 함수
function decryptCookie($value) {
    return openssl_decrypt(base64_decode($value), 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}

// 사용자 DB 저장 또는 업데이트
$query = "SELECT id, level FROM users WHERE google_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $googleId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // 기존 사용자 정보 업데이트
    $conn->query("UPDATE users SET email = '$email', name = '$name', picture = '$picture', updated_at = NOW() WHERE google_id = '$googleId'");
    $user = $result->fetch_assoc();
    $userLevel = $user['level'];
} else {
    // 신규 사용자 삽입 (기본 level = 1)
    $stmt = $conn->prepare("INSERT INTO users (google_id, email, name, picture, level) VALUES (?, ?, ?, ?, 1)");
    $stmt->bind_param("ssss", $googleId, $email, $name, $picture);
    $stmt->execute();
    $userLevel = 1;
}

// 쿠키에 암호화된 사용자 정보 저장
setcookie('user_name', encryptCookie($name), time() + 3600, "/", "pghs104.dothome.co.kr", isset($_SERVER['HTTPS']), true);
setcookie('user_email', encryptCookie($email), time() + 3600, "/", "pghs104.dothome.co.kr", isset($_SERVER['HTTPS']), true);
setcookie('google_id', $userInfo->id, time() + 3600, "/", "pghs104.dothome.co.kr", isset($_SERVER['HTTPS']), true);
// 메인 페이지로 리디렉션
header("Location: http://pghs104.dothome.co.kr");
exit();
?>
