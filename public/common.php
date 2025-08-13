<?php

// すべてのページに共通の処理



// ■■■■■■■■■■■■■■■■
// ■        外部ファイル        ■
// ■■■■■■■■■■■■■■■■

// TRUEならデバッグモード
define('F_DEBUG', file_exists(__DIR__ . '/lib/debug.php'));

// 基本ライブラリ
const MSG_DONTCOPY   = 'Do not save pics please.';                 // コピー不可の画像が操作されようとしたときに表示
const FNAME_BUTTON   = 'button';                                   // フォームのボタン
const FNAME_COMMAND  = 'command';                                  // フォームのコマンド
define('LFILE_SYSERROR', __DIR__ . '/../stat/M-NBI_error.log');    // (リリース時のみ)エラー発生時にログを記録するファイル
require __DIR__ . '/lib/basiclib.php';

// データベース
require __DIR__ . '/lib/database.php';



// ■■■■■■■■■■■■■■■■
// ■          共通定数          ■
// ■■■■■■■■■■■■■■■■

// URL
if (F_DEBUG) {
    define('URL_SSOLOGIN' , 'https://laughing-space-garbanzo-7v4rqrjwg6j7crrqq-3000.app.github.dev/api/mnbisso?backurl=index.php');    // SSOログイン
    define('URL_TOKENINFO', 'https://laughing-space-garbanzo-7v4rqrjwg6j7crrqq-3000.app.github.dev/api/mnbitoken?token=');             // IKEY_INFO取得
    define('URL_ERRORREF' , 'https://www.medicaltown.net/');                                 // エラー発生時の飛び先
    define('URL_LOGOUT'   , 'https://laughing-space-garbanzo-7v4rqrjwg6j7crrqq-3000.app.github.dev/mnbi/');                            // ログアウトボタンのあるページ
} else {
    define('URL_SSOLOGIN' , 'https://laughing-space-garbanzo-7v4rqrjwg6j7crrqq-3000.app.github.dev/api/mnbisso?backurl=index.php');    // SSOログイン
    define('URL_TOKENINFO', 'https://laughing-space-garbanzo-7v4rqrjwg6j7crrqq-3000.app.github.dev/api/mnbitoken?token=');             // IKEY_INFO取得
    define('URL_ERRORREF' , 'https://www.medicaltown.net/');                                 // エラー発生時の飛び先
    define('URL_LOGOUT'   , 'https://laughing-space-garbanzo-7v4rqrjwg6j7crrqq-3000.app.github.dev/mnbi/');                            // ログアウトボタンのあるページ
}

// 各種キー
const GKEY_TOKEN    = 'token';            // GETでトークンを要求するキー
const IKEY_USERKEY  = 'TOKEN_INFO_01';    // トークンからユーザキーを取得するキー
const IKEY_USERNAME = 'TOKEN_INFO_02';    // トークンからユーザ名を取得するキー
const IKEY_EXPTIME  = 'TOKEN_INFO_03';    // トークンから有効期限を取得するキー
const SKEY_USERKEY  = 'userkey';          // (セッション変数)ユーザキー
const SKEY_USERNAME = 'username';         // (セッション変数)ユーザ名
const SKEY_CHAPTER  = 'chapter';          // (セッション変数)現在のチャプタ(CI_～)
const SKEY_ANSWERS  = 'answers';          // (セッション変数)ラジオボタン選択による回答を表す配列
const SKEY_TIME     = 'time';             // (セッション変数)回答開始時のUnix時刻(ms単位)
const SKEY_EXAM     = 'exam';             // (セッション変数)40問テストの状態
const SKEY_VIDEO    = 'video';            // (セッション変数)講義動画の状態
const SKEY_MARATHON = 'marathon';         // (セッション変数)100問マラソンの状態
const SKEY_SLIDE    = 'slide';            // (セッション変数)スライドの状態

// デフォルト文字列
const DEF_USERNAME = 'ゲスト';    // 空文字列だった場合のユーザ名

