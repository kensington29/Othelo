<?php 
include("./funcs.php");
include("./db.php");

$json_game_info = $_GET["game_info"];
$key = "asobinojikan";
// $dec_game_info = dec_aes128cbc($game_info,$key);
// $game_data=explode(",",$game_info);
$dec_game_info = urldecode($json_game_info);
// $game_info = dec_aes128cbc($dec_game_info,$key);
$game_info = dec_aes128cbc($json_game_info,$key);
// echo($game_info);
$game_data=explode(",",$game_info);
// $game_id = intval($game_data[0]);
$game_id = $game_data[0];
$u_type = $game_data[1];
$u_name = $_GET["u_name"];

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>オセロゲーム</title>
      <!-- デフォルトの挙動を解除するためのcss -->
      <link rel="stylesheet" href="CSS/reset.css">

<!--  -->
      <!-- <script src="nico.js"></script> -->
      <!-- <script src="sample.js"></script> -->
<!--  -->

      <!-- これは自分で作ったcssファイル -->
      <link rel="stylesheet" href="css/style_othelo.css">


      <link rel="stylesheet" type="text/css" href="css/nncomment.css" />

</head>
<body>



<!-- <div id="nico"></div> -->

<div id="site_box">
  <div id="sb_inner">
    <div id="header">
      <div id="he_inner">
        <h1>オセロゲーム</h1>
        <div>名前: <?=$u_name?> </div>
      </div>
    </div>
    <div id="main_bord">
      <div id="mb_inner">
        <div id="play_table">
          <div id="othelo_bord"></div>
          <button id="fixed">決定</button>
        </div>
        <div id="player_zone">
          <div id="pz_inner">
            <table class="players">
              <tr>
                <th class="player" id="black">黒</th>
                <td class="name" id="player1"></td>
                <td class="turn" id="b_turn"></td>
              </tr>
              <tr>
                <th class="player" id="white">白</th>
                <td class="name" id="player2"></td>
                <td class="turn" id="w_turn"></td>
              </tr>
            </table>
            <table class="rubbernecks">
              <tr>
                <th class="player" id="rubberneck">やじうま</th>
                <td id="rn_names"></td>
              </tr>
            </table>
            <div id="input_comment"> 
              <textarea id="comment" cols="40" rows="4"></textarea>
              <button id="send">送信</button>
            </div>

            <div id="output"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




<!-- JQuery -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<!-- JQuery -->


<!--** 以下Firebase **-->
<!-- The core Firebase JS SDK is always required and must be listed first -->
<!-- <script src="https://www.gstatic.com/firebasejs/7.14.3/firebase.js"></script> -->

<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->

<script src="js/nncomment.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>


const maxsize = 64 ; //グリッド数
const slot_size = 50 ; //グリッドの一辺の長さ　px

const black = Number(-1);
const white = Number(1);
const vacant = Number(0);

// let u_name; // ユーザ名
let comment; //コメント
let piece_arrange; //コマの配置　0−63、黒:−1　白:1　なし:0
let b_player; //黒プレイヤー名
let w_player; //白プレイヤー名
let current_turn; //現在の手番　-1 or 1
let cw_no=0; //現在のコメントウインドウの番号
let last_comment_id = 99; //コメントのキュー番号初期値

let grid_piece_placed; //クリックされたグリッドの番号　1−64

let my_turn; //プレイヤーが手番か否か


const command = "fetch_game_data";

const game_id = <?=$game_id?>;
const u_type = <?=$u_type?>;
const u_name = "<?=$u_name?>";

console.log(game_id,u_type,u_name);

refresh_display();

refresh_display();
let timerId = setTimeout(function tick() {
  if( !my_turn ){
    refresh_display();
  }
  timerId = setTimeout(tick, 200); // (*)
}, 200);

function update_game_data(command,game_id,current_turn, piece_arrange){
  // let game=[];
    console.log(command,game_id,current_turn,piece_arrange);
    const params = new URLSearchParams();
    params.append('command',command);
    params.append('game_id',game_id);
    params.append('current_turn',current_turn);
    let json_piece_arrange = JSON.stringify(piece_arrange);
    params.append('json_piece_arrange',json_piece_arrange);

    //axiosでAjax送信
    axios.post('./update_game_data.php',params).then(function (response) {
        console.log(response.data);//通信OK

        if(command == 'finish_turn'){
          change_turn();
        }
                  
        my_turn = chk_turn();
        bord_refresh();
        player_reflesh();
        disp_turn();
        refresh_display();

    }).catch(function (error) {
        console.log(error);//通信Error
    }).then(function () {
        // console.log("Last");//通信OK/Error後に処理を必ずさせたい場合

    });

}

