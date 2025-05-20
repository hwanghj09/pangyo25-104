<?php
// 관리자 권한 확인
require_once 'check_admin.php';

$conn = new mysqli('localhost', 'pghs104', 'qwaszx77^^', 'pghs104');
if ($conn->connect_error) {
    die('연결 실패: ' . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $noticeId = $_GET['id'];

    // 공지사항 삭제 쿼리
    $deleteQuery = "DELETE FROM notices WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $noticeId);

    if ($stmt->execute()) {
        echo "공지사항이 삭제되었습니다.";
    } else {
        echo "공지사항 삭제 실패: " . $conn->error;
    }

    $stmt->close();
}
?>
<a href="manage_notices.php">뒤로 가기</a>