// データベースで使用するパラメータ
const DBP_HOST     = '127.0.0.1';     // ホスト名
const DBP_DBNAME   = 'localdb';        // データベース名
const DBP_ACCOUNT  = 'localdb';          // ログインアカウント
const DBP_PASSWORD = 'localdb';    // ログインパスワード
const DBP_TABLE    = 'userdata';      // テーブル名

// データベースのカラム名
const DBC_USERKEY = 'userkey';    // ユーザキー
const DBC_LEVEL   = 'level';      // ユーザのレベル(閲覧できるのはレベルが表すチャプタまでなので、レベル0ではホーム以外を見ることができない)
const DBC_PRE     = 'pr';         // Pre-testの回答プリフィクス
const DBC_POST    = 'po';         // Post-testの回答プリフィクス

// PHPファイル
const PFILE_HOME     = '/index.php';
const PFILE_PREFACE  = '/preface.php';
const PFILE_EXAM     = '/exam.php';
const PFILE_VIDEO    = '/video.php';
const PFILE_MARATHON = '/marathon.php';
const PFILE_SLIDE    = '/slide.php';
const PFILE_LOGOUT1  = '/logout1.php';
const PFILE_LOGOUT2  = '/logout2.php';

// ディレクトリ
const DIR_EXAM     = '/exam/';
const DIR_VIDEO    = '/video/';
const DIR_MARATHON = '/marathon/';
const DIR_SLIDE1   = '/slide1/';
const DIR_SLIDE2   = '/slide2/';

// postされるformのname属性
const FNAME_CHAPTER = 'chapter';    // チャプタ
const FNAME_PHASE   = 'phase';      // フェーズ
const FNAME_STEP    = 'step';       // ステップ
const FNAME_TIME    = 'time';       // 開始時刻(ms単位)
const FNAME_SELECT  = 'select';     // ラジオボタンの選択

// postされるformのボタンのvalue属性
const FBTN_START    = 'start';       // [開始]
const FBTN_CONTINUE = 'continue';    // [続行]
const FBTN_BACKWARD = 'backward';    // [戻る]
const FBTN_FORWARD  = 'forward';     // [進む]
const FBTN_HOME     = 'home';        // [ホーム]

const FCMD_UPDATE   = 'update';      // データベースを更新し、表示すべきページを要求
const FCMD_CONTUNUE = 'continue';    // ページの最新位置の表示を要求

// タイトル
const TTL_MAIN     = 'E-learning system for EGC by M-NBI';
const TTL_SUB      = '～胃拡大内視鏡診断・Web学習システム～';

// テスト等の個数
const N_VIDEO      =  10;    // 講義動画
const MAX_EXAM     =  40;
const MAX_MARATHON = 100;
if (F_DEBUG) {
    define('N_EXAM'    , 2);
    define('N_MARATHON', 2);
    define('N_SLIDE1'  , 2);
    define('N_SLIDE2'  , 2);
} else {
    define('N_EXAM'    , MAX_EXAM    );    // 40問テスト
    define('N_MARATHON', MAX_MARATHON);    // 100問マラソン
    define('N_SLIDE1'  , 140         );    // 教材1のスライド
    define('N_SLIDE2'  , 356         );    // 教材2のスライド
}

// htmlソースのヘッダ/フッタの種類のインデックス
const HTT_HOME     = 0;    // タイトルがリンクではない
const HTT_LESSON   = 1;    // 講義用(青地)
const HTT_EXAM     = 2;    // 試験用(黒地)
const HTT_MARATHON = 3;    // 試験用(黒地)＋小さいフォント

// htmlソースのヘッダに埋め込むJavaScriptの種類
const JSF_SETSTARTMSEC  = 1;    // setStartMsec()を使用
const JSF_SETPASSEDMSEC = 2;    // setPassedMsec()を使用
const JSF_SETDISPTIMER  = 4;    // 開始時刻を埋め込んで経過時間を表示
const JSF_DISPTIME      = 8;    // 指定された時間を表示

// 動画定数のインデックス
const VC_FILE  = 0;    // (文字列)動画のファイル名
const VC_TIME  = 1;    // (文字列)動画の再生時間を表す文字列
const VC_COUNT = 2;

