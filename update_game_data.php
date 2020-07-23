<?php
include("./funcs.php");
include("./db.php");

$command = $_POST["command"];
$game_id = $_POST["game_id"];
$current_turn = $_POST["current_turn"];
$json_piece_arrange =  $_POST["json_piece_arrange"];

// 命令がゲームデータの取得であれば
// dbに接続
$pdo = db_conn($conn["db_name"],$conn["db_host"],$conn["db_id"],$conn["db_pw"]);


if($command == "put_piece"){
  $sql = "UPDATE othlo_game_data_table SET json_piece_arrange=:json_piece_arrange WHERE id=:id";
}elseif($command == "finish_turn"){
  $sql = "UPDATE othlo_game_data_table SET current_turn=:current_turn,json_piece_arrange=:json_piece_arrange WHERE id=:id";
}

//4.SQL
$stmt = $pdo->prepare($sql);
//5.Bind変数へ代入
if($command == "finish_turn"){
  $stmt->bindValue(':current_turn', $current_turn, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
}
$stmt->bindValue(':json_piece_arrange', $json_piece_arrange, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':id', $game_id, PDO::PARAM_INT);
$status = $stmt->execute();


echo "success";
exit;

?>