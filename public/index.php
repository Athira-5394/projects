<?php

// ホーム



// ■■■■■■■■■■■■■■■■
// ■        外部ファイル        ■
// ■■■■■■■■■■■■■■■■

// 共通処理
require __DIR__ . '/common.php';



// ■■■■■■■■■■■■■■■■
// ■          内部関数          ■
// ■■■■■■■■■■■■■■■■

// チャプタ毎の[Start][Continue]ボタン・受講回数・最高得点などを配置
function putChapterCtrls($hb, $db, $ixChapter, $ixLevel, $sFile)
{
    $sCont = getChapterConst($ixChapter, CC_CONTCOLUMN);
    $sTitle = getChapterConst($ixChapter, CC_NAME);
    $bVideo = (CI_VIDEO11 <= $ixChapter && $ixChapter <= CI_VIDEO26);
    if ($bVideo) {
        // [動画]
        $sTitle .= '（' . getVideoConst($ixChapter, VC_TIME) . '）';
    }
    $hb->puts('<form action="' . $sFile . '" method="post">');
    putHiddenInput($hb, FNAME_CHAPTER, $ixChapter);
    $hb->puts('<input id="ID' . $ixChapter . '" type="hidden" name="' . FNAME_TIME . '" value="0">');
    // タイトル
    $hb->puts('<div class="menu-unit">');
    $hb->puts('<div class="menu-unit-subtitle">' . $sTitle . '</div>');
    $hb->puts('<div class="menu-unit-btn">');
    // ボタン
    if ($ixChapter === CI_EXAM1) {
        $bEnabled = ($ixLevel === $ixChapter);
    } else {
        $bEnabled = ($ixLevel >= $ixChapter);
    }
    $sView = getChapterConst($ixChapter, CC_VIEWCOLUMN);
    if ($sCont === NULL) {
        // [動画/解答]
        $sButton = $bVideo ? '開始' : '解答';
        if ($bEnabled) {
            putSubmitButton($hb, 'button-f', $sButton, FBTN_START);
        } else {
            putDisabledButton($hb, 'button-f', $sButton);
        }
    } else {
        // [テスト/マラソン/スライド]
        $bExam = ($ixChapter === CI_EXAM1 || CI_EXAM2 <= $ixChapter && $ixChapter <= CI_EXAM2A);
        $nCont = $db->getInt($sCont);
        $sLeft = 'button-f button-pc-left button-sp-upper';
        $hb->puts('<div>');
        if ($bEnabled && $nCont === 0) {
            if ($bExam) {
                putClickEventButton($hb, $sLeft, '開始', FBTN_START, "setStartMsec('ID" . $ixChapter . "')");
            } else {
                putSubmitButton($hb, $sLeft, '開始', FBTN_START);
            }
        } else {
            putDisabledButton($hb, $sLeft, '開始');
        }
        $hb->puts('</div>');
        $sRight = 'button-f button-pc-right button-sp-lower';
        $hb->puts('<div>');
        if ($bEnabled && $nCont !== 0) {
            if ($bExam) {
                putClickEventButton($hb, $sRight, '続行', FBTN_CONTINUE, "setStartMsec('ID" . $ixChapter . "')");
            } else {
                putSubmitButton($hb, $sRight, '続行', FBTN_CONTINUE);
            }
        } else {
            putDisabledButton($hb, $sRight, '続行');
        }
        $hb->puts('</div>');
    }
    $hb->puts('</div>');
    // 受講回数など
    $sCaption = '';
    if ($sView !== NULL) {
        $nView = $db->getInt($sView);
        if ($nView === 0) {
            if ($sCont === NULL || $nCont === 0) {
                $sCaption = '未受講';
            } else {
                $sCaption = '未完了';
            }
        } else {
            if ($nView > 999) {
                $nView = 999;
            }
            $sMaxScore = getChapterConst($ixChapter, CC_MAXSCORECOLUMN);
            $sCaption = '受講回数<span class="text-num">' . $nView . '</span>回';
            if ($sMaxScore !== NULL) {
                $iMaxScore = $db->getInt($sMaxScore);
                if ($ixChapter === CI_EXAM1) {
                    $sCaption = '得点<span class="text-num">' . $iMaxScore . '</span>点（受講済み）';
                } else {
                    $sCaption .= '／最高得点<span class="text-num">' . $iMaxScore . '</span>点';
                }
            }
        }
    }
    $hb->puts('<div class="menu-unit-attendance">' . $sCaption . '</div>');
    $hb->puts('</div>');
    $hb->puts('</form>');
}

// メイン関数
function Home_main($hb)
{
    // ユーザデータを準備
    $db = prepareUserData(TRUE);
    if (!is_object($db)) {
        // エラーコード(MRC_～)を返す
        assertInt($db);
        return $db;
    }

    // ページを作成
    putHeader($hb, HTT_HOME, JSF_SETSTARTMSEC, 0);
    // 「初めに」
    $hb->puts('<section class="part-section text-center">');
    $hb->puts('<h5>' . getPartConst(PI_PREFACE, PC_CAPTION) . '</h5>');
    $hb->puts('（ここを閲覧しないと次に進めません）');
    $hb->puts('<form action="' . getPartConst(PI_PREFACE, PC_PHPFILE) . '" method="post">');
    putSubmitButton($hb, 'button-first', 'Click', FBTN_START);
    $hb->puts('</form>');
    $hb->puts('</section>');
    // 「初めに」を見ていなければ以下のメニューは表示しない
    $ixLevel = $db->getInt(DBC_LEVEL);
    if ($ixLevel > CI_PREFACE) {
        // 各パートのメニューを表示
        for ($ixPart = PI_EXAM1; $ixPart <= PI_SLIDE; ++$ixPart) {
            $ixFirstChapter = getPartConst($ixPart, PC_FIRSTCHAPTER);
            $sDisabled = ($ixLevel >= $ixFirstChapter) ? '' : ' disabled';
            $hb->puts('<section class="part-section text-center' . $sDisabled .'">');
            $hb->puts('<h5>' . getPartConst($ixPart, PC_CAPTION) . '</h5>');
            if ($ixPart === PI_SLIDE) {
                $hb->puts('※上記の全パートを終えた方へ<br>教材のスライド版です。自由に閲覧可能です。');
            }
            $ixLastChapter = getPartConst($ixPart, PC_LASTCHAPTER);
            $sFile = getPartConst($ixPart, PC_PHPFILE);
            for ($ixChapter = $ixFirstChapter; $ixChapter <= $ixLastChapter; ++$ixChapter) {
                putChapterCtrls($hb, $db, $ixChapter, $ixLevel, $sFile);
            }
            $hb->puts('</section>');
        }
    }
    putFooter($hb, HTT_HOME);

    return MRC_OK;
}



// ■■■■■■■■■■■■■■■■
// ■         メイン処理         ■
// ■■■■■■■■■■■■■■■■

startMain('Home_main', 'putErrorMessage');

?>