// チャプタのインデックス
const CI_HOME      =  0;    // ホーム
const CI_PREFACE   =  1;    // 初めに
const CI_EXAM1     =  2;    // 40問プレテスト(解答なし、1回切り)
const CI_VIDEO11   =  3;    // 入門編講義1
const CI_VIDEO12   =  4;    // 入門編講義2
const CI_VIDEO13   =  5;    // 入門編講義3
const CI_VIDEO14   =  6;    // 入門編講義4
const CI_VIDEO21   =  7;    // 応用編講義1
const CI_VIDEO22   =  8;    // 応用編講義2
const CI_VIDEO23   =  9;    // 応用編講義3
const CI_VIDEO24   = 10;    // 応用編講義4
const CI_VIDEO25   = 11;    // 応用編講義5
const CI_VIDEO26   = 12;    // 応用編講義6
const CI_MARATHON1 = 13;    // 100問マラソン1
const CI_MARATHON2 = 14;    // 100問マラソン2
const CI_MARATHON3 = 15;    // 100問マラソン3
const CI_EXAM2     = 16;    // 40問ポストテスト(解答なし)
const CI_EXAM2A    = 17;    // 40問テスト解答
const CI_SLIDE1    = 18;    // 補助教材スライド1
const CI_SLIDE2    = 19;    // 補助教材スライド2
const CI_COUNT     = 20;

// チャプタ定数のインデックス
const CC_NAME           = 0;    // (文字列)チャプタ名
const CC_VIEWCOLUMN     = 1;    // (文字列)受講回数が格納されるデータベースのカラム名
const CC_CONTCOLUMN     = 2;    // (文字列)コンティニュー位置が格納されるデータベースのカラム名
const CC_SCORECOLUMN    = 3;    // (文字列)コンティニュー位置までの得点が格納されるデータベースのカラム名
const CC_MAXSCORECOLUMN = 4;    // (文字列)最高得点が格納されるデータベースのカラム名
const CC_COUNT          = 5;

// パートのインデックス
const PI_PREFACE  = 0;    // 「初めに」
const PI_EXAM1    = 1;    // 40問テスト（e-learning受講前）
const PI_VIDEO1   = 2;    // 教材1（入門編講義）動画
const PI_VIDEO2   = 3;    // 教材2（応用編講義）動画
const PI_MARATHON = 4;    // 100問マラソン
const PI_EXAM2    = 5;    // 40問テスト（e-learning受講後）
const PI_SLIDE    = 6;    // 補助教材スライド
const PI_COUNT    = 7;

// パート定数のインデックス
const PC_CAPTION      = 0;    // キャプション
const PC_PHPFILE      = 1;    // PHPファイルの名前
const PC_FIRSTCHAPTER = 2;    // 最初のチャプタのインデックス
const PC_LASTCHAPTER  = 3;    // 最後のチャプタのインデックス
const PC_COUNT        = 4;

// main()のエラーコード
define('MRC_KEYINVALID'  , MRC_APPERR + 1);    // (3)認証トークンのキーのフォーマットが異常
define('MRC_TOKENOLD'    , MRC_APPERR + 2);    // (4)認証トークンが古い
define('MRC_FORMINVALID' , MRC_APPERR + 3);    // (5)不正なformを受信した
define('MRC_PAGEINVALID' , MRC_APPERR + 4);    // (6)不当なページが参照されようとした



// ■■■■■■■■■■■■■■■■
// ■          共通関数          ■
// ■■■■■■■■■■■■■■■■

