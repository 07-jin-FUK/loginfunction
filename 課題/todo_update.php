<?php
// 入力項目のチェック
include('functions.php');
session_start();


if (
    !isset($_POST['name']) || $_POST['name'] === '' ||
    !isset($_POST['support']) || $_POST['support'] === '' ||
    // !isset($_POST['doko']) || $_POST['doko'] === '' ||
    // !isset($_POST['place']) || $_POST['place'] === '' ||
    // !isset($_POST['nojapan']) || $_POST['nojapan'] === '' ||

    !isset($_POST['itu']) || $_POST['itu'] === ''


) {
    exit('お名前とどなたといつ行ったのかの入力は必須です（場所は任意)');
};
// var_dump($_POST);
// exit();

$name = $_POST['name'];
$support = $_POST['support'];
$doko = $_POST['doko'];
$place = $_POST['place'];
$nojapan = $_POST['nojapan'];
$itu  = $_POST['itu'];
$id = $_POST['id'];


// DB接続
$pdo = connect_to_db();

// 動画ファイルのアップロード処理
$media = null; // 初期値は null
if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
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
        $media = $uniqueFileName;
    } else {
        echo "ファイルのアップロードに失敗しました。\n";
        exit();
    }
}

// SQL実行
// 新しい動画ファイルがある場合とない場合でSQLを分ける
if ($media) {
    $sql = 'UPDATE todo_table SET name=:name, support=:support, doko=:doko, place=:place, nojapan=:nojapan, itu=:itu, media=:media, updated_at=now() WHERE id=:id';
} else {
    $sql = 'UPDATE todo_table SET name=:name, support=:support, doko=:doko, place=:place, nojapan=:nojapan, itu=:itu, updated_at=now() WHERE id=:id';
}

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':support', $support, PDO::PARAM_STR);
$stmt->bindValue(':doko', $doko, PDO::PARAM_STR);
$stmt->bindValue(':place', $place, PDO::PARAM_STR);
$stmt->bindValue(':nojapan', $nojapan, PDO::PARAM_STR);
$stmt->bindValue(':itu', $itu, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
if ($media) {
    $stmt->bindValue(':media', $media, PDO::PARAM_STR);
}

try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

header('Location: todo_read.php');
exit();
