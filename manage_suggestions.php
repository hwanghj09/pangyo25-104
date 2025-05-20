<?php
// 관리자 권한 확인
require_once 'check_admin.php';

$conn = new mysqli('localhost', 'pghs104', 'qwaszx77^^', 'pghs104');
if ($conn->connect_error) {
    die('연결 실패: ' . $conn->connect_error);
}

// 건의사항 목록 가져오기
$suggestionQuery = "SELECT * FROM suggestions";
$suggestionResult = $conn->query($suggestionQuery);
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>건의사항 관리</title>
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

        a {
            background-color: #007BFF;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
            margin-top: 20px;
        }

        a:hover {
            background-color: #0056b3;
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

            a {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <h1>건의사항 관리</h1>

    <table>
        <thead>
            <tr>
                <th>건의 제목</th>
                <th>건의자 이름</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($suggestion = $suggestionResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($suggestion['suggestion']); ?></td>
                    <td><?php echo htmlspecialchars($suggestion['user_name']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="index.php">뒤로 가기</a>
</body>

</html>
