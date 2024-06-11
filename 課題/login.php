<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;

        }

        .haikei {
            background-image: url("./Img/umi.jpg");
            width: 100%;
            height: 100vh;
            background-size: cover;
            /* opacity: 0.7; */

        }

        .login-container {
            margin-left: 40%;
            margin-top: 10%;
            position: relative;
            width: 400px;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            /* 影をつけて浮かせる */
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            /* ホバー時のアニメーション */
            z-index: 5;
        }

        /* 
         .login-container:hover {
            transform: translateY(-10px);
          
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.3);
        }  */

        .login-form {
            width: 100%;
        }

        .login-form p {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .login-form .form-control {
            margin-bottom: 15px;
        }

        .button {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="haikei">
        <div class="login-container">
            <form action="./login_act.php" method="POST" class="login-form">
                <p>ログイン</p>
                <div>
                    メールアドレス：
                    <input type="text" name="mail" class="form-control" required>
                </div>
                <div>
                    パスワード：
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="button">
                    <input type="submit" value="ログイン" class="btn btn-primary btn-lg">
                </div>
            </form>
            <br>
            <p>新規会員登録の方は<a href="./register.php">こちら</a></p>
            <p>パスワードをお忘れの方は<a href="./forget.php">こちら</a></p>
        </div>
    </div>
</body>

</html>