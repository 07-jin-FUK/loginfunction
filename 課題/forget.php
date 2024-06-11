<?php
// print_r($_SERVER);
// exit();
$errmessage = array();
$complete = false;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST情報がある時の処理
    $mail = $_POST['mail'] ?? '';
    $pass = $_POST['pass'] ?? '';


    if (!$mail) {
        $errmessage[] = "Eメールを入力してください";
    } else if (strlen($mail) > 200) {
        $errmessage[] = "Eメールは200文字以内に指定してください";
    } else if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errmessage[] = "Eメールアドレスは不正です";
    }



    $userfile = './userinfo.txt';
    if (empty($errmessage) && file_exists($userfile)) {
        $users = file_get_contents($userfile);
        $users = explode("\n", $users);
        foreach ($users as $k => $v) {
            $v_ary = str_getcsv($v);
            if ($v_ary[0] == $mail) {
                $repass = bin2hex(random_bytes(5));
                $message = "パスワードを変更しました。\r\n"
                    . $repass . "\r\n";
                if (mail($mail, 'パスワード変更しました。', $message)) {
                    $ph = password_hash($repass, PASSWORD_DEFAULT);
                    $line = '"' . $mail . '","' . $ph . '"';

                    $users[$k] = $line;
                    $userinfo = implode("\n", $users);
                    $ret = file_put_contents($userfile, $userinfo);
                    $complete = true;
                } else {
                    $errmessage[] = "メールの送信に失敗しました。";
                }
                break;
            }
        }
        if (!$complete) {
            $errmessage[] = "メールアドレスが正しくありません。";
        }
    } else {
        $errmessage[] = "ユーザーリストが見つかりません";
    }


    // 入力チェック時

    // IDパスワードの確認

} else {
    if (isset($_SESSION['mail']) && $_SESSION['mail']) {
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        //上でcharlistは省略
        header("Location: //$host$uri/todo_input.php");
        exit();
    }
    $_POST = array();
    $mail = "";
    $pass = "";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワード再発行
    </title>
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
            background-image: url("./Img/tabi.jpg");
            width: 100%;
            height: 100vh;
            background-size: cover;
            /* opacity: 0.7; */

        }

        .login-container {
            margin-left: 38%;
            margin-top: 10%;
            position: relative;
            width: 500px;
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
            <div class="mx-auto" style="width:400px">
                <?php
                if ($errmessage) {
                    echo '<div class="alert alert-danger" role="alert">';
                    echo implode('<br>', $errmessage);
                    echo '</div>';
                }
                ?>
                <?php
                if ($complete) {
                ?>
                    パスワード再発行しました。
                <?php } else { ?>

                    <form action="./forget.php" method="POST">
                        <P>※パスワードを再発行します。※<br>仮パスワードをお送りしますのでメールアドレスを記載ください。
                        </P>
                        <div>

                            <label>
                                メールアドレス：
                                <input type="text" name="mail" class="form-control" value="<?php echo htmlspecialchars($mail, ENT_QUOTES, 'UTF-8'); ?>" style="width:400px" required><br>
                            </label>
                        </div>


                        <div class="button">
                            <input type="submit" value="再発行" class="btn btn-primary btn-lg"><br><br>
                        </div>

                    </form>


                    <p>新規会員登録の方は<a href="./register.php">こちら</a></p>
                    <p>ログインは<a href="./login.php">こちら</a></p>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>