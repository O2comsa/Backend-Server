<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-size: 16px;
            margin: 0;
            padding: 0;
        }

        .certificate {
            height: 100vh;
            width: 100vw;
            background: url("{{ asset('assets/img/certificate-2.png') }}") no-repeat;
            background-size: contain;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .content {
            max-width: 80%; /* Use a relative unit for maximum width */
            padding: 20px;
            font-size: 1.5vw;
            text-align: center;
            direction: rtl;
            font-weight: 700;
            background-color: rgba(255, 255, 255, 0.8); /* Adding a semi-transparent background for better readability */
        }

        @media screen and (max-width: 600px) {
            /* Adjust styles for screens with a maximum width of 600px or smaller */
            .content {
                font-size: 2vw;
            }
        }
    </style>
</head>
<body>
<div class="certificate">
    <div class="content">
        <h1>تشهد الجمعية السعودية لمترجمي لغة الإشارة</h1>
        <h1>بان الاستاذ/ة {{ $user_name }} رقم الهوية الوطنية ({{ $nationalId}})</h1>
        <h1>قـد أتم دورة تدريبية عن بعد بعنوان {{ $courseName }}</h1>
        <h1>لمدة ({{ $days }}) أيام خلال الفترة إبتداءً من تاريخ:({{ $startDate }}) إلى تاريخ ({{ $endDate }})</h1>
        <h1>  بمعدل ({{ $hours }}) ساعة تدريبية وبناءً عليه منح هذه الشهادة</h1>
        <h1>سائلين الله عز وجل دوام التوفيق والسداد ....</h1>
    </div>
</div>
</body>
</html>
