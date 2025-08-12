<?php

// 基本ライブラリの処理



// ■■■■■■■■■■■■■■■■
// ■          外部定数          ■
// ■■■■■■■■■■■■■■■■

// OSを表す定数
define('OS_LINUX'  , PHP_OS == 'Linux');
define('OS_WINDOWS', PHP_OS == 'WINNT' || PHP_OS == 'WIN32');

// main()の戻り値
const MRC_OK     = 0;        // 正常終了
const MRC_SYSERR = 1;        // 例外に変換されたシステムエラー
const MRC_APPERR = 2;        // main()内で発生したアプリケーションエラー(以降の番号は自由に使える)



// ■■■■■■■■■■■■■■■■
// ■        外部ファイル        ■
// ■■■■■■■■■■■■■■■■

if (F_DEBUG) {
    require __DIR__ . '/debug.php';
}



// ■■■■■■■■■■■■■■■■
// ■  デバッグおよびエラー処理  ■
// ■■■■■■■■■■■■■■■■

if (!F_DEBUG) {
    // アサーション関連はスルー
    function assertCond($b) {}
    function assertBool($b) {}
    function assertInt($i) {}
    function assertRange($i, $iMin, $iMax) {}
    function assertIndex($ix, $nCount) {}
    function assertStr($s) {}
    function assertArray($a) {}
    function assertArray1($a, $nArray) {}
    function assertArray2($a, $nColumn, $nRow) {}
    function assertArray2Column($a, $nColumn) {}
    // エラー情報を出力
    function outputErrorInfo($hb, $iError, $sMsg, $sFile, $iLine, $aTrace, $sErrorFunc)
    {
        // エラー情報をログファイルに記録
        file_put_contents(LFILE_SYSERROR, sprintf('[%s] (%d) %s file:%s line:%s' . PHP_EOL, date('Y-m-d H:i:s'), $iError, $sMsg, $sFile, $iLine), FILE_APPEND | LOCK_EX);
        // エラーページを配置
        $hb->clear();
        $sErrorFunc($hb, MRC_SYSERR);
    }
}



// ■■■■■■■■■■■■■■■■
// ■  htmlのバッファリング処理  ■
// ■■■■■■■■■■■■■■■■

// [クラス] htmlページをバッファリングして送信
// <説明> htmlの送信方法に融通性を持たせる。
//   デバッグモード時…クラス内バッファに溜める(直接 echo が使えないのは不便なので)
//   リリースモード時…ob_start()/ob_get_contents() で実装

// [コンストラクタ] バッファリングを開始
// new CHtmlBuf()

// [メソッド] 文字列をバッファに追加
// put(string $sHtml): void

// [メソッド] 文字列と改行をバッファに追加
// puts(string $sHtml): void

// [メソッド] バッファリングした文字列を送信
// send(): void

// [メソッド] バッファリングした文字列をクリア
// clear(): void

if (!F_DEBUG) {
    class CHtmlBuf
    {
        function __construct()
        {
            ob_start();
        }
        function put($sHtml)
        {
            echo $sHtml;
        }
        function puts($sHtml)
        {
            echo $sHtml, PHP_EOL;
        }
        function send()
        {
            $sAll = ob_get_contents();
            ob_end_clean();
            echo $sAll;
        }
        function clear()
        {
            ob_end_clean();
        }
    }
}



// ■■■■■■■■■■■■■■■■
// ■          共通関数          ■
// ■■■■■■■■■■■■■■■■

// リンク用のボタンを配置
function putLinkButton($hb, $sClass, $sCaption, $sLink)
{
    // type="button" がなければ "submit" として処理されてしまうことに注意
    $hb->puts('<button class="' . $sClass . '" type="button" onclick="location.href=\'' . $sLink . '\'">' . $sCaption . '</button>');
}

// 送信用のボタンを配置
function putSubmitButton($hb, $sClass, $sCaption, $sValue)
{
    $hb->puts('<button class="' . $sClass . '" type="submit" name="' . FNAME_BUTTON . '" value="' . $sValue . '">' . $sCaption . '</button>');
}

// クリックイベントを持つボタンを配置
function putClickEventButton($hb, $sClass, $sCaption, $sValue, $sClickFunc)
{
    $hb->puts('<button class="' . $sClass . '" type="submit" name="' . FNAME_BUTTON . '" value="' . $sValue . '" onclick="' . $sClickFunc . '">' . $sCaption . '</button>');
}

// 使用不可のボタンを配置
function putDisabledButton($hb, $sClass, $sCaption)
{
    $hb->puts('<button class="' . $sClass . '" disabled="disabled">' . $sCaption . '</button>');
}

