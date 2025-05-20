<?php
// DB 연결
$conn = new mysqli('localhost', 'pghs104', 'qwaszx77^^', 'pghs104');
if ($conn->connect_error) {
    die('연결 실패: ' . $conn->connect_error);
}

// 'public'인 유저 정보 가져오기
$query = "SELECT name, phone, instagram, birthday FROM users WHERE privacy_setting = 'public'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // 공개된 유저 정보 출력
    echo "<h1>공개된 사용자 목록</h1>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 80%; margin: 0 auto;'>
            <thead>
                <tr>
                    <th>이름</th>
                    <th>전화번호</th>
                    <th>인스타그램</th>
                    <th>생일</th>
                </tr>
            </thead>
            <tbody>";

    while ($user = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($user['name']) . "</td>
                <td>" . htmlspecialchars($user['phone']) . "</td>
                <td>" . htmlspecialchars($user['instagram']) . "</td>
                <td>" . htmlspecialchars($user['birthday']) . "</td>
              </tr>";
    }

    echo "</tbody>
        </table>";
} else {
    echo "<p>공개된 사용자가 없습니다.</p>";
}

// DB 연결 종료
$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>공개된 사용자 목록</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        td {
            background-color: #fff;
        }
    </style>
</head>
<body>
    <a href="index.php" style="text-decoration: none; color: #007BFF; display: block; text-align: center; margin-top: 20px;">홈으로 돌아가기</a>
</body>
</html>