// 動画の定数を取得
// <引数1> チャプタのインデックス(CI_VIDEO11～CI_VIDEO26)
// <引数2> 動画定数のインデックス(VC_～)
function getVideoConst($ixChapter, $ixVideoConst)
{
    assertRange($ixChapter, CI_VIDEO11, CI_VIDEO26);
    assertIndex($ixVideoConst, VC_COUNT);
    static $amTable = [
        [ 'chap11.mp4', '10:00', ],    // CI_VIDEO11
        [ 'chap12.mp4', '09:59', ],    // CI_VIDEO12
        [ 'chap13.mp4', '06:20', ],    // CI_VIDEO13
        [ 'chap14.mp4', '13:08', ],    // CI_VIDEO14
        [ 'chap21.mp4', '15:44', ],    // CI_VIDEO21
        [ 'chap22.mp4', '14:28', ],    // CI_VIDEO22
        [ 'chap23.mp4', '17:00', ],    // CI_VIDEO23
        [ 'chap24.mp4', '15:36', ],    // CI_VIDEO24
        [ 'chap25.mp4', '17:09', ],    // CI_VIDEO25
        [ 'chap26.mp4', '16:05', ],    // CI_VIDEO26
    ];
    assertArray2($amTable, VC_COUNT, CI_VIDEO26 - CI_VIDEO11 + 1);

    return $amTable[$ixChapter - CI_VIDEO11][$ixVideoConst];
}

// チャプタの定数を取得
// <引数1> チャプタのインデックス(CI_～)
// <引数2> チャプタ定数のインデックス(CC_～)
function getChapterConst($ixChapter, $ixChapterConst)
{
    assertIndex($ixChapter, CI_COUNT);
    assertIndex($ixChapterConst, CC_COUNT);
    static $amTable = [
        [ NULL                         , NULL      , NULL     , NULL      , NULL    , ],    // CI_HOME
        [ NULL                         , NULL      , NULL     , NULL      , NULL    , ],    // CI_PREFACE
        [ 'Pre-test'                   , 'pr_view' , 'pr_cont', 'pr_score', 'pr_max', ],    // CI_EXAM1
        [ '1-1'                        , 'v11_view', NULL     , NULL      , NULL    , ],    // CI_VIDEO11
        [ '1-2'                        , 'v12_view', NULL     , NULL      , NULL    , ],    // CI_VIDEO12
        [ '1-3'                        , 'v13_view', NULL     , NULL      , NULL    , ],    // CI_VIDEO13
        [ '1-4'                        , 'v14_view', NULL     , NULL      , NULL    , ],    // CI_VIDEO14
        [ '2-1'                        , 'v21_view', NULL     , NULL      , NULL    , ],    // CI_VIDEO21
        [ '2-2'                        , 'v22_view', NULL     , NULL      , NULL    , ],    // CI_VIDEO22
        [ '2-3'                        , 'v23_view', NULL     , NULL      , NULL    , ],    // CI_VIDEO23
        [ '2-4'                        , 'v24_view', NULL     , NULL      , NULL    , ],    // CI_VIDEO24
        [ '2-5'                        , 'v25_view', NULL     , NULL      , NULL    , ],    // CI_VIDEO25
        [ '2-6'                        , 'v26_view', NULL     , NULL      , NULL    , ],    // CI_VIDEO26
        [ '3-1.ランダムテスト1'        , 'm1_view' , 'm1_cont', 'm1_score', 'm1_max', ],    // CI_MARATHON1
        [ '3-2.体系的並び順による解答' , 'm2_view' , 'm2_cont', NULL      , NULL    , ],    // CI_MARATHON2
        [ '3-3.ランダムテスト2'        , 'm3_view' , 'm3_cont', 'm3_score', 'm3_max', ],    // CI_MARATHON3
        [ 'Post-test'                  , 'po_view' , 'po_cont', 'po_score', 'po_max', ],    // CI_EXAM2
        [ NULL                         , NULL      , NULL     , NULL      , NULL    , ],    // CI_EXAM2A
        [ '教材1（入門編講義）スライド', 's1_view' , 's1_cont', NULL      , NULL    , ],    // CI_SLIDE1
        [ '教材2（応用編講義）スライド', 's2_view' , 's2_cont', NULL      , NULL    , ],    // CI_SLIDE2
    ];
    assertArray2($amTable, CC_COUNT, CI_COUNT);

    return $amTable[$ixChapter][$ixChapterConst];
}

