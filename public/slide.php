<?php

// 「教材1(入門編講義)スライド」「教材2(応用編講義)スライド」ページ

// フォーム
//   FNAME_CHAPTER  チャプタ  CI_SLIDE1/CI_SLIDE2
//   FNAME_PHASE    フェーズ  0～N_SLIDE-1…スライド1～スライドN_SLIDEを表示
//                            N_SLIDE…完了処理



// ■■■■■■■■■■■■■■■■
// ■        外部ファイル        ■
// ■■■■■■■■■■■■■■■■

// 共通処理
require __DIR__ . '/common.php';



// ■■■■■■■■■■■■■■■■
// ■          内部定数          ■
// ■■■■■■■■■■■■■■■■

// $amInfoのインデックス
const SLI_NSLIDE = 0;    // スライドの個数
const SLI_DIR    = 1;    // ディレクトリ
const SLI_COUNT  = 2;



// ■■■■■■■■■■■■■■■■
// ■          内部関数          ■
// ■■■■■■■■■■■■■■■■

// メイン関数
function Slide_main($hb)
{
    static $aaTable = [
        [N_SLIDE1, DIR_SLIDE1],
        [N_SLIDE2, DIR_SLIDE2],
    ];
    assertArray2($aaTable, SLI_COUNT, 2);

    // ユーザデータを準備
    $db = prepareUserData(FALSE);
    if (!is_object($db)) {
        // エラーコード(MRC_～)を返す
        assertInt($db);
        return $db;
    }

    // リクエストの種類
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // [POST]
        $ixChapter = getFormIntRange(FNAME_CHAPTER, CI_SLIDE1, CI_SLIDE2);
        if ($ixChapter === NULL) {
            return MRC_FORMINVALID;
        }
        if ($db->getInt(DBC_LEVEL) < $ixChapter) {
            return MRC_FORMINVALID;
        }
        $sContColumn = getChapterConst($ixChapter, CC_CONTCOLUMN);
        assertStr($sContColumn);
        $amInfo = $aaTable[$ixChapter - CI_SLIDE1];
        $nSlide = $amInfo[SLI_NSLIDE];
        switch (getFormStr(FNAME_BUTTON)) {
        case FBTN_START:
            // [開始]
            $db->setInt($sContColumn, 0);
            $ixPhase = 0;
            break;
        case FBTN_CONTINUE:
            // [続行]
            $ixPhase = $db->getInt($sContColumn);
            break;
        default:
            // [進む]
            $ixPhase = getFormIndex(FNAME_PHASE, $nSlide);
            if ($ixPhase === NULL) {
                return MRC_FORMINVALID;
            }
            ++$ixPhase;
            if ($ixPhase !== $nSlide) {
                $db->setInt($sContColumn, $ixPhase);
            } else {
                // [完了]
                $db->setInt($sContColumn, 0);
                // 閲覧回数をカウントアップ
                $sViewColumn = getChapterConst($ixChapter, CC_VIEWCOLUMN);
                assertStr($sViewColumn);
                $db->addInt($sViewColumn, 1);
                // ホームへリダイレクト
                header('Location: ' . PFILE_HOME);
                exit;
            }
            break;
        case FBTN_BACKWARD:
            // [戻る]
            $ixPhase = getFormIntRange(FNAME_PHASE, 1, $nSlide + 1);
            if ($ixPhase === NULL) {
                return MRC_FORMINVALID;
            }
            --$ixPhase;
            $db->setInt($sContColumn, $ixPhase);
            break;
        }
        $_SESSION[SKEY_SLIDE] = [$ixChapter, $ixPhase];
    } else {
        // [GET]
        if (!isset($_SESSION[SKEY_SLIDE])) {
            return MRC_PAGEINVALID;
        }
        list($ixChapter, $ixPhase) = $_SESSION[SKEY_SLIDE];
        $amInfo = $aaTable[$ixChapter - CI_SLIDE1];
        $nSlide = $amInfo[SLI_NSLIDE];
    }

    // ページを作成
    $iSlide = $ixPhase + 1;
    putHeader($hb, HTT_EXAM, 0, 0);
    $hb->puts('<h3 class="caption-center">Slide: ' . $iSlide . '/' . $nSlide . '</h3>');
    $hb->puts('<form action="' . PFILE_SLIDE . '" method="post">');
    putHiddenInput($hb, FNAME_CHAPTER, $ixChapter);
    putHiddenInput($hb, FNAME_PHASE, $ixPhase);
    $hb->puts('<div class="button-neat">');
    if ($ixPhase !== 0) {
        putSubmitButton($hb, 'btn btn-primary btn-fixed', '戻る', FBTN_BACKWARD);
    } else {
        $hb->puts('<span class="btn-fixed"></span>');
    }
    putLinkButton($hb, 'btn btn-success btn-fixed', 'ホーム', PFILE_HOME);
    if ($iSlide !== $nSlide) {
        putSubmitButton($hb, 'btn btn-primary btn-fixed', '進む', FBTN_FORWARD);
    } else {
        putSubmitButton($hb, 'btn btn-danger btn-fixed', '完了', FBTN_FORWARD);
    }
    $hb->puts('</div>');
    $hb->puts('</form>');
    putUncopiableImage($hb, $amInfo[SLI_DIR] . sprintf('%03d.jpg', $iSlide));
    putFooter($hb, HTT_EXAM);

    return MRC_OK;
}



// ■■■■■■■■■■■■■■■■
// ■         メイン処理         ■
// ■■■■■■■■■■■■■■■■

startMain('Slide_main', 'putErrorMessage');

?>