// 表示の更新
function refresh_display(){
    //Ajax（非同期通信）
    // let game=[];
    const params = new URLSearchParams();
    params.append('command',command);
    params.append('game_id',game_id);

    //axiosでAjax送信
    axios.post('./fetch_game_data.php',params).then(function (response) {
        // console.log(response.data);//通信OK
        b_player=response.data["b_player"];
        w_player=response.data["w_player"];
        current_turn=response.data["current_turn"];
        piece_arrange=response.data["piece_arrange"];
                  
        my_turn = chk_turn();
        bord_refresh();
        player_reflesh();
        disp_turn();
    }).catch(function (error) {
        console.log(error);//通信Error
    }).then(function () {
        // console.log("Last");//通信OK/Error後に処理を必ずさせたい場合

    });
}


  // 手番の表示
  function disp_turn(){
    let ct_disp = '<p>◀︎</p>';
    $("#b_turn").empty();
    $("#w_turn").empty();
    if(current_turn== -1){
      $("#b_turn").append(ct_disp);
    }else{
      $("#w_turn").append(ct_disp);
    }
  }

// 手番の交代
function change_turn(){
  current_turn *= -1 ;
}

    ///////////////////////////////////////////////////////
    // ウインドウを開いた時の動作
    ///////////////////////////////////////////////////////
    window.addEventListener('load', function() {
      // firebase.database().ref('msg').remove();
      // 64個のグリッドを生成
      create_grid("othelo_bord",maxsize);
      // create_c_window();

      // 各グリッドにクリックイベントを設定
      let grids = document.getElementsByClassName("grid");
      for (let i = 0; i < grids.length; i++){
      grids[i ].onclick = function(){clk_grids(this);};
      }

      //全てのグリッドにコマを配置する。コマは透明とし、pointer-events:noneを設定する
      //pointer-events:noneの設定は、下の要素（グリッド）のクリックを有効にするため
      set_piece(maxsize);

      // refresh_display();

    })
    
    ///////////////////////////////////////////////////////
    //グリッドがクリックされた場合の動作
    ///////////////////////////////////////////////////////
    function clk_grids(ele){
      let element_id =ele.id;
      let grid_no=Number(element_id.substr(14));
      //クリックの有効性を判定する
      let chk_click = check_click(grid_no);
      if(chk_click){
        // alert(element_id+","+grid_no);
        if(grid_piece_placed != null){
          document.getElementById('piece_no'+grid_piece_placed ).style.backgroundColor = "transparent";
          piece_arrange[grid_piece_placed-1]=vacant;
        }
        piece_arrange[grid_no-1]=Number(current_turn);
        if(current_turn == black){
          document.getElementById('piece_no'+grid_no).style.backgroundColor = black;
        }else{
          document.getElementById('piece_no'+grid_no).style.backgroundColor = white;
        }
        grid_piece_placed = grid_no;
        const game = {
          b_player: b_player,
          w_player: w_player,
          current_turn: current_turn,
          piece_arrange: piece_arrange
        }

        update_game_data("put_piece",game_id,current_turn,piece_arrange);
        // ref.push(game);
      }

    }

    // グリッドのクリックの有効性判定関数
    function check_click(grid_no){
      // u_name = $("#u_name").val();
      if(piece_arrange[grid_no-1] != vacant){
        //グリッドが空きではない
        return false;
      }else if((!my_turn)){
                 console.log(current_turn,u_name,b_player,w_player)
        return false;
      }
      return true;
    }

    ///////////////////////////////////////////////////////
    //n×nのグリッドを生成する関数
    ///////////////////////////////////////////////////////
    function create_grid(parent_idname, grid_size){
      // parent_id : グリッドを配置する要素のid名(#なし)
      // grid_size : グリッド内のスロット数（例：３×３の場合、９）

      let parent_id = "#"+parent_idname;

      // grid_size = 25;

      // const slot_size = 50; //グリッドの辺の長さ　（ｐｘ）
      let num_row = Math.round(Math.sqrt(grid_size));
      let side_length = num_row*slot_size;
      console.log(grid_size,parent_id);
      $(parent_id).empty();
      $(parent_id).width(side_length);
      $(parent_id).height(side_length);
      for(let i = 1; i < grid_size+1; i++){
        $(parent_id).append('<div class="grid" id="'+parent_idname+'_no'+i+'"></div>');
        document.getElementById(parent_idname+'_no'+i).style.width = slot_size-2+"px";
        document.getElementById(parent_idname+'_no'+i).style.height = slot_size-2+"px";
        document.getElementById(parent_idname+'_no'+i).style.border = '1px solid darkgray';
      }
    }

    ///////////////////////////////////////////////////////
    //全てのグリッドにコマを配置する関数
    ///////////////////////////////////////////////////////
    function set_piece(grid_size){

      for (let i = 1; i < grid_size+1; i++ ){
        let parent_id = "othelo_bord_no"+i;
        let parent= document.getElementById(parent_id);
        let elem=document.createElement('div') ;
        elem.id='piece_no'+i ;
        elem.className='piece';
        parent.appendChild(elem);
        document.getElementById('piece_no'+i).style.width = slot_size-10+"px";
        document.getElementById('piece_no'+i).style.height = slot_size-10+"px";
        document.getElementById('piece_no'+i).style.margin = "auto auto";
        document.getElementById('piece_no'+i).style.borderRadius = "50%";
        document.getElementById('piece_no'+i).style.backgroundColor = "transparent";
      }
    }


  ///////////////////////////////////////////////////////
  // 決定イベント
  ///////////////////////////////////////////////////////
    $("#fixed").on("click", function(){
      // piece_arrange[grid_piece_placed-1]=current_turn;
      grid_piece_placed = null;
      change_turn(); //暫定　挟まれたコマの裏返し、勝敗判定、相手がコマをおけりかどうかの確認が必要
      console.log(current_turn,piece_arrange);

      const game = {
        b_player: b_player,
        w_player: w_player,
        current_turn: current_turn,
        piece_arrange: piece_arrange
      };

      update_game_data("finish_turn",game_id,current_turn,piece_arrange);
      // ref.push(game);
    });



  ///////////////////////////////////////////////////////
  // 送信イベント 
  ///////////////////////////////////////////////////////
    $("#send").on("click", function(){
  const u_name = $("#u_name").val();
  const comment = $("#comment").val();
  const msg = {
    u_name: u_name,
    comment: comment
  };
  // ref.push(msg);//送信する
  send_comment(comment);
  clear_comment(); 
});

