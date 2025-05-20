<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>학사 일정 캘린더</title>
    <style>
        /* styles.css */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
        }

        .calendar-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 1.5rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            font-size: 1.2rem;
        }

        td {
            height: 50px;
            vertical-align: middle;
        }

        td.empty {
            background-color: #f0f0f0;
        }

        td.has-event {
            background-color: #f4c542;
            font-weight: bold;
        }

        td:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>

<body>
    <div class="calendar-container">
        <h1>학사 일정</h1>
        <div class="calendar">
            <div class="calendar-header">
                <button class="prev" onclick="changeMonth(-1)">&#10094;</button>
                <h2 id="monthYear"></h2>
                <button class="next" onclick="changeMonth(1)">&#10095;</button>
            </div>
            <table id="calendarTable">
                <thead>
                    <tr>
                        <th>일</th>
                        <th>월</th>
                        <th>화</th>
                        <th>수</th>
                        <th>목</th>
                        <th>금</th>
                        <th>토</th>
                    </tr>
                </thead>
                <tbody id="calendarBody"></tbody>
            </table>
        </div>
    </div>

    <script>
        // script.js
        const monthNames = [
            "1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"
        ];

        let currentMonth = new Date().getMonth(); // 현재 월
        let currentYear = new Date().getFullYear(); // 현재 년도

        const events = {
            '2025-03-4': '학기 시작',
            '2025-03-22': '1학기 리더십캠프',
            '2025-03-26': '전국연합학력 평가',
            '2025-04-16': '동아리',
            '2025-04-24': '1학기 1차 지필평가',
            '2025-04-25': '1학기 1차 지필평가',
            '2025-04-258': '1학기 1차 지필평가',
            '2025-04-29': '1학기 1차 지필평가',
            '2025-05-02': '건강검사(1~3교시)',
            '2025-05-18': '동아리',
            '2025-05-30': '1학기 2차 지필평가',
            '2025-06-01': '1학기 2차 지필평가',
            '2025-06-02': '1학기 2차 지필평가',
            '2025-06-03': '1학기 2차 지필평가',
            '2025-07-17': '방학식',
            '2025-08-01' : '황현준 생일',
            '2025-05-26' : '이수민 생일',
            '2025-08-06' : '차정우 생일',
            '2025-02-18' : '김태리 생일'
        };

        // 월과 연도를 변경하는 함수
        function changeMonth(direction) {
            currentMonth += direction;

            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            } else if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }

            renderCalendar();
        }

        // 캘린더 렌더링 함수
        function renderCalendar() {
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDate = new Date(currentYear, currentMonth + 1, 0);
            const monthYear = document.getElementById('monthYear');
            const calendarBody = document.getElementById('calendarBody');

            monthYear.textContent = `${monthNames[currentMonth]} ${currentYear}`;

            let calendarHtml = '';
            let dayOfWeek = firstDay.getDay(); // 첫 번째 날의 요일 (0 = 일요일, 1 = 월요일 등)
            let totalDays = lastDate.getDate(); // 해당 월의 마지막 날짜

            // 빈 칸을 채우는 부분 (이전 달의 일)
            for (let i = 0; i < dayOfWeek; i++) {
                calendarHtml += `<td class="empty"></td>`;
            }

            // 현재 월의 날짜들
            for (let day = 1; day <= totalDays; day++) {
                const dateString = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const hasEvent = events[dateString] ? 'has-event' : '';

                calendarHtml += `<td class="${hasEvent}">
                                    ${day}
                                    ${hasEvent ? `<br><span>${events[dateString]}</span>` : ''}
                                  </td>`;

                // 토요일에 새로운 행 시작
                if ((day + dayOfWeek) % 7 === 0) {
                    calendarHtml += '</tr><tr>';
                }
            }

            // 나머지 빈 칸 채우기
            const remainingDays = 7 - ((totalDays + dayOfWeek) % 7);
            if (remainingDays < 7) {
                for (let i = 0; i < remainingDays; i++) {
                    calendarHtml += `<td class="empty"></td>`;
                }
            }

            calendarBody.innerHTML = calendarHtml;
        }

        // 초기 렌더링
        renderCalendar();
    </script>
</body>

</html>