// パートの定数を取得
// <引数1> パートのインデックス(PI_～)
// <引数2> パート定数のインデックス(PC_～)
function getPartConst($ixPart, $ixPartConst)
{
    assertIndex($ixPart, PI_COUNT);
    assertIndex($ixPartConst, PC_COUNT);
    static $amTable = [
        [ '初めにご覧ください'            , PFILE_PREFACE , CI_PREFACE  , CI_PREFACE  , ],    // PI_PREFACE
        [ '40問テスト（e-learning受講前）', PFILE_EXAM    , CI_EXAM1    , CI_EXAM1    , ],    // PI_EXAM1
        [ '教材1（入門編講義）動画'       , PFILE_VIDEO   , CI_VIDEO11  , CI_VIDEO14  , ],    // PI_VIDEO1
        [ '教材2（応用編講義）動画'       , PFILE_VIDEO   , CI_VIDEO21  , CI_VIDEO26  , ],    // PI_VIDEO2
        [ '100問マラソン'                 , PFILE_MARATHON, CI_MARATHON1, CI_MARATHON3, ],    // PI_MARATHON
        [ '40問テスト（e-learning受講後）', PFILE_EXAM    , CI_EXAM2    , CI_EXAM2A   , ],    // PI_EXAM2
        [ '補助教材スライド'              , PFILE_SLIDE   , CI_SLIDE1   , CI_SLIDE2   , ],    // PI_SLIDE
    ];
    assertArray2($amTable, PC_COUNT, PI_COUNT);

    return $amTable[$ixPart][$ixPartConst];
}

