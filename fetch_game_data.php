<?php
include("./funcs.php");
include("./db.php");

$command = $_POST["command"];
$game_id = $_POST["game_id"];

// 命令がゲームデータの取得であれば
if($command == "fetch_game_data"){
  // dbに接続
  $pdo = db_conn($conn["db_name"],$conn["db_host"],$conn["db_id"],$conn["db_pw"]);

  //２．データ登録SQL作成
  $stmt = $pdo->prepare("SELECT * FROM othlo_game_data_table WHERE id=:id");
  $stmt->bindValue(":id",$game_id,PDO::PARAM_INT);
  $status = $stmt->execute();

  //３．データ表示
  if($status==false) {
      sql_error($stmt);
  }else{
      $r = $stmt->fetch();
      
      $user_id=$r["user_id"];
      if($game_id != $r["id"]);{
        $game["b_player"]=$r["b_player"];
        $game["w_player"]=$r["w_player"];
        $game["current_turn"]=$r["current_turn"];
        $game["piece_arrange"]=json_decode($r["json_piece_arrange"]);
        
        $json_game=json_encode($game);
        
        echo $json_game;
        
      }
    }
  

}



?>
