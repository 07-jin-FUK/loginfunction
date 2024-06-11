<?php
session_start();
include('functions.php');

$errmessage = array();

// ログイン状態の確認
if (!isset($_SESSION['username']) || !isset($_SESSION['mail'])) {
    // ログインしていない場合、ログインページにリダイレクト
    header('Location: login.php');
    exit();
}

// ログインしている場合の処理
$username = $_SESSION['username'];
$mail = $_SESSION['mail'];

// DB接続
$pdo = connect_to_db();

// SQL実行
$sql = 'SELECT * FROM users_table WHERE username=:username AND mail=:mail';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    // ユーザーが見つからない場合の処理
    echo "ユーザーが見つかりませんでした。";
    exit();
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール更新</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f8f9fa;
        }

        .login-container {
            margin-left: 35%;
            margin-top: 5%;
            position: relative;
            width: 600px;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

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
        }
    </style>
</head>

<body>
    <div class="haikei">
        <div class="login-container">

            <?php if (!empty($errmessage)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= implode('<br>', $errmessage); ?>
                </div>
            <?php endif; ?>

            <form action="./profileup.php" method="POST" enctype="multipart/form-data">
                <p style="font-size:20px">プロフィール更新</p>
                <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
                <div>
                    <label>
                        名前：
                        <input type="text" name="username" value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" style="width:400px" readonly><br>
                    </label>
                </div>
                <div>
                    <label>
                        メールアドレス：
                        <input type="text" name="mail" value="<?= htmlspecialchars($mail, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" style="width:400px" readonly><br>
                    </label>
                </div>
                <div>
                    <label>
                        誕生日：
                        <input type="date" name="birthday" value="<?= htmlspecialchars($record['birthday'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" style="width:400px"><br>
                    </label>
                </div>
                <div>
                    <label>
                        プロフィール写真：
                        <input type="file" name="picture" class="form-control" style="width:400px"><br>
                    </label>
                    <?php if (!empty($record['picture'])) : ?>
                        <img src="<?= htmlspecialchars($record['picture'], ENT_QUOTES, 'UTF-8'); ?>" alt="プロフィール画像" width="150">
                    <?php endif; ?>
                </div>
                <div>
                    <label>
                        自己紹介文：
                        <textarea name="bio" class="form-control" style="width:400px"><?= htmlspecialchars($record['bio'], ENT_QUOTES, 'UTF-8'); ?></textarea><br>
                    </label>
                </div>

                <div class="button">
                    <input type="submit" value="更新" class="btn btn-primary btn-lg"><br><br>
                </div>
            </form>

            <a href="./todo_input.php">戻る</a>

        </div>
    </div>
</body>

</html>