// ヘッダを配置
// <引数1> テキストバッファ
// <引数2> ヘッダの種類(HTT_～)
// <引数3> JavaScriptの使用フラグ(JSF_～)
// <引数4> JavaScriptの関数setDispTimer()に与える開始時刻(0可)
function putHeader($hb, $ixMode, $fJavaScript, $dStartMsec)
{
    $hb->puts('<!doctype html>');
    $hb->puts('<html dir="ltr" lang="ja">');
    $hb->puts('<head>');
    $hb->puts('<meta charset="utf-8">');
    $hb->puts('<meta http-equiv="X-UA-Compatible" content="IE=Edge">');
    $hb->puts('<meta name="description" content="' . TTL_MAIN . '">');
    $hb->puts('<meta name="keywords" content="オリンパス,内視鏡,e-learning">');
    $hb->puts('<meta name="viewport" content="width=device-width,initial-scale=1.0">');
    $hb->puts('<meta name="format-detection" content="telephone=no">');

    $hb->puts('<script type="text/javascript">');
    $hb->puts('window.onunload = function(){};');
    $hb->puts('history.forward();');
    $hb->puts('</script>');

    $hb->puts('<script src="/js/jquery.min.js" type="text/javascript"></script>');
    $hb->puts('<script src="/js/common.js" type="text/javascript"></script>');
    $hb->puts('<link href="/css/normalize.css" rel="stylesheet" type="text/css" media="all">');
    $hb->puts('<link href="/css/style.css" rel="stylesheet" type="text/css" media="print, screen and (min-width: 768px)">');
    $hb->puts('<link href="/css/style_sp.css" rel="stylesheet" type="text/css" media="screen and (max-width: 767px)">');

    $hb->puts('<!-- GoogleFont, Other -->');
    $hb->puts('<link href="/css/googlefont.css" rel="stylesheet" type="text/css" media="all">');
    $hb->puts('<link href="/css/style_f.css" rel="stylesheet" type="text/css" media="all">');
    $hb->puts('');
    $hb->puts('<title>' . TTL_MAIN . '</title>');
    $hb->puts('<!-- Google Tag Manager -->');
    $hb->puts('<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({"gtm.start":');
    $hb->puts('new Date().getTime(),event:"gtm.js"});var f=d.getElementsByTagName(s)[0],');
    $hb->puts('j=d.createElement(s),dl=l!="dataLayer"?"&l="+l:"";j.async=true;j.src=');
    $hb->puts('"https://www.googletagmanager.com/gtm.js?id="+i+dl;f.parentNode.insertBefore(j,f);');
    $hb->puts('})(window,document,"script","dataLayer","GTM-KW6RQ2");</script>');
    $hb->puts('<!-- End Google Tag Manager -->');

    if ($fJavaScript !== 0) {
        $hb->puts('<script type="text/javascript" src="/js/time.js"></script>');
        if (($fJavaScript & (JSF_SETDISPTIMER | JSF_DISPTIME)) !== 0) {
            $hb->puts('<script type="text/javascript">');
            $hb->puts('var dStartMsec = ' . $dStartMsec . ';');
            $sFunc = (($fJavaScript & JSF_SETDISPTIMER) !== 0) ? 'setDispTimer' : 'dispTime';
            $hb->puts("document.addEventListener('DOMContentLoaded', {$sFunc});");
            $hb->puts('</script>');
        }
    }
    $hb->puts('</head>');
    $hb->puts('');

    $hb->puts('<body>');
    $hb->puts('<!-- Google Tag Manager (noscript) -->');
    $hb->puts('<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KW6RQ2"');
    $hb->puts('height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>');
    $hb->puts('<!-- End Google Tag Manager (noscript) -->');
    $hb->puts('');
    $hb->puts('<header>');
    $hb->puts('<div>');
    $hb->puts('<p class="logo"><a href="https://www.medicaltown.net/"><img src="/img/common/logo.png" alt="OLYMPUS"></a></p>');
    $hb->puts('<h1 class="medical_ttl">メディカルタウン　<br class="pc">' . TTL_MAIN . '</h1>');
    $hb->puts('<div class="group">');
    $hb->puts('<ul>');
    $hb->puts('<li class="group01"><a href="https://www.olympus.co.jp/" target="_blank">企業情報</a></li>');
    $hb->puts('<li class="group02"><a href="https://www.olympus-global.com/directory/" target="_blank">Global Network</a></li>');
    $hb->puts('</ul>');
    $hb->puts('</div>');
    $hb->puts('</div>');
    $hb->puts('</header>');
    $hb->puts('');

    // ここまで共通、ここからページ毎の本体
    switch ($ixMode) {
    case HTT_HOME:
        $sUserName = getUserName();
        $hb->puts('<div class="contents lesson-region">');
        $hb->puts('<section class="head-section">');
        $hb->puts('<h1>' . TTL_MAIN . '</h1>');
        $hb->puts('<h2>' . TTL_SUB . '</h2>');
        $hb->puts('<div class="user-region">');
        $hb->puts('ようこそ ' . $sUserName . ' さま<br>');
        $hb->puts('<small>※' . $sUserName . 'さまではない場合は<a href="' . PFILE_LOGOUT1 . '">こちら</a>をクリックしてください</small>');
        $hb->puts('</div>');
        $hb->puts('</section>');
        break;
    case HTT_LESSON:
        $hb->puts('<div class="contents lesson-region">');
        break;
    case HTT_EXAM:
        $hb->puts('<div class="contents exam-region">');
        $hb->puts('<div class="container">');
        break;
    default:
        // [HTT_MARATHON]
        $hb->puts('<div class="contents exam-region root-font-size">');
        $hb->puts('<div class="container">');
        break;
    }
}

// フッタを配置
// <引数1> テキストバッファ
function putFooter($hb, $ixMode)
{
    if ($ixMode === HTT_EXAM || $ixMode === HTT_MARATHON) {
        $hb->puts('</div>');    // <div class="container">
    }
    $hb->puts('</div>');        // <div class="contents lesson-region/exam-region">
    $hb->puts('');
    $hb->puts('<footer>');
    $hb->puts('<p class="back_btn"><a href="#page"><span>ページの先頭へ</span></a></p>');
    $hb->puts('<div class="submenu">');
    $hb->puts('<ul>');
    $hb->puts('<li class="info"><a href="https://www.medicaltown.net/support/" target="_blank">お客様サポート</a></li>');
    $hb->puts('<li class="about"><a href="https://www.medicaltown.net/terms/" target="_blank">利用規約</a></li>');
    $hb->puts('<li class="priv"><a href="https://www.olympus.co.jp/products/policy/privacy_management/privacy.html" target="_blank">個人情報の取り扱いについて</a></li>');
    $hb->puts('</ul>');
    $hb->puts('</div>');
    $hb->puts('<p class="copyright">&copy; Olympus Corporation</p>');
    $hb->puts('</footer>');
    $hb->puts('');
    $hb->puts('</body>');
    $hb->puts('</html>');
}

