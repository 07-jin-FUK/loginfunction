<?php

// DB接続
include('functions.php');
$pdo = connect_to_db();

$filter_name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
  $filter_name = $_POST['name'];
}

// 名前でソートされた場合のSQLクエリ
$sql = 'SELECT *, media AS video_path FROM todo_table';
if ($filter_name !== '') {
  $sql .= ' WHERE name LIKE :name'; // 名前でフィルタリング
}

$sql .= ' ORDER BY id DESC'; // IDを降順でソート（投稿された順）

$stmt = $pdo->prepare($sql);

if ($filter_name !== '') {
  $stmt->bindValue(':name', '%' . $filter_name . '%', PDO::PARAM_STR);
}

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$output = "";
foreach ($result as $record) {
  $output .= "
<tr>
  <td style='border: 1px double black;'><span style='color: green; font-size:20px;'>{$record["name"]}</span>は、
    <span style='color: gray; font-size:20px; '>{$record["support"]}</span>と、
    <span style='color: black; font-size:20px;'>{$record["itu"]}</span>に
    <span style='color: red; font-size:20px;'>{$record["place"]}{$record["nojapan"]}</span>に行ったよ！<br>
    その日の一言は <span style='color: blue; font-size:20px;'>「{$record["doko"]}」</span>だったよ！
  </td>
  <td style='border: 1px solid black;'>
    <video width='240' height='180' controls>
      <source src='./uploads/{$record["video_path"]}' type='video/mp4'>
    </video>
  </td>
  <td style='border: 1px solid black;'><a href='todo_edit.php?id={$record["id"]}'>編集する</a></td>
  <td style='border: 1px solid black;'><a href='todo_delete.php?id={$record["id"]}'>削除する</a></td>
</tr>
";
}

// 新しいデータを取得する
$sql_count = 'SELECT place, COUNT(*) as count FROM todo_table WHERE place IS NOT NULL AND place <> "" AND deleted = 0 GROUP BY place ORDER BY count DESC'; // 回数でソート
$stmt_count = $pdo->prepare($sql_count);

