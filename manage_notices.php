<?php
// 관리자 권한 확인
require_once 'check_admin.php';

$conn = new mysqli('localhost', 'pghs104', 'qwaszx77^^', 'pghs104');
if ($conn->connect_error) {
    die('연결 실패: ' . $conn->connect_error);
}

// 공지사항 목록 가져오기
$noticeQuery = "SELECT * FROM notices ORDER BY created_at DESC";
$noticeResult = $conn->query($noticeQuery);
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>공지사항 관리</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 80%;
            margin: 20px 0;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        table th, table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table td {
            background-color: #f9f9f9;
        }

        form {
            display: inline-block;
            margin-top: 10px;
            text-align: left;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .delete-button {
            background-color: #e74c3c;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .delete-button:hover {
            background-color: #c0392b;
        }

        .level-info {
            margin-top: 15px;
            font-size: 16px;
            color: #555;
            text-align: center;
        }

        /* 모바일 반응형 */
        @media screen and (max-width: 768px) {
            table {
                width: 100%;
            }

            input[type="text"], textarea {
                width: 100%;
            }

            input[type="submit"], .delete-button {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <h1>공지사항 관리</h1>

    <table>
        <thead>
            <tr>
                <th>공지 제목</th>
                <th>내용</th>
                <th>작성 날짜</th>
                <th>행동</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($notice = $noticeResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($notice['title']); ?></td>
                    <td><?php echo htmlspecialchars($notice['content']); ?></td>
                    <td><?php echo htmlspecialchars($notice['created_at']); ?></td>
                    <td>
                        <!-- 공지사항 수정 폼 -->
                        <form action="update_notice.php" method="POST">
                            <input type="hidden" name="notice_id" value="<?php echo $notice['id']; ?>">
                            <input type="text" name="title" value="<?php echo htmlspecialchars($notice['title']); ?>" required>
                            <textarea name="content" required><?php echo htmlspecialchars($notice['content']); ?></textarea>
                            <input type="submit" value="수정">
                        </form>
                        <!-- 공지사항 삭제 버튼 -->
                        <a href="delete_notice.php?id=<?php echo $notice['id']; ?>" class="delete-button">삭제</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="index.php">뒤로 가기</a>
</body>

</html>