// 「あなたの得点」ページを表示
function putScorePage($hb, $iScore, $iPerfectScore)
{
    putHeader($hb, HTT_EXAM, 0, 0);
    $hb->puts('<div class="container text-center">');
    $hb->puts('<div class="message-region">');
    $hb->puts('<h3>あなたの得点は' . $iPerfectScore . '点満点中、' . $iScore . '点でした。</h3>');
    $hb->puts('<p>「完了」ボタンを押すと、ホーム画面に戻ります。</p><br>');
    putLinkButton($hb, 'btn btn-danger btn-fixed', '完了', PFILE_HOME);
    $hb->puts('</div>');
    $hb->puts('</div>');
    putFooter($hb, HTT_EXAM);
}

// エラーメッセージを配置
function putErrorMessage($hb, $iCode)
{
    putHeader($hb, HTT_EXAM, 0, 0);
    $hb->puts('<div class="exam-region text-center">');
    $hb->puts('<div class="message-region">');
    $hb->puts('<h3>エラーが発生しました</h3>');
    $hb->puts('<!--  エラーコード: ' . $iCode . ' -->');
    $hb->puts('<p>下のボタンを押すと、メディカルタウンに戻ります。</p><br>');
    putLinkButton($hb, 'btn btn-info', 'メディカルタウン', URL_ERRORREF);
    $hb->puts('</div>');
    $hb->puts('</div>');
    putFooter($hb, HTT_EXAM);
}

// 各種パラメータをhiddenで配置
function putHiddenParams($hb, $ixChapter, $ixPhase, $ixStep)
{
    putHiddenInput($hb, FNAME_CHAPTER, $ixChapter);
    putHiddenInput($hb, FNAME_PHASE, $ixPhase);
    putHiddenInput($hb, FNAME_STEP, $ixStep);
}

// コピー不可の画像を配置
function putUncopiableImage($hb, $sFile)
{
    $hb->puts('<div class="img-region"><img class="img-fluid" src="' . $sFile . '" alt="" onContextmenu="alert(\'' . MSG_DONTCOPY . '\');return false"></div>');
}

// ユーザの名前を取得
// <戻り値> ユーザの名前
function getUserName()
{
    return $_SESSION[SKEY_USERNAME];
}

// 受講回数を更新
function updateDataBaseView($db, $ixChapter)
{
    $sViewColumn = getChapterConst($ixChapter, CC_VIEWCOLUMN);
    assertStr($sViewColumn);
    $db->beginTransaction();
    $db->addInt($sViewColumn, 1);
    if ($db->getInt(DBC_LEVEL) === $ixChapter) {
        ++$ixChapter;
        $db->setInt(DBC_LEVEL, $ixChapter);
    }
    $db->commit();
}

// 最高得点/受講回数/コンティニュー位置/レベルを更新
function updateExamDataBase($db, $ixChapter, $ixPhase, $sContColumn, $sScoreColumn)
{
    $sMaxScoreColumn = getChapterConst($ixChapter, CC_MAXSCORECOLUMN);
    assertStr($sMaxScoreColumn);
    $sViewColumn = getChapterConst($ixChapter, CC_VIEWCOLUMN);
    assertStr($sViewColumn);
    $iAddLevel = ($ixChapter !== CI_EXAM2) ? 1 : (CI_SLIDE2 - CI_EXAM2);
    $db->beginTransaction();
    if ($ixPhase === $db->getInt($sContColumn)) {
        $iScore = $db->getInt($sScoreColumn);
        if ($iScore > $db->getInt($sMaxScoreColumn)) {
            $db->setInt($sMaxScoreColumn, $iScore);
        }
        $db->addInt($sViewColumn, 1);
        $db->setInt($sContColumn, 0);
        if ($db->getInt(DBC_LEVEL) === $ixChapter) {
            $db->addInt(DBC_LEVEL, $iAddLevel);
        }
    }
    $db->commit();
}

