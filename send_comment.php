<?php
include("./funcs.php");
include("./db.php");

$game_id = $_POST["game_id"];
$u_name = $_POST["u_name"];
$comment = $_POST["comment"];

//dbに接続
$pdo = db_conn($conn["db_name"],$conn["db_host"],$conn["db_id"],$conn["db_pw"]);

// 書き込むキューid（latest_id）を取得
//２．データ登録SQL作成
$stmt = $pdo->prepare("SELECT latest_id FROM Othello_comment_management WHERE game_id=:game_id");
$stmt->bindValue(":game_id",$game_id,PDO::PARAM_INT);
$status = $stmt->execute();

//３．データ表示
if($status==false) {
    sql_error($stmt);
}else{
    $r= $stmt->fetch();
    $latest_id = $r["latest_id"];
  }

if($latest_id+1< 100){
  $target_id=$latest_id+1;
}else{
  $target_id=$latest_id+1-100;
}
// コメントの書き込み

echo($target_id);
$sql = "UPDATE othello_comment SET u_name=:u_name, comment=:comment WHERE game_id=:game_id AND queue=:queue";

//4.SQL
$stmt = $pdo->prepare($sql);
//5.Bind変数へ代入
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_INT); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':u_name', $u_name, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
$stmt->bindValue(':queue', $target_id, PDO::PARAM_INT);
$status = $stmt->execute();

if($status==false) {
  sql_error($stmt);
}else{
  echo("success");
}

// コメント管理データの更新
$sql = "UPDATE Othello_comment_management SET latest_id=:latest_id WHERE game_id=:game_id";

//4.SQL
$stmt = $pdo->prepare($sql);
//5.Bind変数へ代入
$stmt->bindValue(':latest_id', $target_id, PDO::PARAM_INT);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_INT);
$status = $stmt->execute();


echo "success";
exit;

?>