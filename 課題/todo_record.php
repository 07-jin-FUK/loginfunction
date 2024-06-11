<?php
session_start();
include('functions.php');
$pdo = connect_to_db();

if (!isset($_SESSION['mail'])) {
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: //$host$uri/login.php");
    exit();
}
$username = isset($_SESSION['name']) ? $_SESSION['name'] : 'ゲスト';

?>


<!DOCTYPE html>
<html lang="en">

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
            height: 20vh;
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
            color: white;
            border-radius: 0.5rem;
            background-color: blue;
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
            color: white;
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

        .nakami {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container">
            <a class="navbar-brand" href="#">Welcome</a>
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
                            <li><a class="dropdown-item" href="./changepass.php">パスワード変更</a></li>
                            <li><a class="dropdown-item" href="./forget.php">パスワードを忘れた</a></li>
                            <li><a class="dropdown-item" href="./register.php">新規ユーザー登録</a></li>
                            <li><a class="dropdown-item" href="./logout.php">ログアウト</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./logout.php">ログアウト</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <!-- 録画中の映像を表示する要素 スタイルをブロック要素にしています。使用するときは cssで調整してください -->
        <div class="nakami">
            <video id="recordVideo" autoplay muted width="800" height="600" style="display:block;"></video>
            <!-- リプレイを表示する要素 -->
            <video id="replayVideo" autoplay controls width="800" height="600" style="display:none;"></video>
            <!-- 録画を動作するボタン -->
            <div class="">
                <button class="btn btn-flat" id="start">録画開始</button>
                <button class="btn btn-flat" id="stop" disabled>録画停止</button>
                <button class="btn btn-flat" id="save" disabled>動画保存</button>
            </div>
        </div>
    </div>
    <script>
        const recordVideo = document.getElementById('recordVideo');
        const replayVideo = document.getElementById('replayVideo');
        const startButton = document.getElementById('start');
        const stopButton = document.getElementById('stop');
        const saveButton = document.getElementById('save');
        let mediaRecorder;
        // 録画されたデータを格納する配列
        const recordedBlobs = [];

        // 録画を開始
        async function startRecording() {
            // streamを取得
            const stream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: true
            }).catch(err => console.error('カメラ取得エラー', err));
            // video要素にstreamを入れ込む
            recordVideo.srcObject = stream;
            // mimeTypeを設定
            const options = {
                mimeType: 'video/webm;codecs=vp9,opus'
            };
            mediaRecorder = new MediaRecorder(stream, options);
            mediaRecorder.ondataavailable = (event) => {
                if (event.data && event.data.size > 0) {
                    // 録画データが格納される
                    recordedBlobs.push(event.data);
                }
            };

            mediaRecorder.start();
            console.log('録画開始');
            startButton.disabled = true;
            stopButton.disabled = false;
        }

        // リプレイを再生
        function replay() {
            const blob = new Blob(recordedBlobs, {
                type: 'video/webm'
            });
            replayVideo.src = URL.createObjectURL(blob);
            replayVideo.style.display = 'block';
            replayVideo.controls = true;
            recordVideo.style.display = 'none';
            saveButton.disabled = false;
        }

        // 動画を保存
        function saveRecording() {
            const blob = new Blob(recordedBlobs, {
                type: 'video/webm'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = '録画ファイル.webm';
            document.body.appendChild(a);
            a.click();
            setTimeout(() => {
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 100);
        }

        // 録画を開始
        function stopRecording() {
            mediaRecorder.stop();
            console.log('録画停止');
            mediaRecorder.onstop = () => {
                replay();
                startButton.disabled = false;
                stopButton.disabled = true;
                recordVideo.srcObject.getTracks().forEach(track => track.stop());
            };
        }

        startButton.addEventListener('click', () => {
            startRecording();
            startButton.disabled = true;
            stopButton.disabled = false;
        });

        stopButton.addEventListener('click', stopRecording);
        saveButton.addEventListener('click', saveRecording);
    </script>
</body>

</html>