<?php

session_start();
include('functions.php');
// var_dump($_SESSION);
// exit();

$errmessage = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST情報がある時の処理
    $username = $_POST["username"];
    $password = $_POST["password"];
    $mail = $_POST["mail"];
    $pass2 = $_POST["pass2"];

    // バリデーション
    if (!$mail) {
        $errmessage[] = "Eメールを入力してください";
    } else if (strlen($mail) > 200) {
        $errmessage[] = "Eメールは200文字以内に指定してください";
    } else if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errmessage[] = "Eメールアドレスは不正です";
    }

    if (!$password) {
        $errmessage[] = "パスワードを入力してください";
    } else if (strlen($password) > 100) {
        $errmessage[] = "パスワードは100文字以内に指定してください";
    }

    if ($password !== $pass2) {
        $errmessage[] = "確認用パスワードが一致していません";
    }

    if (empty($errmessage)) {
        // エラーがない場合のみデータベース操作を行う
        $pdo = connect_to_db();

        $sql = 'SELECT COUNT(*) FROM users_table WHERE mail=:mail';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);

        try {
            $status = $stmt->execute();
        } catch (PDOException $e) {
            echo json_encode(["sql error" => "{$e->getMessage()}"]);
            exit();
        }

        if ($stmt->fetchColumn() > 0) {  // 1 ではなく 0 でユーザが存在するかを確認
            echo '<p>すでに登録されているユーザです．</p>';
            echo '<a href="./login.php">login</a>';
            exit();
        }

        $sql = 'INSERT INTO users_table(id, username, password, mail, is_admin, created_at, updated_at, deleted_at) VALUES(NULL, :username, :password, :mail, 0, now(), now(), NULL)';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);

        try {
            $status = $stmt->execute();
            // 登録成功メッセージを表示する場合
            echo '<p>登録が成功しました。</p>';
            echo '<a href="./login.php">ログイン</a>';
            exit();
        } catch (PDOException $e) {
            echo json_encode(["sql error" => "{$e->getMessage()}"]);
            exit();
        }
    }
} else {
    $username = '';
    $mail = '';
    $password = '';
    $pass2 = '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f8f9fa;
            /* 少し明るい背景色 */
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

        .haikei {
            background-image: url("./Img/choice.jpg");
            width: 100%;
            height: 100vh;
            background-size: cover;
            /* opacity: 0.7; */

        }
    </style>

</head>

<body>
    <div class="haikei">
        <div class="login-container">

            <?php
            if ($errmessage) {
                echo '<div class="alert alert-danger" role="alert">';
                echo implode('<br>', $errmessage);
                echo '</div>';
            }
            ?>

            <form action="./register.php" method="POST">
                <!-- 処理を行う宛先を指定 -->
                <P style="font-size:20px ">新規会員登録</P>
                <div>
                    <label>
                        名前：
                        <input type="text" name="username" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>" class=" form-control" style="width:400px"><br>
                    </label>
                </div>
                <div>
                    <label>
                        メールアドレス：
                        <input type="text" name="mail" value="<?php echo htmlspecialchars($mail, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" style="width:400px"><br>
                    </label>
                </div>
                <div>
                    <label>
                        パスワード：
                        <input type="password" name="password" class="form-control" style="width:400px"><br>
                    </label>
                </div>
                <div>
                    <label>
                        パスワード(確認)：
                        <input type="password" name="pass2" class="form-control" style="width:400px"><br>
                    </label>
                </div>
                <div class="button">
                    <input type="submit" value="登録" class="btn btn-primary btn-lg"><br><br>
                </div>

            </form>

            <p>すでに登録済みの方は<a href="./login.php">こちら</a></p>

        </div>
    </div>
</body>

</html>