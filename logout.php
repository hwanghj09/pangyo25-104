<?php
// 세션 시작
session_start();

// 세션 데이터 제거
session_unset();
session_destroy();

// 세션 쿠키 제거 (세션 아이디 쿠키)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// 쿠키 제거 (암호화된 쿠키 무효화)
setcookie('user_name', '', time() - 3600, "/", "pghs104.dothome.co.kr", isset($_SERVER['HTTPS']), true);
setcookie('user_email', '', time() - 3600, "/", "pghs104.dothome.co.kr", isset($_SERVER['HTTPS']), true);
setcookie('google_id', '', time() - 3600, "/", "pghs104.dothome.co.kr", isset($_SERVER['HTTPS']), true);

// 리다이렉트 (로그아웃 후 index.php로 이동)
header('Location: index.php');
exit;
?>
