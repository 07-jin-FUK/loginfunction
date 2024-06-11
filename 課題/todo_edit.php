<?php
session_start();
include('functions.php');



// id受け取り
$id = $_GET['id'];

// DB接続
$pdo = connect_to_db();


// SQL実行
$sql = 'SELECT * FROM todo_table WHERE id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

$record = $stmt->fetch(PDO::FETCH_ASSOC);



?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編集画面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        body {
            display: flex;
            /* justify-content: center;
            align-items: center; */
            height: 100vh;
            margin: 0;

        }

        .haikei {
            background-image: url("./Img/omoide.jpg");
            width: 100%;
            height: 100vh;
            background-size: cover;
            /* opacity: 0.7; */

        }

        .login-container {
            margin-left: 35%;
            margin-top: 1%;
            position: relative;
            width: 600px;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            /* 影をつけて浮かせる */
            /* text-align: center; */
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

        .anke {
            margin-bottom: 10px;
            margin-right: 10px;
            margin-left: 10px;
            /* justify-content: center;
            text-align: center; */
        }

        .douga {
            margin-bottom: 10px;
            display: flex;
            justify-content: center;
            text-align: center;
            flex-direction: column;
        }

        .yoko {
            display: flex;
            /* justify-content: center; */
            /* margin-right: 10px;
            margin-left: 10px; */
        }

        .dou {
            margin-left: 20px;
        }

        .doko {
            margin-left: 90px;
        }
    </style>
</head>

<body>
    <div class="haikei">
        <div class="login-container">
            <form action="todo_update.php" method="POST" enctype="multipart/form-data">
                <div class="container" id="tyuuou">
                    <fieldset>
                        <p>編集画面</p>
                        <div>
                            <input type="hidden" name="id" value="<?= $record['id'] ?>">
                        </div>
                        <div class="yoko">
                            <div class="anke">
                                <label for="nameInput" class="form-label">お名前</label>
                                <input type="text" class="form-control" name="name" id="nameInput" value="<?= $record['name'] ?>" placeholder="お名前は？">
                            </div>
                            <div class="dou">
                                <label for="" class="form-label">同行者</label>
                                <input type="text" class="form-control" name="support" value="<?= $record['support'] ?>" placeholder="どなたと？">
                            </div>
                        </div>
                        <div class="yoko">
                            <div class="anke">
                                <label for="" class="form-label">日時</label><br>
                                <input type="date" name="itu" value="<?= $record['itu'] ?>">
                            </div>
                            <div class="doko">
                                都道府県<select name="place" id="weatherInput" class="form-select">
                                    <option value="">どこにいきましたか？</option>
                                    <?php
                                    $places = ["北海道", "東京都", "佐賀県", "福岡県", "大分県", "沖縄県", "長崎県", "大阪府", "埼玉県", "熊本県"];
                                    foreach ($places as $place) {
                                        echo "<option value=\"$place\"" . ($record['place'] == $place ? ' selected' : '') . ">$place</option>";
                                    }
                                    ?>
                                </select>

                            </div>
                        </div>
                        <div class="anke">
                            (任意詳細) <input type="text" class="form-control" name="nojapan" placeholder="地名？海外？" value="<?= $record['nojapan'] ?>">
                        </div>
                        <div class="douga">
                            <label>現在の動画:</label>
                            <?php if (!empty($record['media']) && $record['media'] !== 'null') : ?>
                                <video controls width="300" style="margin-left:20%">
                                    <source src="uploads/<?= $record['media'] ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php else : ?>
                                <p>現在の動画はありません。</p>
                            <?php endif; ?>
                        </div>
                        <div class="anke">
                            <label>新しい動画をアップロード:</label>
                            <input type="file" name="media" class="form-control">

                            ※ 新しい動画をアップロードする場合はファイルを選択してください。<br>選択しない場合は現在の動画が保持されます。
                        </div>
                        <div class="anke">
                            <label>
                                一言（コメント） </label>
                            <textarea name="doko" class="form-control" rows="2"><?= htmlspecialchars($record['doko'], ENT_QUOTES, 'UTF-8') ?></textarea>

                        </div>


                        <div class="anke">
                            <button class="btn btn-primary btn-lg">思い出を書き換える！</button>
                        </div>


                    </fieldset>
                    <a href="todo_read.php">一覧画面へ</a>

            </form>
        </div>
    </div>
</body>

</html>