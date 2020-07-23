<?php
include("./funcs.php");
include("./db.php");

$game_info = $_GET["game_info"];
$key = "asobinojikan";
// $dec_game_info = dec_aes128cbc($game_info,$key);
// $game_data=explode(",",$game_info);
$dec_game_info = dec_aes128cbc($game_info,$key);
$game_data=explode(",",$dec_game_info);
// $game_id = intval($game_data[0]);
$game_id = $game_data[0];
$u_type = $game_data[1];
$u_name = $_GET["u_name"];



?>