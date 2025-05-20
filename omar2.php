<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <link href="fontawesome/css/fontawesome.css" rel="stylesheet" />
    <link href="fontawesome/css/brands.css" rel="stylesheet" />
    <link href="fontawesome/css/solid.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-JobWAqYk5CSjWuVV3mxgS+MmccJqkrBaDhk8SKS1BW+71dJ9gzascwzW85UwGhxiSyR7Pxhu50k+Nl3+o5I49A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <title>اللياقة البدنية الملكية</title>
    <link rel="stylesheet" href="style.css">
</head>
<STYLE>
body {
    font-family: Arial, sans-serif;
    background: rgba(0, 0, 0, 0.5);  /* إضافة الخلفية الشفافة */
    color: white;  /* تغيير لون النص ليكون مناسب مع الخلفية الداكنة */
    margin: 0;
    padding: 0;
    direction: rtl;
    height: 100vh; /* تأكد من أن الخلفية تغطي كامل الشاشة */
    overflow: hidden; /* منع التمرير إذا لم يكن مطلوبًا */
}
    .wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 30px;
        padding: 40px 20px;
    }

    /* تنسيق الـ review-item */
    .review-item {
        width: 48%; /* تعديل العرض ليكون متساوي */
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        box-sizing: border-box; /* لضمان التساوي في الحجم مع احتساب الـ padding */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 20px; /* تباعد أسفل البوكس */
    }

    .review-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
    }

    /* نص داخل الـ review-item */
    .review-item h2 {
        color: #34495e;
        font-size: 18px;
        line-height: 1.6;
        margin-bottom: 20px;
        text-align: center;
    }

    .review-item p {
        font-size: 16px;
        color: #7f8c8d;
    }

    /* تنسيق استجابة للشاشات الصغيرة */
    @media (max-width: 768px) {
        .review-item {
            width: 100%; /* عرض البوكسات بالكامل في الشاشات الصغيرة */
            margin-bottom: 20px;
        }
        .wrapper {
            gap: 20px; /* تقليل المسافة بين البوكسات في الشاشات الصغيرة */
        }
    }

    /* footer */
    footer {
        background-color: #34495e;
        color: white;
        text-align: center;
        padding: 20px;
    }

    .social a {
        color: #ecf0f1;
        font-size: 24px;
        margin: 0 10px;
        text-decoration: none;
    }

    .social a:hover {
        color: #e74c3c;
    }

    .copyright {
        font-size: 14px;
        margin-top: 10px;
    }
</STYLE>
<body>
    <header>
        <a href="#home" class="logo"> طرق بناء <span> العضلات</span></a>
        <a href="#home" class="logo"> بناء العضلات تعتمد على شقين هو  <span>  ممارسة التمارين الرياضية و النظام الغذائي </span></a>
    </header>

    <section class="home" id="home">
        <div class="wrapper" data-aos="zoom-in-up">
            <div class="review-item">
                <h2>
                    التمارين الرياضية: تساعد تمارين القوّة على بناء العضلات، ويُنصح بممارسة تمارين القوة مرتين أو أكثر في الأسبوع؛ حيث يجب ممارسة كلّ تمرين بما يتراوح بين 2-3 جولات، وتكرار كلّ تمرين من 8-12 مرة في كلّ جولة، ويُنصح بعدم المُبالغة في رفع الأوزان حتى لا يتضرّر الجسم، ولذلك فإنّ من الضروريّ التدرّج في نوع وشدّة التمارين، ومن تمارين القوة:
                    تمارين الضغط (بالإنجليزية: Pushups)، وتمارين العقلة (بالإنجليزية: Pullups)، وتمارين القرفصاء (بالإنجليزية: Squats)، وتمارين الاندفاع (بالإنجليزية: Lungs).
                    تمارين المقاومة.
                    تمارين رفع الأثقال بأنواعها المختلفة.
                </h2>
            </div>

            <div class="review-item">
                <h2>
                    النظام الغذائي:  يجب تناول الأطعمة التي تحتوي على جميع العناصر الغذائيّة؛ ومن هذه العناصر الغذائية:
                    البروتين؛ حيث يُنصح بتناول الأطعمة الغنيّة بالبروتين قبل وبعد التمارين الرياضيّة، وزيادة استهلاك البروتين من 1.2-1.5 غرام لكلّ كيلوغرامٍ من وزن الجسم، ومن الأطعمة الغنيّة بالبروتين: البيض، واللحوم، والدجاج، والسلمون، والتونة، والبقوليات.
                    الكربوهيدرات: وذلك لملء مخازن الجلايكوجين التي يحتاجها الجسم عند ممارسة التمارين مثل: البطاطا الحلوة، والبقوليات، والخضراوات.
                    الدهون المفيدة: ومنها؛ الأفوكادو، واللبن، والمكسرات، وبذور الشيا، والزيتون، وزيت جوز الهند، والبذور، والحليب.
                    الفيتامينات والمعادن.
                </h2>
            </div>
        </div>
    </section>

    <!-- footer -->
    <footer class="footer">
        <div class="social">
            <a href="#"><i class="fa-brands fa-facebook"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
            <a href="#"><i class="fa-brands fa-twitter"></i></a>
        </div>
        <p class="copyright">
            &copy;اللياقة البدنية الملكية 2025- القائد/جميع الحقوق محفوظة
        </p>
    </footer>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            offset: 300,
            duration: 1400,
        });
    </script>
    <script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js"></script>
    <script src="script.js"></script>
</body>
</html>
