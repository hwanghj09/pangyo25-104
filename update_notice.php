<?php
// 관리자 권한 확인
require_once 'check_admin.php';

$conn = new mysqli('localhost', 'pghs104', 'qwaszx77^^', 'pghs104');
if ($conn->connect_error) {
    die('연결 실패: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $noticeId = $_POST['notice_id'];
    $newTitle = $_POST['title'];
    $newContent = $_POST['content'];

    // 공지사항 수정 쿼리
    $updateQuery = "UPDATE notices SET title = ?, content = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('ssi', $newTitle, $newContent, $noticeId);

    if ($stmt->execute()) {
        echo "공지사항이 수정되었습니다.";
    } else {
        echo "공지사항 수정 실패: " . $conn->error;
    }

    $stmt->close();
}
?>
<a href="manage_notices.php">뒤로 가기</a>
