<?php
session_start();
include('functions.php');
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

// // セッションの有効性を確認
// if (!isset($_SESSION['id'])) {
//   echo "ログインしていません。";
//   exit();
// }


$pdo = connect_to_db();

if (!isset($_SESSION['mail'])) {
  $host = $_SERVER['HTTP_HOST'];
  $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  header("Location: //$host$uri/login.php");
  exit();
}
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'ゲスト';


?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>旅記録</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <style>
    .anke {
      align-items: center;
      margin-bottom: 10px;
    }

    @import url(//fonts.googleapis.com/css?family=Lato:300:400);

    body {
      margin: 0;
    }

    h1 {
      font-family: 'Lato', sans-serif;
      font-weight: 300;
      letter-spacing: 2px;
      font-size: 48px;
    }

    p {
      font-family: 'Lato', sans-serif;
      letter-spacing: 1px;
      font-size: 14px;
      color: #333333;
    }

    .header {
      position: relative;
      text-align: center;
      background: linear-gradient(60deg, rgba(84, 58, 183, 1) 0%, rgba(0, 172, 193, 1) 100%);
      color: white;
    }

    .logo {
      width: 50px;
      fill: white;
      padding-right: 15px;
      display: inline-block;
      vertical-align: middle;
    }

    .inner-header {
      height: 12vh;
      width: 100%;
      margin: 0;
      padding: 0;
    }

    .flex {
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      margin-bottom: 10px;
    }

    .waves {
      position: relative;
      width: 100%;
      height: 15vh;
      margin-bottom: -7px;
      min-height: 100px;
      max-height: 150px;
    }

    .content {
      position: relative;
      height: 20vh;
      text-align: center;
      background-color: white;
    }

    .parallax>use {
      animation: move-forever 25s cubic-bezier(.55, .5, .45, .5) infinite;
    }

    .parallax>use:nth-child(1) {
      animation-delay: -2s;
      animation-duration: 7s;
    }

    .parallax>use:nth-child(2) {
      animation-delay: -3s;
      animation-duration: 10s;
    }

    .parallax>use:nth-child(3) {
      animation-delay: -4s;
      animation-duration: 13s;
    }

    .parallax>use:nth-child(4) {
      animation-delay: -5s;
      animation-duration: 20s;
    }

    @keyframes move-forever {
      0% {
        transform: translate3d(-90px, 0, 0);
      }

      100% {
        transform: translate3d(85px, 0, 0);
      }
    }

    @media (max-width: 768px) {
      .waves {
        height: 40px;
        min-height: 40px;
      }

      .content {
        height: 30vh;
      }

      h1 {
        font-size: 24px;
      }
    }

    .btn,
    a.btn,
    button.btn {
      margin-left: 10px;
      font-size: 15px;
      font-weight: 700;
      line-height: 0.2;
      position: relative;
      display: inline-block;
      padding: 1rem 1rem;
      cursor: pointer;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      -webkit-transition: all 0.3s;
      transition: all 0.3s;
      text-align: center;
      vertical-align: middle;
      text-decoration: none;
      letter-spacing: 0.1em;
      color: black;
      border-radius: 0.5rem;
      background-color: white;
    }

    .btn:hover {
      scale: 1.1;
      background-color: silver;
      color: blue;
    }

    label {
      font-size: 20px;
    }

    a.btn-flat {
      overflow: hidden;
      padding: 1.3rem 2rem;
      color: #fff;
      border-radius: 0;
      background: #000;
    }

    a.btn-flat span {
      position: relative;
    }

    a.btn-flat:before {
      position: absolute;
      top: 0;
      left: 0;
      width: 150%;
      height: 500%;
      content: "";
      -webkit-transition: all 0.5s ease-in-out;
      transition: all 0.5s ease-in-out;
      -webkit-transform: translateX(-98%) translateY(-25%) rotate(45deg);
      transform: translateX(-98%) translateY(-25%) rotate(45deg);
      background: #00b7ee;
    }

    a.btn-flat:hover:before {
      -webkit-transform: translateX(-9%) translateY(-25%) rotate(45deg);
      transform: translateX(-9%) translateY(-25%) rotate(45deg);
    }


    #z {
      z-index: 100;
    }

    .welcome-message {
      font-size: 18px;
      font-weight: 500;
      color: black;
      margin-right: 20px;
      display: flex;
      align-items: center;
    }

    .nav-item {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    /* Popup notification styles */
    .popup-notification {
      display: none;
      position: fixed;
      top: 3%;
      left: 50%;
      transform: translate(-50%, -50%);
      padding: 10px 20px;
      background-color: blue;
      color: white;
      font-size: 20px;
      border-radius: 5px;
      z-index: 1000;
    }
  </style>
</head>

<body>
  <audio id="audio" src="./Img/Someday.mp3" preload="auto" loop></audio>
  <div class="header">
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container">

        <div class="welcome-message">
          ようこそ, <span style="font-size:23px;color:blue;">&nbsp;<?php echo htmlspecialchars($username); ?>&nbsp;</span> さん
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="./todo_input.php">Home(記録)</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="./todo_read.php">投稿履歴</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">オプション</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="./profile.php"> プロフィールを更新する</a></li>
                <li><a class="dropdown-item" href="./changepass.php">パスワード変更</a></li>
                <li><a class="dropdown-item" href="./forget.php">パスワードを忘れた</a></li>
                <li><a class="dropdown-item" href="./register.php">新規ユーザー登録</a></li>
                <li><a class="dropdown-item" href="./logout.php">ログアウト</a></li>
              </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="./logout.php">ログアウト</a>
            </li>

            <?php
            if (isset($_SESSION['update_success'])) {
              echo '<div class="alert alert-success" role="alert">';
              echo $_SESSION['update_success'];
              echo '</div>';
              unset($_SESSION['update_success']);
            }

            if (isset($_SESSION['update_error'])) {
              echo '<div class="alert alert-danger" role="alert">';
              echo $_SESSION['update_error'];
              echo '</div>';
              unset($_SESSION['update_error']);
            }
            ?>
          </ul>
          <button class=" btn btn-flat" style="font-size:20px;background-color:blue;"><a style="color:white" href="./todo_record.php">録画する！</a></button>
        </div>
      </div>
    </nav>
    <div class="inner-header flex">
      <svg version="1.1" class="logo" baseProfile="tiny" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 500 500" xml:space="preserve">
        <path fill="#FFFFFF" d="M250,2.3c-68.4,0-135,26.6-186.3,78c-103,103-103,270.6,0,373.7c50.6,50.6,116.9,78,186.3,78
        s135-26.6,186.3-78c103-103,103-270.6,0-373.7C385,28.9,318.4,2.3,250,2.3L250,2.3z M250,472.3c-60.5,0-117.4-23.6-160.2-66.4
        c-88.4-88.4-88.4-232,0-320.4c42.8-42.8,99.7-66.4,160.2-66.4s117.4,23.6,160.2,66.4c88.4,88.4,88.4,232,0,320.4
        C367.4,448.7,310.5,472.3,250,472.3L250,472.3z" />
        <path fill="#FFFFFF" d="M317.5,151.1c-1.4-1.3-2.9-2.4-4.5-3.3c-9.1-5.8-21-5.3-28.8,2.6l-86.6,86.6c-1.8,1.8-4.1,2.9-6.6,3.2
        l-39.8,4.5c-1.1,0.1-2.2,0.4-3.2,0.8c-6.1,2.5-9.2,9.5-7.2,15.8c1,3.2,3.1,5.8,5.9,7.4c2.1,1.2,4.5,1.8,6.9,1.8
        c0.6,0,1.2,0,1.8-0.1l39.8-4.5c5.1-0.6,9.9-2.9,13.6-6.6l86.6-86.6C325.4,173.3,325.9,160.4,317.5,151.1z" />
      </svg>
      <h1>旅記録</h1>
    </div>
    <div id="popup" class="popup-notification">投稿が完了しました！</div>
    <div>
      <audio id="audio" src="./Img/Someday.mp3" preload="auto" loop></audio>

      <div class="flex">
        <form action="todo_create.php" method="POST" id="uploadForm" enctype="multipart/form-data">
          <div class="container" id="tyuuou">

            <fieldset>
              <div id="popup" class="popup-notification">投稿が完了しました！</div>
              <legend>今日も1日お疲れさまでした!<br>楽しめましたか？？</legend>

              <div class="anke">
                <input type="text" class="form-control" name="name" id="nameInput" placeholder="お名前は？">
              </div>
              <div class="anke">
                <input type="text" class="form-control" name="support" placeholder="どなたと？">
              </div>
              <div class="anke">
                <input type="date" name="itu">
              </div>
              <div class="anke">
                <select name="place" id="weatherInput" class="form-select">
                  <option value="">どこにいきましたか？(海外は無記入）</option>
                  <option value="北海道">北海道</option>
                  <option value="東京都">東京都</option>
                  <option value="佐賀県">佐賀県</option>
                  <option value="福岡県">福岡県</option>
                  <option value="大分県">大分県</option>
                  <option value="沖縄県">沖縄県</option>
                  <option value="長崎県">長崎県</option>
                  <option value="大阪府">大阪府</option>
                  <option value="埼玉県">埼玉県</option>
                  <option value="埼玉県">熊本県</option>
                </select>
                <!-- <input type="text" name="place" placeholder="都道府県は？"> -->

              </div>


              <div class="anke">
                (任意) <input type="text" class="form-control" name="nojapan" placeholder="地名？海外？">
              </div>
              <div class="anke">
                <textarea name="doko" class="form-control" placeholder="一言（コメント）"></textarea>

              </div>
              <div class="anke">

                <label>動画アップロード</label>
                <input type="file" name="media" class="form-control">

              </div>
              <div>
                <button class=" btn btn-flat" id="z" onclick="handleSubmit()">思い出を追加！</button>
              </div>


            </fieldset>
          </div>
        </form>

      </div>
    </div>
    <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
      <defs>
        <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s58 18 88 18 58-18 88-18 58 18 88 18v44h-352z" />
      </defs>
      <g class="parallax">
        <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(255,255,255,0.7" />
        <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.5)" />
        <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.3)" />
        <use xlink:href="#gentle-wave" x="48" y="7" fill="#fff" />
      </g>
    </svg>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      const music = document.getElementById('audio');
      // 音量を小さく設定（0.0から1.0の範囲で設定）
      music.volume = 0.2;

      // ページロード時に音楽を再生
      music.play();

      // 名前の入力フィールドがクリックされたときに音楽を再生
      nameInput.addEventListener('click', () => {
        music.play();
      });

      // Check for success parameter in URL
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('success') === 'true') {
        showPopup();
      }
    });

    function showPopup() {
      const popup = document.getElementById('popup');
      popup.style.display = 'block';
      setTimeout(() => {
        popup.style.display = 'none';
      }, 4000);
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous"></script>
</body>

</html>