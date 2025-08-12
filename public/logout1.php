<?php

// 「ログアウト」実行ページ



// ■■■■■■■■■■■■■■■■
// ■        外部ファイル        ■
// ■■■■■■■■■■■■■■■■

// 共通処理
require __DIR__ . '/common.php';



// ■■■■■■■■■■■■■■■■
// ■          内部関数          ■
// ■■■■■■■■■■■■■■■■

// メイン関数
function Logout_main($hb)
{
    session_start();
    // Cookieをクリア
    $sSession = session_name();
    if (isset($_COOKIE[$sSession])) {
        setcookie($sSession, '', time() - 3600, '/');
    }
    // セッション変数をクリア
    $_SESSION = [];
    // セッションを破棄
    session_destroy();

    // リダイレクト
    header('Location: ' . PFILE_LOGOUT2);
    exit;
}



// ■■■■■■■■■■■■■■■■
// ■         メイン処理         ■
// ■■■■■■■■■■■■■■■■

startMain('Logout_main', 'putErrorMessage');

?>