// inputタグをhiddenで配置
function putHiddenInput($hb, $sName, $sValue)
{
    $hb->puts('<input type="hidden" name="' . $sName . '" value="' . $sValue . '">');
}

// postされたformデータからname属性に割り当てられた文字列を取得
// <引数1> name属性
// <戻り値> 文字列(存在しなければNULL)
function getFormStr($sName)
{
    return filter_input(INPUT_POST, $sName);
}

// postされたformデータから数値を取得
// <引数1> name属性
// <戻り値> 取得した数値(失敗ならNULL)
function getFormInt($sName)
{
    if (isset($_POST[$sName])) {
        $i = filter_var($_POST[$sName], FILTER_VALIDATE_INT);
        if ($i !== FALSE) {
            return $i;
        }
    }
    return NULL;
}

// postされたformデータから範囲指定付きで数値を取得
// <引数1> name属性
// <引数2> 最小値
// <引数3> 最大値
// <戻り値> 取得した数値(失敗ならNULL)
function getFormIntRange($sName, $iMin, $iMax)
{
    assertInt($iMin);
    assertInt($iMax);
    assertCond($iMin <= $iMax);

    if (isset($_POST[$sName])) {
        $i = filter_var($_POST[$sName], FILTER_VALIDATE_INT);
        if ($i !== FALSE && $iMin <= $i && $i <= $iMax) {
            return $i;
        }
    }
    return NULL;
}

// postされたformデータからインデックスを取得
// <引数1> name属性
// <引数2> インデックスの個数(インデックスの最大値+1)
// <戻り値> 取得したインデックス(失敗ならNULL)
function getFormIndex($sName, $nIndex)
{
    assertCond(is_int($nIndex) && 0 < $nIndex);

    if (isset($_POST[$sName])) {
        $i = filter_var($_POST[$sName], FILTER_VALIDATE_INT);
        if ($i !== FALSE && 0 <= $i && $i < $nIndex) {
            return $i;
        }
    }
    return NULL;
}

// postされたformデータからname属性に割り当てられたラジオボタンの選択状態を取得
// <引数1> name属性から[]を除いた本体
// <引数2> name属性のインデックス番号
// <引数3> ラジオボタンの個数
// <戻り値> -1(エラー)/0(未選択)/1～n(選択)
function getFormRadioState($sNameBody, $ixName, $nRadio)
{
    assertCond(is_int($ixName) && $ixName >= 0);
    assertCond(is_int($nRadio) && $nRadio > 0);

    if (!isset($_POST[$sNameBody][$ixName])) {
        return 0;
    }
    $i = filter_var($_POST[$sNameBody][$ixName], FILTER_VALIDATE_INT);
    if ($i !== FALSE && 0 <= $i && $i <= $nRadio) {
        return $i;
    }
    return -1;
}

// 一般的なエラー発生時に呼ばれるコールバック関数
function errorHandler($iError, $sError, $sErrorFile, $iErrorLine)
{
    // 例外に変換
    throw new ErrorException($sError, $iError, 0, $sErrorFile, $iErrorLine);
}

// 致命的なエラー発生時に呼ばれるコールバック関数
function shutdownHandler($asd)
{
    $aError = error_get_last();
    if ($aError !== NULL) {
        // debug_backtrace() は使えない
        outputErrorInfo($asd[0], $aError['type'], $aError['message'], $aError['file'], $aError['line'], NULL, $asd[1]);
        // 最後に送信が必要
        $asd[0]->send();
    }
}

// メイン関数を開始
// <引数1> メイン関数の文字列
// <引数2> エラー出力関数の文字列
// いずれの関数も CHtmlBuf オブジェクトを引数とする。
function startMain($sMainFunc, $sErrorFunc)
{
    // hmtlのバッファリングを開始
    $hb = new CHtmlBuf();
    // タイムゾーンを設定(エラーログファイルに time() を使っている)
    date_default_timezone_set('Asia/Tokyo');
    // エラー表示を抑制
    ini_set('display_errors', 'Off');
    // エラーハンドラを設定
    set_error_handler('errorHandler', E_ALL);
    register_shutdown_function('shutdownHandler', [$hb, $sErrorFunc]);
    // main関数を実行
    try {
        $iCode = $sMainFunc($hb);
        if ($iCode !== MRC_OK) {
            $sErrorFunc($hb, $iCode);
        }
    } catch (Exception $e) {
        // [例外もしくはエラーをキャッチ]
        outputErrorInfo($hb, $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace(), $sErrorFunc);
    }
    // 送信
    $hb->send();
}

?>
