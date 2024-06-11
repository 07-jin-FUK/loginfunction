<?php
include('functions.php'); // データベース接続関数を含むファイル
session_start();

// デバッグ情報の出力
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
// exit();


// POSTデータを受け取る
$username = $_POST['username'];
$mail = $_POST['mail'];
$birthday = $_POST['birthday'];
$bio = $_POST['bio'];

// プロフィール画像のアップロード処理
$picture = null;
if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
    $picture = 'uploads/' . basename($_FILES['picture']['name']);
    move_uploaded_file($_FILES['picture']['tmp_name'], $picture);
}



// データベースに接続
$pdo = connect_to_db();

// ユーザーがログインしているか確認
if (!isset($_SESSION['id'])) {
    echo "ログインしていません。";
    exit();
}

$id = $_SESSION['id'];


// ユーザー情報をデータベースに挿入
$sql = 'UPDATE users_table SET username=:username,mail=:mail,birthday=:birthday,picture=:picture,bio=:bio,updated_at=now() WHERE id=:id';
$stmt = $pdo->prepare($sql);

$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
$stmt->bindValue(':birthday', $birthday, PDO::PARAM_STR);
$stmt->bindValue(':picture', $picture, PDO::PARAM_STR);
$stmt->bindValue(':bio', $bio, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_STR);

if ($stmt->execute()) {
    // 更新成功
    $_SESSION['update_success'] = "プロフィールが更新されました！";
    // セッションに最新の情報を反映
    $_SESSION['name'] = $username;
    $_SESSION['mail'] = $mail;
} else {
    // エラー処理
    $_SESSION['update_error'] = "プロフィールの更新に失敗しました。";
}

// 更新後に指定のページにリダイレクト
header("Location: todo_input.php");
exit();
