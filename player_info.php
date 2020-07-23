<?php
include("./funcs.php");

// POSTデータ取得
$json_game = $_POST["json_game"];
echo $jason_game;

// JSONファイルのデコード
$game = json_decode($json_game,true);

// DB接続
$pdo = db_conn("php_contest","localhost","root","");

$stmt = $pdo->prepare("INSERT INTO othlo_game_data_table(user_id,game,indate)VALUES(:user_id,:game,sysdate())");
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':game', $json_game, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();

?>