try {
  $status_count = $stmt_count->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$count_result = $stmt_count->fetchAll(PDO::FETCH_ASSOC);
$places = [];
$counts = [];
foreach ($count_result as $row) {
  $places[] = $row['place'];
  $counts[] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>記録の森</title>

  <style>
    body {
      background-image: url(./Img/ressha.jpg);
      background-size: cover;
      background-attachment: fixed;
      opacity: 0.95;
      /* スクロールしても背景画像が動かない */

    }

    .main {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }

    /* コンテンツ全体に白い背景を追加 */
    .content-wrapper {
      background-color: white;
      /* 背景を白に設定 */
      padding: 20px;
      margin: 20px;
      border-radius: 10px;
      /* 角を丸くする */
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      /* 軽い影をつけて浮き上がらせる */
      max-width: 1200px;
      /* コンテンツの最大幅を制限 */
      width: 100%;

    }

    table {
      border-collapse: collapse;
      border-spacing: 0;
      width: 100%;
    }

    tr {
      border-bottom: 1px solid black;
    }

    td,
    th {
      padding: 10px;
      border: 1px solid black;
    }

    .chart-container {
      width: 45%;
      justify-content: center;
      margin: auto;
    }

    .chart-container1 {
      width: 40%;
      margin: auto;
    }

    .hidden {
      display: none;
    }

    .yoko {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .sort {
      display: flex;
      justify-content: center;
    }

    .top {
      margin-left: 30px;
    }
  </style>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

  <audio id="audio" src="./Img/あの日見た景色.mp3" preload="auto"></audio>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container">
      <a class="navbar-brand" href="#">Welcome</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="./todo_input.php">Home(記録)</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="./todo_read.php">投稿履歴</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              オプション
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="./changepass.php">パスワード変更</a></li>
              <li><a class="dropdown-item" href="./forget.php">パスワードを忘れた
                </a></li>
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
  <div class="main">
    <div class="content-wrapper">
      <fieldset>
        <div class="container">
          <div class="sort">
            <h3>思い出一覧</h3>
            <a class="top" href="todo_input.php">追加で記入する</a><br>

            <form class="top" method="POST" action="">
              <label for="name">名前で検索:</label>
              <input type="text" id="name" name="name" value="<?= htmlspecialchars($filter_name, ENT_QUOTES, 'UTF-8') ?>">
              <input type="submit" value="検索">
            </form><br>

          </div>
          <div class="yoko">
            <table class="yoko">
              <tbody id="memories">
                <?= $output ?>
              </tbody>
            </table>
          </div>
          <button id="showMoreButton" class="btn btn-primary btn-lg">もっと見る</button>
          <button id="showLessButton" class="btn btn-secondary btn-lg hidden">閉じる</button>
        </div>
      </fieldset>
    </div>
    <div class="content-wrapper">
      <div class="container" style='margin-left: 40%;'>
        <p style='font-size:20px;'>みんながよくいってる地域！！</p>
      </div>
      <div class="yoko">
        <!-- <div class="chart-container">
      <canvas id="placeChart"></canvas>
    </div> -->
        <div class="chart-container1">
          <canvas id="placePieChart"></canvas>
        </div>
      </div>
      <div class="container" id="concentrationMessageContainer">
        <h3 class="yoko" id="concentrationMessage"></h3>
      </div>
    </div>
  </div>
  <div class="map-container">
    <svg id="japan-map" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 520 620">
      <path id="JP-01" d="M..." fill="#ccc" /> <!-- 北海道 -->
      <path id="JP-02" d="M..." fill="#ccc" /> <!-- 青森県 -->
      <!-- 他の都道府県... -->
    </svg>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // const ctx = document.getElementById('placeChart').getContext('2d');
      const counts = <?= json_encode($counts) ?>;
      const maxCount = Math.max(...counts);
      const backgroundColors = counts.map(count => count === maxCount ? 'rgba(255, 99, 132, 0.2)' : 'rgba(75, 192, 192, 0.2)');
      const borderColors = counts.map(count => count === maxCount ? 'rgba(255, 99, 132, 1)' : 'rgba(75, 192, 192, 1)');

      // new Chart(ctx, {
      //   type: 'bar',
      //   data: {
      // labels: 
      //     datasets: [{
      //       label: '訪問回数',
      //       data: counts,
      //       backgroundColor: backgroundColors,
      //       borderColor: borderColors,
      //       borderWidth: 1
      //     }]
      //   },
      //   options: {
      //     scales: {
      //       y: {
      //         beginAtZero: true,
      //         ticks: {
      //           stepSize: 1 // 縦軸のカウントを1ずつに設定
      //         }
      //       }
      //     }
      //   }
      // });

      const pieCtx = document.getElementById('placePieChart').getContext('2d');
      new Chart(pieCtx, {
        type: 'pie',
        data: {
          labels: <?= json_encode($places) ?>,
          datasets: [{
            label: '訪問回数',
            data: counts,
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)',
              'rgba(54, 162, 235, 0.2)',
              'rgba(255, 206, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(153, 102, 255, 0.2)',
              'rgba(255, 159, 64, 0.2)',
              'rgba(132, 80, 30, 0.2)',
              'rgba(100, 19, 64, 0.2)',
              'rgba(255, 159, 64, 0.2)',

            ],
            borderColor: [
              'rgba(255, 99, 132, 1)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(153, 102, 255, 1)',
              'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
          }]
        }
      });
    });

    document.addEventListener('DOMContentLoaded', () => {
      const showMoreButton = document.getElementById('showMoreButton');
      const showLessButton = document.getElementById('showLessButton');
      const memoriesTable = document.getElementById('memories');
      const memories = memoriesTable.querySelectorAll('tr');
      const initialCount = 3;
      const totalCount = memories.length;
      let visibleCount = initialCount;

      const updateTableVisibility = () => {
        memories.forEach((memory, index) => {
          memory.style.display = index < visibleCount ? '' : 'none';
        });

        showMoreButton.style.display = visibleCount < totalCount ? '' : 'none';
        showLessButton.style.display = visibleCount > initialCount ? '' : 'none';
      };

      showMoreButton.addEventListener('click', () => {
        visibleCount = Math.min(visibleCount + 5, totalCount);
        updateTableVisibility();
      });

      showLessButton.addEventListener('click', () => {
        visibleCount = initialCount;
        updateTableVisibility();
      });

      updateTableVisibility();
    });

    const counts = <?= json_encode($counts) ?>;
    const maxCount = Math.max(...counts);

    if (maxCount >= 2) {
      const places = <?= json_encode($places) ?>;
      const maxIndex = counts.indexOf(maxCount);
      const maxIndex1 = counts.indexOf(maxCount) + 1;
      const maxPlace = places[maxIndex];
      const maxPlace1 = places[maxIndex1];
      // console.log(maxIndex);
      // console.log(maxPlace);
      // console.log(maxIndex1);

      const concentrationMessage = `現在、みんながよく旅しているのは<span style="color: red;font-size: 40px">${maxPlace}、${maxPlace1}</span>です！`;
      document.getElementById('concentrationMessage').innerHTML = concentrationMessage;
      document.getElementById('concentrationMessageContainer').style.display = 'block';
    }
    document.addEventListener('DOMContentLoaded', (event) => {
      const music = document.getElementById('audio');
      // 音量を小さく設定（0.0から1.0の範囲で設定）
      music.volume = 0.2;

      // ページロード時に音楽を再生
      music.play();
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous"></script>
</body>

</html>