function send_comment(comment){

console.log("comment",game_id,u_name,comment);
const params = new URLSearchParams();
params.append('game_id',game_id);
params.append('u_name',u_name);
params.append('comment',comment);

//axiosでAjax送信
axios.post('./send_comment.php',params).then(function (response) {
    console.log(response.data);//通信OK

}).catch(function (error) {
    console.log(error);//通信Error
}).then(function () {
    // console.log("Last");//通信OK/Error後に処理を必ずさせたい場合

});

fetch_comment();
}

// textarea(#comment)のクリア
function clear_comment(){
  let textForm = document.getElementById("comment");
    textForm.value = '';
}


///////////////////////////////////////////////////////
// 受信イベント
///////////////////////////////////////////////////////
// ref.on("child_added", function(data){
// // ref.on("child_changed", function(data){
//   const v = data.val(); //オブジェクト変数がvに代入
//   const k = data.key; //

//   if(typeof v.comment !== "undefined"){
//     const h = '<p>'+v.u_name+'：<br>'+v.comment+'</p>';
//     $("#output").prepend(h);

//     const comment = '【'+v.u_name+'】'+v.comment;
//     $('#play_table').comment(comment);
//   }

function fetch_comment(){

  console.log("comment",game_id,last_comment_id);
  const params = new URLSearchParams();
  params.append('game_id',game_id);
  params.append('last_comment_id',last_comment_id);

  //axiosでAjax送信
  axios.post('./fetch_comment.php',params).then(function (response) {
      console.log(response.data);//通信OK

  }).catch(function (error) {
      console.log(error);//通信Error
  }).then(function () {
      // console.log("Last");//通信OK/Error後に処理を必ずさせたい場合

});

}

  // if(typeof v.current_turn !== "undefined"){
  //   b_player = v.b_player;
  //   w_player = v.w_player;
  //   current_turn = v.current_turn;
  //   piece_arrange = v.piece_arrange;
  
  //   console.log(b_player);
  //   console.log(w_player);
  //   console.log(current_turn);
  //   console.log(piece_arrange);

  //   my_turn = chk_turn();

  //   bord_refresh();
  //   player_reflesh();
  //   disp_turn();
  // }
// });

function chk_turn(){
  // const u_name = $("#u_name").val();
  if(u_type == 0){
    $('#fixed').prop('disabled',true);
    return false;
  }
  const obj = document.querySelector("#othelo_bord_no1")
  if(current_turn==black && u_name == b_player){
    // alert("ok")
    $('#fixed').prop('disabled',false);
    return true;
  }else if(current_turn==white && u_name == w_player){
    // alert("ok")
    $('#fixed').prop('disabled',false);
    return true;
  }else{
    // alert("ng")
    $('#fixed').prop('disabled',true);
    return false;
  }

}


function player_reflesh(){
  const display_b_player = document.getElementById("player1").textContent;
  const display_w_player = document.getElementById("player2").textContent;
  // alert(display_b_player);
  if(display_b_player != b_player){
    $("#player1").empty();
    $("#player1").append(b_player);
    
  }
  if(display_w_player != w_player){
    $("#player2").empty();
    $("#player2").append(w_player);

  }

}


//　ボード表示の更新
function bord_refresh(){
  for(let i=1; i< maxsize+1; i++){
    if(piece_arrange[i-1] == black){
        document.getElementById('piece_no'+i).style.backgroundColor = "black";
    }else if(piece_arrange[i-1] == white){
        document.getElementById('piece_no'+i).style.backgroundColor = "white";
    }else{
        document.getElementById('piece_no'+i).style.backgroundColor = "transparent";
    }
  }
}

</script>


</body>
</html>
