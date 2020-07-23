<?php
include("./funcs.php");
include("./db.php");

$key = "asobinojikan";


$game = json_decode($_GET['json_game'],true);
$game_id = $_GET['game_id'];

$u_type = -1;
$append_data_b = $game_id;
$append_data_b .= ",";
$append_data_b .= $u_type;
$cyper_app_data_b= enc_aes128cbc($append_data_b,$key);
$json_app_data_b=urlencode($cyper_app_data_b);

$t_cyper_app_data_b = urldecode($json_app_data_b);
$t_app_data_b = dec_aes128cbc($t_cyper_app_data_b,$key);

$b_url = $url;
$b_url .= "php_contest_othelo/othelo.php?game_info=";
// $b_url .= $dec_app_data_b;
// $b_url .= $cyper_app_data_b;
$b_url .= $json_app_data_b;
$b_url .= "&u_name=";
$b_url .= $game["b_player"];

$u_type = 1;
$append_data_w = $game_id;
$append_data_w .= ",";
$append_data_w .= $u_type;
$cyper_app_data_w= enc_aes128cbc($append_data_w,$key);
$json_app_data_w=urlencode($cyper_app_data_w);
// $dec_app_data_w = dec_aes128cbc($cyper_app_data_w,$key);
$w_url = $url;
$w_url .= "php_contest_othelo/othelo.php?game_info=";
// $w_url .= $cyper_app_data_w;
$w_url .= $json_app_data_w;
$w_url .= "&u_name=";
$w_url .= $game["w_player"];

$u_type = 0;
$append_data_r = $game_id;
$append_data_r .= "&u_type=";
$append_data_r .= $u_type;

$append_data_r .= "&b_player=";
$append_data_r .= $game["b_player"];
$append_data_r .= "&w_player=";
$append_data_r .= $game["w_player"];

$cyper_app_data_r= enc_aes128cbc($append_data_r,$key);
$json_app_data_r=urlencode($cyper_app_data_r);
$r_url = $url;
$r_url .= "php_contest_othelo/rubbberneck_name_input.php?append_data=";
// $r_url .= $cyper_app_data_r;
$r_url .= $json_app_data_r;

$t_cypher_app_data_r = urldecode($json_app_data_r);
$t_append_data_r = dec_aes128cbc($cyper_app_data_r,$key);
?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>オセロゲームURL</title>
</head>
<body>
<div>
  <h1>黒手:<?=$game["b_player"]?>さんのURL</h1>
  <p><?=$b_url?></p><br>
  <!-- <p><?=$append_data_b?></p>
  <p><?=$cyper_app_data_b?></p>
  <p><?=$json_app_data_b?></p>
  <p><?=$t_cyper_app_data_b?></p>
  <p><?=$t_app_data_b?></p> -->
  <h1>白手:<?=$game["w_player"]?>さんのURL</h1>
  <p><?=$w_url?></p><br>
  <!-- <p><?=$dec_app_data_w?></p> -->
  <h1>やじうまさんたちのURL</h1>
  <p><?=$r_url?></p>
  <!-- <p><?=$cyper_app_data_r?></p>
  <p><?=$json_app_data_r?></p>
  <p><?=$t_cypher_app_data_r?></p>
  <p><?=$t_append_data_r?></p> -->
</div>
<div>

</div>
<h1>
  
</body>
</html>