<?php
include("./funcs.php");
include("./db.php");

$enc_append_data = strstr($_SERVER['REQUEST_URI'],'append_data=');
$enc_append_data2 = substr($enc_append_data,12);
$cyper_append_data = urldecode($enc_append_data2);

$key = "asobinojikan";
$append_data = dec_aes128cbc($cyper_append_data,$key);

$data = explode("&",$append_data);

$game_id = $data[0];
$u_type = substr($data[1],7);
$b_player = substr($data[2],9);
$w_player = substr($data[3],9);

$hidden_data = $game_id;
$hidden_data .= ",";
$hidden_data .= $u_type;

$cypher_game_info = enc_aes128cbc($hidden_data,$key);
$enc_game_info=urlencode($cypher_game_info);

$temp = dec_aes128cbc($cypher_game_info,$key);

?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <p><?=$b_player?>さんと<?=$w_player?>さんのオセロゲームを覗くために、表示名を入力してください</p>
  <div>表示名: <input type="text" id="u_name"> </div>
  <button id="fixed">覗く</button>
  <!-- <p><?=$append_data?></p> -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
  document.querySelector('#fixed').onclick=function() {

    const u_name = $("#u_name").val();

    let location = "./othelo.php?game_info=";
    location += '<?=$enc_game_info?>';
    location += "&u_name=";
    location +=u_name;
    console.log(location);

    window.location.href = location;


  }


</script>
</body>
</html>