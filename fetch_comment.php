<?php
include("./funcs.php");
include("./db.php");

$game_id = $_POST["game_id"];
$last_comment_id = $_POST["last_comment_id"];

echo($game_id);
echo($last_comment_id);

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


  // コメント取得
  //取得条件
if ( $latest_id == $last_comment_id){
  // 前回の取得からコメントが増えていない場合何もしない
  return;
}elseif($latest_id > $last_comment_id){
  if($latest_id - $last_comment_id <= 3){
    //前回の取得からコメントの増加が３以下の場合増加分を全部とる
    $condition = "(queue_id<=$latest_id) && (queue_id >$last_comment_id)";
  }else{
    // 前回の取得から３つ以上コメントが増えている場合、最新３つのみとる
    $condition = "(queue_id<=$latest_id) && (queue_id >$latest_id-3)";
  }
  
}elseif($latest_id >= 3){
  //最新のキューidが前回取得のコメントidより小さい場合、キューidが戻っている
  // 最新のキューidが３以上であれば、そのまま最新から３つのみとる
  $condition = "(queue_id<=$latest_id) && (queue_id >$latest_id-3)";
  
}elseif(($latest_id <3) && (99-$last_comment_id+$latest_id >=3)){
  // 前回の取得から３つより多いコメントが増えている場合、最新３つのみとる
  $condition = "(queue_id<=$latest_id) || (queue_id > 97+$latest_id)";
}else{
  // 前回の取得からの増加が３以下の場合
  $condition = "(queue_id<=$latest_id) || (queue_id >$last_comment_id)";
}
//コメント取得
$stmt = $pdo->prepare("SELECT * FROM othello_comment WHERE queue_id = '$condition' " );
$status = $stmt->execute();

$view="";
if($status==false) {
    //execute（SQL実行時にエラーがある場合）
  $error = $stmt->errorInfo();
  exit("SQLError:".$error[2]);

}else{
  //Selectデータの数だけ自動でループしてくれる
  while( $r = $stmt->fetch(PDO::FETCH_ASSOC)){ 
      $view = '【'.$r["u_name"].'】'.$r["comment"];
      echo ($view);
}
}
// 最後に読んだコメントのidを最新のコメントのidにする
$last_comment_id = $latest_id;


if($status==false) {
  sql_error($stmt);
}else{
  echo("success");
}



echo "success";
exit;

?>



