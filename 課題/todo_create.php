<?php
include('functions.php');
// POSTデータ確認
// var_dump($_FILES);
// exit();
if (
    !isset($_POST['name']) || $_POST['name'] === '' ||
    !isset($_POST['support']) || $_POST['support'] === '' ||
    !isset($_POST['itu']) || $_POST['itu'] === ''
    // 入力しなくていい画面もあるので入力確認は名前と時間のみ

) {
    exit('お名前とどなたといつ行ったのかの入力は必須です（場所は任意)');
};


$name = $_POST['name'];
$support = $_POST['support'];
$where = $_POST['doko'];
$place = $_POST['place'];
$nojapan = $_POST['nojapan'];
$when = $_POST['itu'];

// 動画ファイルをアップロード
$media = 'null';
if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
    $uploadDir = 'uploads/';

    // オリジナルのファイル名を取得し、安全なファイル名に変換
    $originalFileName = basename($_FILES['media']['name']);
    $safeFileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $originalFileName);

    // ユニークなファイル名を生成
    $uniqueFileName = time() . '_' . $safeFileName;
    $uploadFile = $uploadDir . $uniqueFileName;

    // アップロード先ディレクトリが存在しない場合は作成
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // ファイルを移動
    if (move_uploaded_file($_FILES['media']['tmp_name'], $uploadFile)) {
        echo "ファイルは正しくアップロードされました。\n";
        $media = $uniqueFileName;
    } else {
        echo "ファイルのアップロードに失敗しました。\n";
    }
} else {
    echo "動画のアップロードにエラーがあります。エラーメッセージ: " . $_FILES['media']['error'] . "\n";
    print_r($_FILES['media']);
}


// DB接続
$pdo = connect_to_db();

// データベースにデータを挿入
$sql = 'INSERT INTO todo_table (id,name,support,doko,place,nojapan,itu,media, created_at, updated_at) VALUES (NULL, :name,:support,:doko,:place,:nojapan,:itu,:media, now(), now())';
$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':support', $support, PDO::PARAM_STR);
$stmt->bindValue(':doko', $where, PDO::PARAM_STR);
$stmt->bindValue(':place', $place, PDO::PARAM_STR);
$stmt->bindValue(':nojapan', $nojapan, PDO::PARAM_STR);
$stmt->bindValue(':itu', $when, PDO::PARAM_STR);
$stmt->bindValue(':media', $media, PDO::PARAM_STR);


// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
};

// 入力ページにリダイレクト
header("Location: todo_input.php?success=true");
exit();
