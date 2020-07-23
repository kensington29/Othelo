<?php
//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($str){
    return htmlspecialchars($str, ENT_QUOTES);
}

//DB接続
//DB接続関数：db_conn()
function db_conn($db_name,$db_host,$db_id,$db_pw){
  try {
      // $db_name = "gs_db3";    //データベース名
      // $db_id   = "root";      //アカウント名
      // $db_pw   = "root";      //パスワード：XAMPPはパスワード無しに修正してください。
      // $db_host = "localhost"; //DBホスト
      $pdo = new PDO('mysql:dbname='.$db_name.';charset=utf8;host='.$db_host, $db_id, $db_pw);
      return $pdo;
  } catch (PDOException $e) {
      exit('DB Connection Error:'.$e->getMessage());
  }
}

//SQLエラー
function sql_error($stmt){
    //execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit("SQLError:".$error[2]);
}

//リダイレクト
function redirect($file_name){
    header("Location: ".$file_name);
    exit();
}
//SessionCheck
function sschk(){
  if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"]!=session_id()){
    exit("Login Error");
  }else{
    session_regenerate_id(true);
    $_SESSION["chk_ssid"] = session_id();
  }
}

//fileUpload("送信名","アップロード先フォルダ");
function fileUpload($fname,$path){
    if (isset($_FILES[$fname] ) && $_FILES[$fname]["error"] ==0 ) {
        //ファイル名取得
        $file_name = $_FILES[$fname]["name"];
        //一時保存場所取得
        $tmp_path  = $_FILES[$fname]["tmp_name"];
        //拡張子取得
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);
        //ユニークファイル名作成
        $file_name = date("YmdHis").md5(session_id()) . "." . $extension;
        // FileUpload [--Start--]
        $file_dir_path = $path.$file_name;
        if ( is_uploaded_file( $tmp_path ) ) {
            if ( move_uploaded_file( $tmp_path, $file_dir_path ) ) {
                chmod( $file_dir_path, 0644 );
                return $file_name; //成功時：ファイル名を返す
            } else {
                return 1; //失敗時：ファイル移動に失敗
            }
        }
     }else{
         return 2; //失敗時：ファイル取得エラー
     }
}

// aes-128-cbcエンコード
function enc_aes128cbc($original_txt,$key){
    $cipher = "aes-128-cbc";
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $cyper_app_data_r_raw = openssl_encrypt($original_txt, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $cyper_app_data_r_raw, $key, $as_binary=true);
    $cipher_txt= base64_encode( $iv.$hmac.$cyper_app_data_r_raw );

    return $cipher_txt;
}

// aes-128-cbcデコード
function dec_aes128cbc($cipher_txt,$key){
    $cipher = "aes-128-cbc";
    $c = base64_decode($cipher_txt);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen+$sha2len);
    $original_txt = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    return $original_txt;
}

?>