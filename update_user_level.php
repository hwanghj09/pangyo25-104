<?php
// 관리자 권한 확인
require_once 'check_admin.php';

$conn = new mysqli('localhost', 'pghs104', 'qwaszx77^^', 'pghs104');
if ($conn->connect_error) {
    die('연결 실패: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['user_id'];
    $newLevel = $_POST['level'];

    // 레벨 수정 쿼리
    $updateQuery = "UPDATE users SET level = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('ii', $newLevel, $userId);

    if ($stmt->execute()) {
        echo "사용자 레벨이 수정되었습니다.";
    } else {
        echo "레벨 수정 실패: " . $conn->error;
    }

    $stmt->close();
}
?>
<a href="manage_users.php">뒤로 가기</a>
