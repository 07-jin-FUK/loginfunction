<?php
// データ受け取り
session_start();
include('functions.php');

$mail = $_POST['mail'];
$password = $_POST['password'];

// DB接続
$pdo = connect_to_db();

// SQL実行
$sql = 'SELECT * FROM users_table WHERE mail=:mail AND password=:password AND deleted_at IS NULL';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
$stmt->bindValue(':password', $password, PDO::PARAM_STR);

// ユーザ有無で条件分岐

try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo '<p>メールアドレスもしくはパスワードが一致していません</p>';
    echo '<a href="./login.php">login</a>';
} else {
    $_SESSION = array();
    $_SESSION['session_id'] = session_id();
    $_SESSION['is_admin'] = $user['is_admin'];
    $_SESSION['mail'] = $user['mail'];
    $_SESSION['id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    header("Location:todo_input.php");
    exit();
}
