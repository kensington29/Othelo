<?php
include("./funcs.php");
include("./db.php");

// $game   = json_decode(file_get_contents('php://input'), true);
$game   = json_decode($_POST["game"],true);


$b_player   = $game["b_player"];
$w_player = $game["w_player"];
$current_turn = $game["current_turn"];
$json_piece_arrange = json_encode($game['piece_arrange']);

// 暫定
$user_id = 1;
// 暫定

$pdo = db_conn($conn["db_name"],$conn["db_host"],$conn["db_id"],$conn["db_pw"]);

//３．ゲーム初期データ登録SQL作成
$sql = "INSERT INTO othlo_game_data_table(user_id,b_player,w_player,current_turn,json_piece_arrange,indate)VALUES(:user_id,:b_player,:w_player,:current_turn,:json_piece_arrange,sysdate())";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':b_player', $b_player, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':w_player', $w_player, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':current_turn', $current_turn, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':json_piece_arrange', $json_piece_arrange, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();

//４．データ登録処理後
if ($status == false) {
    sql_error($stmt);
} else {
    //追加したゲームのidを返す
    $game_id = $pdo->lastInsertId();
}

// コメント管理データ作成
$latest_id = 0;
$sql = "INSERT INTO Othello_comment_management(game_id,latest_id)VALUES(:game_id,:latest_id)";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':latest_id', $latest_id, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();

// コメントキュー作成
for($queue=0;$queue<100;$queue++){
    $sql = "INSERT INTO othello_comment(game_id,queue)VALUES(:game_id,:queue)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
    $stmt->bindValue(':queue', $queue, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
    $status = $stmt->execute();
}

//４．データ登録処理後
if ($status == false) {
    sql_error($stmt);
} else {
    //追加したゲームのidを返す
    echo $game_id;
    exit;
}

?>