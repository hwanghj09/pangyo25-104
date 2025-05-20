<?php
// 관리자 권한 확인
require_once 'check_admin.php';

// DB 연결
$conn = new mysqli('localhost', 'pghs104', 'qwaszx77^^', 'pghs104');
if ($conn->connect_error) {
    die('연결 실패: ' . $conn->connect_error);
}

// 사용자 목록 가져오기
$userQuery = "SELECT * FROM users";
$userResult = $conn->query($userQuery);
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>사용자 관리</title>
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

        a {
            color: #007BFF;
            text-decoration: none;
            font-size: 16px;
            margin-top: 10px;
            display: block;
            text-align: center;
        }

        table {
            width: 90%;
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
            display: inline;
        }

        input[type="number"] {
            width: 60px;
            padding: 5px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
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

            input[type="number"], input[type="submit"] {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <h1>사용자 관리</h1>
    <div class="level-info">
        <p>1 : 일반학생, 5 : 부반장, 6 : 반장, 50 : 쌤</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>사용자 ID</th>
                <th>이름</th>
                <th>전화번호</th>
                <th>인스타그램</th>
                <th>생일</th>
                <th>레벨</th>
                <th>행동</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $userResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($user['instagram'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($user['birthday'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($user['level']); ?></td>
                    <td>
                        <!-- 사용자 레벨 수정 폼 -->
                        <form action="update_user_level.php" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <input type="number" name="level" value="<?php echo $user['level']; ?>" required>
                            <input type="submit" value="레벨 수정">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="index.php">뒤로 가기</a>
</body>

</html>
