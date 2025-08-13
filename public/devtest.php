<?php
$url = "https://laughing-space-garbanzo-7v4rqrjwg6j7crrqq-3000.app.github.dev/api/mnbitoken?token=5963e953d3616bc9c1c84369c318589c8a05d312";

//cURLセッションを初期化する
$ch = curl_init();

//URLとオプションを指定する
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//URLの情報を取得する
$res =  curl_exec($ch);

//結果を表示する
echo print_r($res,true);

//セッションを終了する
curl_close($conn);


//$contents = json_decode("https://laughing-space-garbanzo-7v4rqrjwg6j7crrqq-3000.app.github.dev/api/mnbitoken?token=5963e953d3616bc9c1c84369c318589c8a05d312", TRUE);
//$temp = stripslashes("https://laughing-space-garbanzo-7v4rqrjwg6j7crrqq-3000.app.github.dev/api/mnbitoken?token=5963e953d3616bc9c1c84369c318589c8a05d312");
//$contents = json_decode($temp, TRUE);
//var_dump($contents);
//echo $contents;
echo $_SERVER['REMOTE_ADDR'];
?>