// ユーザデータを準備
// <戻り値> 成功ならデータベースハンドル、エラー発生ならエラー番号(MRC_～)
function prepareUserData($bAllowDirect)
{
    // セッション開始
    session_start();
    // セッション中でもブラウザのキャッシュを有効に
    header('Expires:-1');
    header('Cache-Control:');
    header('Pragma:');
    if (!OS_WINDOWS) {
        // ユーザキーの有無を確認
        if (!isset($_SESSION[SKEY_USERKEY])) {
            if (!$bAllowDirect) {
                // [セッション情報にユーザキーがないのにindex.php以外が参照された]index.phpへリダイレクト
                $sRedirect = PFILE_HOME;
            } elseif (!isset($_GET[GKEY_TOKEN])) {
                // [トークンがない]APIを呼び出す
                $sRedirect = URL_SSOLOGIN;
            } else {
            	//header('Location: https://www.medicaltown.net/?aaa='.URL_TOKENINFO . $_GET[GKEY_TOKEN]);
            	//exit;
            	
            	$url = URL_TOKENINFO . $_GET[GKEY_TOKEN];
            	
            	//cURLセッションを初期化する
				$ch = curl_init();

				//URLとオプションを指定する
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				//URLの情報を取得する
				$json =  curl_exec($ch);
            	
				//セッションを終了する
				curl_close($ch);
            	
                // jsonを取得
                //$json = file_get_contents(URL_TOKENINFO . $_GET[GKEY_TOKEN]);
            	
           	
                // デコード
                $amInfo = json_decode($json, TRUE);
                // キーの有無をチェック
                if ($amInfo === NULL || !isset($amInfo[IKEY_USERKEY]) || !isset($amInfo[IKEY_EXPTIME])) {
                    return MRC_KEYINVALID;
                }
                // ユーザキーが16進数文字列かどうかチェック
                if (!ctype_xdigit($amInfo[IKEY_USERKEY])) {
                    return MRC_KEYINVALID;
                }
                // 返ってきた時刻が過去ならエラー
                $timeToken = new DateTime($amInfo[IKEY_EXPTIME]);
                $timeNow = new DateTime();
                if ($timeToken < $timeNow) {
                    return MRC_TOKENOLD;
                }
                $sUserName = $amInfo[IKEY_USERNAME];
                if (empty($sUserName)) {
                    $sUserName = DEF_USERNAME;
                }
                $_SESSION[SKEY_USERNAME] = $sUserName;
                $_SESSION[SKEY_USERKEY] = $amInfo[IKEY_USERKEY];
                // ログイン処理が完了したのでクエリ文字列をクリア
                $sRedirect = PFILE_HOME;
            }
            // リダイレクトして終了
            header('Location: ' . $sRedirect);
            exit;
        }
        // ユーザキーを取得
        $sUserKey = $_SESSION[SKEY_USERKEY];
    } else {
        $sUserKey = 'testuserkey';
        $_SESSION[SKEY_USERNAME] = DEF_USERNAME;
    }
    // データベースアクセスを準備
    $db = new CDataBase(DBP_HOST, DBP_DBNAME, DBP_ACCOUNT, DBP_PASSWORD);
    // テーブルを選択
    $db->selectTable(DBP_TABLE, DBC_USERKEY);
    // データベースのレコードを選択
    if (!$db->selectRecord($sUserKey)) {
        // [ユーザレコードが存在しない]レコードを新規作成
        $db->createRecord($sUserKey);
        $db->selectRecord($sUserKey);
        // レベルを1に
        $db->setInt(DBC_LEVEL, 1);
    }
    return $db;
}

?>
