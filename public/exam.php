<?php

// 「40問テスト」ページ

// 画面遷移
//   ixPhase = 0  … Case:1
//     ixStep = 0        … Image:1
//       …
//     ixStep = nImage-1 … Image:n
//     ixStep = nImage   … (TSS_SELECT )所見判定画面
//     ixStep = nImage+1 … (TSS_ERROR  )所見判定エラー画面
//     ixStep = nImage+2 … (TSS_CONFIRM)最終確認画面
//     ixStep = nImage+3 … (TSS_RELAY  )(Case40以外)次の設問へ  ←ここに到達したとき、コンティニュー位置と一致していたらデータベースを更新
//       …
//   ixPhase = 1  … Case:2
//       …
//   ixPhase = 39 … Case:40
//   ixPhase = 40 … 「あなたの得点」画面  ←ここに到達したら集計してデータベースを更新



// ■■■■■■■■■■■■■■■■
// ■        外部ファイル        ■
// ■■■■■■■■■■■■■■■■

// 共通処理
require __DIR__ . '/common.php';



// ■■■■■■■■■■■■■■■■
// ■          内部定数          ■
// ■■■■■■■■■■■■■■■■

// ラジオボタンのインデックス
const TRB_DL    = 0;    // DL
const TRB_V     = 1;    // V
const TRB_S     = 2;    // S
const TRB_GRADE = 3;    // Grade
const TRB_COUNT = 4;

// 文字列変換のインデックス
const TCS_DIAG   = 0;    // Diagnosis
const TCS_HDIAG  = 1;    // Histological diagnosis
const TCS_DL     = 2;    // DL
const TCS_V      = 3;    // V
const TCS_S      = 4;    // S
const TCS_GRADE  = 5;    // Grade
const TCS_COUNT  = 6;

// 40問の症例定数のインデックス
const TCC_NIMAGE = 0;    // イメージの枚数
const TCC_DIAG   = 1;    // Diagnosis
const TCC_HDIAG  = 2;    // Histological diagnosis
const TCC_DL     = 3;    // DL
const TCC_V      = 4;    // V
const TCC_S      = 5;    // S
const TCC_COUNT  = 6;

// DL(Demarcation line)のインデックス
const TDL_NONE     = 0;     // (出題     )無回答
const TDL_INDETERM = 1;     // (出題     )Indeterminate
const TDL_ABSENT   = 2;     // (出題/正解)Absent
const TDL_PRESENT  = 3;     // (出題/正解)Present
const TDL_COUNT    = 4;

// V(Microvascular pattern)のインデックス
const TMV_NONE      = 0;    // (出題     )無回答
const TMV_INDETERM  = 1;    // (出題     )Indeterminate
const TMV_ABSENT    = 2;    // (出題/正解)Absent
const TMV_REGULAR   = 3;    // (出題/正解)Regular
const TMV_IRREGULAR = 4;    // (出題/正解)Irregular
const TMV_COUNT     = 5;

// S(Microsurface pattern)のインデックス
const TMS_NONE      = 0;    // (出題     )無回答
const TMS_INDETERM  = 1;    // (出題     )Indeterminate
const TMS_ABSENT    = 2;    // (出題/正解)Absent
const TMS_REGULAR   = 3;    // (出題/正解)Regular
const TMS_IRREGULAR = 4;    // (出題/正解)Irregular
const TMS_COUNT     = 5;

// Grade分類のインデックス
const TGC_NONE   = 0;    // 無回答
const TGC_GRADE1 = 1;    // Grade1
const TGC_GRADE2 = 2;    // Grade2
const TGC_GRADE3 = 3;    // Grade3
const TGC_GRADE4 = 4;    // Grade4
const TGC_GRADE5 = 5;    // Grade5
const TGC_COUNT  = 6;

// 診断(Diagnosis)のインデックス
const TDN_NONCANCER   = 0;    // 非癌
const TDN_CANCER_PM   = 1;    // 癌/pM
const TDN_CANCER_PSM1 = 2;    // 癌/pSM1
const TDN_CANCER_PSM2 = 3;    // 癌/pSM2
const TDN_COUNT       = 4;

// Histological diagnosis のインデックス
const THD_ADENOMA  = 0;    // 非癌/adenoma
const THD_GASTRI   = 1;    // 非癌/Chronic gastritis
const THD_XANTHOMA = 2;    // 非癌/黄色腫
const THD_TUB1     = 3;    // 癌/tub1
const THD_TUB1TUB2 = 4;    // 癌/tub1>tub2
const THD_TUB1MUC  = 5;    // 癌/tub1>muc>pap
const THD_TUB2TUB1 = 6;    // 癌/tub2>tub1
const THD_TUB2POR1 = 7;    // 癌/tub2>por1
const THD_UNKNOWN  = 8;    // (不明)
const THD_COUNT    = 9;

// 画面のインデックス
const TSS_SELECT  = 0;    // 所見判定画面
const TSS_ERROR   = 1;    // 所見判定エラー画面
const TSS_CONFIRM = 2;    // 最終確認画面
const TSS_RELAY   = 3;    // 次の設問へ
const TSS_COUNT   = 4;

// 所見判定画面におけるエラーの状態
const TSE_OK          = 0;    // エラーなし
const TSE_NOTSELECTED = 1;    // 選択されていないラジオボタンがある
const TSE_INVALID     = 2;    // 選択に矛盾がある



// ■■■■■■■■■■■■■■■■
// ■          内部関数          ■
// ■■■■■■■■■■■■■■■■

// 症例の定数を取得
// <引数1> 症例インデックス(0～39)
// <引数2> 症例定数のインデックス(TCC_～)
// <戻り値> 定数
function getCaseConst($ixCase, $ixCaseConst)
{
    assertIndex($ixCase, MAX_EXAM);
    assertIndex($ixCaseConst, TCC_COUNT);
    //    イメージの枚数
    //       Diagnosis
    //                        Historical diagnosis
    //                                      DL
    //                                                   V
    //                                                                  S
    static $aiTable = [
        [ 7, TDN_CANCER_PM  , THD_TUB1    , TDL_PRESENT, TMV_IRREGULAR, TMS_REGULAR  , ],    // 01??
        [ 7, TDN_CANCER_PM  , THD_TUB1    , TDL_PRESENT, TMV_IRREGULAR, TMS_IRREGULAR, ],    // 02??
        [ 8, TDN_CANCER_PM  , THD_TUB1    , TDL_PRESENT, TMV_ABSENT   , TMS_IRREGULAR, ],    // 03??
        [ 6, TDN_CANCER_PSM2, THD_TUB1TUB2, TDL_PRESENT, TMV_IRREGULAR, TMS_IRREGULAR, ],    // 04??
        [ 5, TDN_NONCANCER  , THD_UNKNOWN , TDL_ABSENT , TMV_REGULAR  , TMS_REGULAR  , ],    // 05??(変更)
        [ 7, TDN_NONCANCER  , THD_GASTRI  , TDL_PRESENT, TMV_REGULAR  , TMS_REGULAR  , ],    // 06??
        [ 6, TDN_CANCER_PM  , THD_TUB1    , TDL_PRESENT, TMV_IRREGULAR, TMS_IRREGULAR, ],    // 07??
        [ 7, TDN_NONCANCER  , THD_GASTRI  , TDL_PRESENT, TMV_REGULAR  , TMS_ABSENT   , ],    // 08??
        [ 5, TDN_CANCER_PM  , THD_TUB1    , TDL_PRESENT, TMV_IRREGULAR, TMS_IRREGULAR, ],    // 09??
        [ 6, TDN_NONCANCER  , THD_GASTRI  , TDL_ABSENT , TMV_REGULAR  , TMS_REGULAR  , ],    // 10??
        [ 7, TDN_CANCER_PSM1, THD_TUB2POR1, TDL_PRESENT, TMV_IRREGULAR, TMS_IRREGULAR, ],    // 11??
        [ 6, TDN_NONCANCER  , THD_UNKNOWN , TDL_ABSENT , TMV_REGULAR  , TMS_REGULAR  , ],    // 12??(変更)
        [ 5, TDN_NONCANCER  , THD_UNKNOWN , TDL_PRESENT, TMV_REGULAR  , TMS_REGULAR  , ],    // 13??(変更)
        [ 5, TDN_NONCANCER  , THD_GASTRI  , TDL_PRESENT, TMV_REGULAR  , TMS_ABSENT   , ],    // 14??
        [ 6, TDN_NONCANCER  , THD_UNKNOWN , TDL_ABSENT , TMV_REGULAR  , TMS_REGULAR  , ],    // 15??(変更)
        [ 6, TDN_CANCER_PM  , THD_TUB1    , TDL_PRESENT, TMV_IRREGULAR, TMS_IRREGULAR, ],    // 16??
        [ 6, TDN_NONCANCER  , THD_GASTRI  , TDL_ABSENT , TMV_REGULAR  , TMS_REGULAR  , ],    // 17??
        [ 5, TDN_CANCER_PM  , THD_UNKNOWN , TDL_PRESENT, TMV_IRREGULAR, TMS_IRREGULAR, ],    // 18??(変更)
        [ 6, TDN_CANCER_PM  , THD_TUB2TUB1, TDL_PRESENT, TMV_ABSENT   , TMS_IRREGULAR, ],    // 19??
        [ 6, TDN_NONCANCER  , THD_UNKNOWN , TDL_PRESENT, TMV_ABSENT   , TMS_REGULAR  , ],    // 20??(変更)
        [ 5, TDN_NONCANCER  , THD_GASTRI  , TDL_PRESENT, TMV_REGULAR  , TMS_REGULAR  , ],    // 21??
        [ 6, TDN_NONCANCER  , THD_GASTRI  , TDL_PRESENT, TMV_ABSENT   , TMS_REGULAR  , ],    // 22??
        [ 6, TDN_CANCER_PM  , THD_TUB1    , TDL_PRESENT, TMV_IRREGULAR, TMS_IRREGULAR, ],    // 23??
        [ 8, TDN_CANCER_PSM2, THD_TUB1MUC , TDL_PRESENT, TMV_ABSENT   , TMS_IRREGULAR, ],    // 24??
        [ 7, TDN_CANCER_PM  , THD_TUB1    , TDL_PRESENT, TMV_IRREGULAR, TMS_IRREGULAR, ],    // 25??
        [ 6, TDN_NONCANCER  , THD_GASTRI  , TDL_PRESENT, TMV_REGULAR  , TMS_REGULAR  , ],    // 26??
        [ 6, TDN_CANCER_PM  , THD_TUB1    , TDL_PRESENT, TMV_IRREGULAR, TMS_IRREGULAR, ],    // 27??
        [ 6, TDN_NONCANCER  , THD_GASTRI  , TDL_PRESENT, TMV_REGULAR  , TMS_REGULAR  , ],    // 28??
        [ 7, TDN_NONCANCER  , THD_UNKNOWN , TDL_ABSENT , TMV_REGULAR  , TMS_REGULAR  , ],    // 29??(変更)
        [ 8, TDN_NONCANCER  , THD_GASTRI  , TDL_ABSENT , TMV_REGULAR  , TMS_REGULAR  , ],    // 30??
        [ 8, TDN_CANCER_PM  , THD_TUB1TUB2, TDL_PRESENT, TMV_IRREGULAR, TMS_IRREGULAR, ],    // 31??
        [ 6, TDN_CANCER_PM  , THD_TUB1TUB2, TDL_PRESENT, TMV_IRREGULAR, TMS_ABSENT   , ],    // 32??
        [ 7, TDN_CANCER_PM  , THD_TUB2TUB1, TDL_PRESENT, TMV_IRREGULAR, TMS_ABSENT   , ],    // 33??
        [ 6, TDN_CANCER_PM  , THD_UNKNOWN , TDL_PRESENT, TMV_ABSENT   , TMS_IRREGULAR, ],    // 34??(変更)
        [ 6, TDN_CANCER_PM  , THD_TUB1    , TDL_PRESENT, TMV_IRREGULAR, TMS_IRREGULAR, ],    // 35??
        [ 6, TDN_CANCER_PM  , THD_TUB1    , TDL_PRESENT, TMV_IRREGULAR, TMS_IRREGULAR, ],    // 36??
        [ 6, TDN_CANCER_PM  , THD_UNKNOWN , TDL_PRESENT, TMV_IRREGULAR, TMS_IRREGULAR, ],    // 37??(変更)
        [ 8, TDN_NONCANCER  , THD_GASTRI  , TDL_ABSENT , TMV_REGULAR  , TMS_REGULAR  , ],    // 38??
        [ 6, TDN_CANCER_PM  , THD_UNKNOWN , TDL_PRESENT, TMV_ABSENT   , TMS_IRREGULAR, ],    // 39??(変更)
        [ 5, TDN_NONCANCER  , THD_UNKNOWN , TDL_PRESENT, TMV_REGULAR  , TMS_REGULAR  , ],    // 40??(変更)
    ];
    assertArray2Column($aiTable, TCC_COUNT);

    return $aiTable[$ixCase][$ixCaseConst];
}

// DL/V/S/診断/HDを表す文字列に変換
// <引数1> 文字列の種類(TCC_DIAG～TCC_GRADE)
// <引数2> TDN_～/THD_～/TDL_～/TMV_～/TMS_～/TGC_～
function classToStr($ixClass, $ixDiag)
{
    assertIndex($ixClass, TCS_COUNT);
    assertIndex($ixDiag, THD_COUNT);
    static $asTable = [
        [ '非癌', '癌（pM）', '癌（pSM1）', '癌（pSM2）', ],
        [ 'adenoma', 'Chronic gastritis', '黄色腫', 'tub1', 'tub1 > tub2', 'tub1 > muc > pap', 'tub2 > tub1', 'tub2 > por1', '-', ],
        [ '（無回答）', 'Indeterminate', 'Absent', 'Present', ],
        [ '（無回答）', 'Indeterminate', 'Absent', 'Regular', 'Irregular', ],
        [ '（無回答）', 'Indeterminate', 'Absent', 'Regular', 'Irregular', ],
        [ '（無回答）', 'Grade1', 'Grade2', 'Grade3', 'Grade4', 'Grade5', ],
    ];
    assertArray1($asTable, TCS_COUNT);
    assertArray1($asTable[TCS_DIAG ], TDN_COUNT);
    assertArray1($asTable[TCS_HDIAG], THD_COUNT);
    assertArray1($asTable[TCS_DL   ], TDL_COUNT);
    assertArray1($asTable[TCS_V    ], TMV_COUNT);
    assertArray1($asTable[TCS_S    ], TMS_COUNT);
    assertArray1($asTable[TCS_GRADE], TGC_COUNT);

    return $asTable[$ixClass][$ixDiag];
}

// ラジオボタンの回答を取得
function getSelection($aTable)
{
    for ($i = 0; $i < TRB_COUNT; ++$i) {
        $iState = getFormRadioState(FNAME_SELECT, $i, count($aTable[$i]));
        if ($iState < 0) {
            return MRC_FORMINVALID;
        }
        $aixSel[] = $iState;
    }
    return $aixSel;
}

// すべての回答が未回答でないか？
// <戻り値> 回答されていればTRUE、未回答のものがあればFALSE
function isSelectionAny($aixSel)
{
    assertArray1($aixSel, TRB_COUNT);

    return $aixSel[TRB_DL] !== TDL_NONE && $aixSel[TRB_V] !== TMV_NONE && $aixSel[TRB_S] !== TMS_NONE && $aixSel[TRB_GRADE] !== TGC_NONE;
}

// すべての回答に矛盾がないか？
// <戻り値> 矛盾がなければTRUE、矛盾があればFALSE
// 対応関係一覧表
//   DL:pre
//                S:reg/abs  S:ind  S:irr
//     V:reg/abs      1        3      5
//     V:ind          3        3      5
//     V:irr          5        5      5
//   DL:abs  1
//   DL:ind  3
function isSelectionValid($aixSel)
{
    assertArray1($aixSel, TRB_COUNT);

    // GRADE2はGRADE1に、GRADE4はGRADE5にまとめる
    $iGrade = $aixSel[TRB_GRADE];
    switch ($iGrade) {
    case TGC_GRADE2:
        $iGrade = TGC_GRADE1;
        break;
    case TGC_GRADE4:
        $iGrade = TGC_GRADE5;
        break;
    }
    switch ($aixSel[TRB_DL]) {
    case TDL_PRESENT:
        if ($aixSel[TRB_V] === TMV_IRREGULAR || $aixSel[TRB_S] === TMS_IRREGULAR) {
            $bValid = ($iGrade === TGC_GRADE5);
        } elseif ($aixSel[TRB_V] === TMV_INDETERM || $aixSel[TRB_S] === TMS_INDETERM) {
            $bValid = ($iGrade === TGC_GRADE3);
        } else {
            $bValid = ($iGrade === TGC_GRADE1);
        }
        break;
    case TDL_ABSENT:
        $bValid = ($iGrade === TGC_GRADE1);
        break;
    default:
        // [TDL_INDETERM]
        $bValid = ($iGrade === TGC_GRADE3);
        break;
    }
    return $bValid;
}

// 回答(癌/非癌のみ判定)は正解か？
// <戻り値> 正解ならTRUE、不正解ならFALSE
function isSelectionCorrect($ixCase, $aixSel)
{
    assertIndex($ixCase, N_EXAM);
    assertArray1($aixSel, TRB_COUNT);

    return ($aixSel[TRB_GRADE] === TGC_GRADE4 || $aixSel[TRB_GRADE] === TGC_GRADE5) === (getCaseConst($ixCase, TCC_DIAG) !== TDN_NONCANCER);
}

// ラジオボタンを配置
function putRadioButtons($hb, $sTitle, $ixClass, $nRow, $aixButton, $ixChecked)
{
    assertIndex($ixClass, TRB_COUNT);
    assertCond(is_array($aixButton));

    if ($sTitle !== NULL) {
        $hb->puts('<tr><th>' . $sTitle . '</th>');
    } else {
        $hb->puts('<tr>');
    }
    for ($i = 0; $i < $nRow; ++$i) {
        if ($i < count($aixButton)) {
            $ixButton = $aixButton[$i];
            $sChecked = ($ixChecked !== $ixButton) ? '' : ' checked="checked"';
            $hb->puts('<td><label><input type="radio" name="' . FNAME_SELECT . '[' . $ixClass . ']" value="' . $ixButton . '" ' . $sChecked . '>' . classToStr(TCS_DL + $ixClass, $ixButton) . '</label></td>');
        } else {
            $hb->puts('<td></td>');
        }
    }
    $hb->puts('</tr>');
}

// カラム2のテーブルを配置
function putTable2($hb, $sTitle, $sItem)
{
    $hb->puts('<tr><th>' . $sTitle . '</th><td>' . $sItem . '</td></tr>');
}

// カラム3のテーブルを配置
function putTable3($hb, $sTitle, $sItem1, $sItem2)
{
    $hb->puts('<tr><th>' . $sTitle . '</th><td>' . $sItem1 . '</td><td>' . $sItem2 . '</td></tr>');
}

// 選択されていないときに警告を配置
function putSelWarning($hb, $bSel, $sTitle)
{
    if (!$bSel) {
        $hb->puts('<p><span class="text-danger">' . $sTitle . 'が選択されていません。</span></p>');
    }
}

// タイトル部分を配置
function putCaptions($hb, $sTitle, $iCase)
{
    $hb->puts('<h4 class="caption-neat">');
    $hb->puts('<span>Case: ' . $iCase . '/' . N_EXAM . '</span>');
//    $hb->puts('<span>経過時間: <span id="PassedTime"></span></span>');
    $hb->puts('<span style="color: #000;">経過時間: <span style="color: #000;" id="PassedTime"></span></span>');
    $hb->puts('</h4>');
    $hb->puts('<h3 class="caption-center">' . $sTitle . '</h3>');
}

// ラジオボタンの選択状態をhiddenで配置
function putHiddenSelections($hb, $aixSel)
{
    foreach ($aixSel as $ix) {
        putHiddenInput($hb, FNAME_SELECT . '[]', $ix);
    }
}

// 解答表を配置
function putAnswerTable($hb, $db, $ixChapter)
{
    $sContColumn = getChapterConst($ixChapter, CC_CONTCOLUMN);
    assertStr($sContColumn);
    $ixMax = $db->getInt($sContColumn);
    if ($ixMax === 0) {
        $ixMax = N_EXAM;
    }
    $sExam = ($ixChapter === CI_EXAM1) ? DBC_PRE : DBC_POST;
    $hb->puts('<table class="table table-bordered">');
    $hb->puts('<tr><th>問題番号</th><th>DL</th><th>V</th><th>S</th><th>診断</th></tr>');
    for ($i = 0; $i < MAX_EXAM; ++$i) {
        $ixDl   = getCaseConst($i, TCC_DL  );
        $ixV    = getCaseConst($i, TCC_V   );
        $ixS    = getCaseConst($i, TCC_S   );
        $ixDiag = getCaseConst($i, TCC_DIAG);
        $sCase = strval($i + 1);
        $sDl   = classToStr(TCS_DL, $ixDl);
        $sV    = classToStr(TCS_V , $ixV );
        $sS    = classToStr(TCS_S , $ixS );
        $sDiag = ($ixDiag === TDN_NONCANCER) ? '非癌' : '癌';
        if ($i < $ixMax) {
            $sQuestion = $sExam . $sCase . '_';
            if ($db->getInt($sQuestion . 'dl') !== $ixDl) {
                $sDl = '<span class="wrong-answer">' . $sDl . '</span>';
            }
            if ($db->getInt($sQuestion . 'v') !== $ixV) {
                $sV = '<span class="wrong-answer">' . $sV . '</span>';
            }
            if ($db->getInt($sQuestion . 's') !== $ixS) {
                $sS = '<span class="wrong-answer">' . $sS . '</span>';
            }
            $ixGrade = $db->getInt($sQuestion . 'grade');
            if ($ixDiag === TDN_NONCANCER && $ixGrade > TGC_GRADE3 || $ixDiag !== TDN_NONCANCER && $ixGrade <= TGC_GRADE3) {
                $sDiag = '<span class="wrong-answer">' . $sDiag . '</span>';
            }
        } else {
            $sCase = '<span class="unreached-answer">' . $sCase . '</span>';
            $sDl   = '<span class="unreached-answer">' . $sDl   . '</span>';
            $sV    = '<span class="unreached-answer">' . $sV    . '</span>';
            $sS    = '<span class="unreached-answer">' . $sS    . '</span>';
            $sDiag = '<span class="unreached-answer">' . $sDiag . '</span>';
        }
        $hb->puts('<tr><th>' . $sCase . '</th><td>' . $sDl . '</td><td>' . $sV . '</td><td>' . $sS . '</td><td>' . $sDiag . '</td></tr>');
    }
    $hb->puts('</table>');
}

// メイン関数
function Exam_main($hb)
{
    // ラジオボタンの情報テーブル
    static $aaRadioTable = [
        [TDL_PRESENT, TDL_ABSENT   , TDL_INDETERM,                          ],
        [TMV_REGULAR, TMV_IRREGULAR, TMV_ABSENT  , TMV_INDETERM,            ],
        [TMS_REGULAR, TMS_IRREGULAR, TMS_ABSENT  , TMS_INDETERM,            ],
        [TGC_GRADE1 , TGC_GRADE2   , TGC_GRADE3  , TGC_GRADE4  , TGC_GRADE5,],
    ];
    assertArray1($aaRadioTable, TRB_COUNT);

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
        $ixChapter = getFormInt(FNAME_CHAPTER);
        if ($ixChapter === NULL || $ixChapter !== CI_EXAM1 && $ixChapter < CI_EXAM2 && CI_EXAM2A < $ixChapter) {
            return MRC_FORMINVALID;
        }
        if ($db->getInt(DBC_LEVEL) < $ixChapter) {
            return MRC_FORMINVALID;
        }
        switch ($ixChapter) {
        default:
            // [CI_EXAM1/CI_EXAM2]
            $sContColumn = getChapterConst($ixChapter, CC_CONTCOLUMN);
            assertStr($sContColumn);
            $sScoreColumn = getChapterConst($ixChapter, CC_SCORECOLUMN);
            assertStr($sScoreColumn);
            $ixStep = 0;
            $dStartMsec = 0;
            $sButton = getFormStr(FNAME_BUTTON);
            switch ($sButton) {
            case FBTN_START:
                // [開始]
                $db->setInt($sScoreColumn, 0);
                $db->setInt($sContColumn, 0);
                $ixPhase = 0;
                $aixSel = array_fill(0, count($aaRadioTable), 0);
                break;
            case FBTN_CONTINUE:
                // [続行]
                $ixPhase = $db->getInt($sContColumn);
                $aixSel = array_fill(0, count($aaRadioTable), 0);
                break;
            default:
                // [進む/戻る]
                $ixPhase = getFormIndex(FNAME_PHASE, N_EXAM);
                if ($ixPhase === NULL) {
                    return MRC_FORMINVALID;
                }
                $nImage = getCaseConst($ixPhase, TCC_NIMAGE);
                switch ($sButton) {
                default:
                    // [進む]
                    $ixStep = getFormIndex(FNAME_STEP, $nImage + TSS_COUNT);
                    if ($ixStep === NULL) {
                        return MRC_FORMINVALID;
                    }
                    $ixCmd = $ixStep - $nImage;
                    if ($ixCmd !== TSS_RELAY) {
                        $aixSel = getSelection($aaRadioTable);
                    }
                    switch ($ixCmd) {
                    default:
                        ++$ixStep;
                        break;
                    case TSS_SELECT:
                    case TSS_ERROR:
                        $ixStep = $nImage + TSS_ERROR;
                        if (isSelectionAny($aixSel) && isSelectionValid($aixSel)) {
                            ++$ixStep;
                        }
                        break;
                    case TSS_CONFIRM:
                        // 妥当なコンティニュー位置だったときのみデータベースを更新
                        $db->beginTransaction();
                        if ($ixPhase === $db->getInt($sContColumn)) {
                            $sExam = ($ixChapter === CI_EXAM1) ? DBC_PRE : DBC_POST;
                            $sQuestion = $sExam . ($ixPhase + 1) . '_';
                            $db->setInt($sQuestion . 'dl'   , $aixSel[TRB_DL   ]);
                            $db->setInt($sQuestion . 'v'    , $aixSel[TRB_V    ]);
                            $db->setInt($sQuestion . 's'    , $aixSel[TRB_S    ]);
                            $db->setInt($sQuestion . 'grade', $aixSel[TRB_GRADE]);
                            if (isSelectionCorrect($ixPhase, $aixSel)) {
                                $db->addInt($sScoreColumn, 1);
                            }
                            $db->addInt($sContColumn, 1);
                        }
                        $db->commit();
                        ++$ixStep;         // TSS_RELAY
                        if ($ixPhase === N_EXAM - 1) {
                            ++$ixPhase;    // N_EXAM
                        }
                        break;
                    case TSS_RELAY:
                        $ixPhase = $db->getInt($sContColumn);    // コンティニュー位置を上書き
                        $ixStep = 0;
                        $aixSel = array_fill(0, count($aaRadioTable), 0);
                        break;
                    }
                    break;
                case FBTN_BACKWARD:
                    // [戻る]
                    $ixStep = getFormIntRange(FNAME_STEP, 1, $nImage + TSS_CONFIRM);
                    if ($ixStep === NULL) {
                        return MRC_FORMINVALID;
                    }
                    --$ixStep;
                    if ($ixStep >= $nImage + TSS_SELECT) {
                        --$ixStep;
                    }
                    $aixSel = getSelection($aaRadioTable);
                    break;
                }
                break;
            }
            if ($ixPhase !== N_EXAM) {
                // 開始時刻を取得
                $sTime = getFormStr(FNAME_TIME);
                if ($sTime === NULL) {
                    return MRC_FORMINVALID;
                }
                $dStartMsec = (float)$sTime;
            } else {
                // 最高得点/受講回数/コンティニュー位置/レベルを更新
                updateExamDataBase($db, $ixChapter, $ixPhase, $sContColumn, $sScoreColumn);
            }
            $_SESSION[SKEY_EXAM] = [$ixChapter, $ixPhase, $ixStep, $dStartMsec, $aixSel];
            break;
        case CI_EXAM2A:
            $_SESSION[SKEY_EXAM] = $ixChapter;
            break;
        }
    } else {
        // [GET]
        if (!isset($_SESSION[SKEY_EXAM])) {
            return MRC_PAGEINVALID;
        }
        $ixChapter = $_SESSION[SKEY_EXAM];
        if (is_array($ixChapter)) {
            list($ixChapter, $ixPhase, $ixStep, $dStartMsec, $aixSel) = $ixChapter;
        }
    }

    // ページを作成
    switch ($ixChapter) {
    default:
        if ($ixPhase !== N_EXAM) {
            $iCase = $ixPhase + 1;
            $nImage = getCaseConst($ixPhase, TCC_NIMAGE);
            $ixCmd = $ixStep - $nImage;
            switch ($ixCmd) {
            default:
                // [イメージ画面]
                $iImage = $ixStep + 1;
                putHeader($hb, HTT_EXAM, JSF_SETDISPTIMER, $dStartMsec);
                putCaptions($hb, 'Image: ' . $iImage . '/' . $nImage, $iCase);
                $hb->puts('<form action="' . PFILE_EXAM . '" method="post">');
                putHiddenParams($hb, $ixChapter, $ixPhase, $ixStep);
                putHiddenInput($hb, FNAME_TIME, $dStartMsec);
                putHiddenSelections($hb, $aixSel);
                $hb->puts('<div class="button-neat">');
                if ($ixStep === 0) {
                    $hb->puts('<span></span>');
                } else {
                    putSubmitButton($hb, 'btn btn-primary btn-fixed', '戻る', FBTN_BACKWARD);
                }
                putSubmitButton($hb, 'btn btn-primary btn-fixed', '進む', FBTN_FORWARD);
                $hb->puts('</div>');
                $hb->puts('</form>');
                putUncopiableImage($hb, DIR_EXAM . sprintf('%02d%02d.jpg', $iCase, $iImage));
                break;
            case TSS_SELECT:
            case TSS_ERROR:
                // [所見判定画面][所見判定エラー画面]
                // ラジオボタンの配置
                putHeader($hb, HTT_EXAM, JSF_SETDISPTIMER, $dStartMsec);
                putCaptions($hb, 'M-NBI所見判定', $iCase);
                $hb->puts('<form action="' . PFILE_EXAM . '" method="post">');
                putHiddenParams($hb, $ixChapter, $ixPhase, $ixStep);
                putHiddenInput($hb, FNAME_TIME, $dStartMsec);
                $hb->puts('<div class="button-neat">');
                putSubmitButton($hb, 'btn btn-primary btn-fixed', '戻る', FBTN_BACKWARD);
                putSubmitButton($hb, 'btn btn-primary btn-fixed', '確認', FBTN_FORWARD);
                $hb->puts('</div>');
                if ($ixCmd === TSS_ERROR) {
                    if (!isSelectionAny($aixSel)) {
                        putSelWarning($hb, $aixSel[TRB_DL   ] !== TDL_NONE, 'DL'   );
                        putSelWarning($hb, $aixSel[TRB_V    ] !== TMV_NONE, 'V'    );
                        putSelWarning($hb, $aixSel[TRB_S    ] !== TMS_NONE, 'S'    );
                        putSelWarning($hb, $aixSel[TRB_GRADE] !== TGC_NONE, 'Grade');
                    } elseif (!isSelectionValid($aixSel)) {
                        $hb->puts('<p><span class="text-danger">Gradeの選択に矛盾があります。</span></p>');
                    }
                }
                $hb->puts('<p>以下の分類から適切なものを選択し、「確認」ボタンをクリックしてください。</p>');
                $hb->puts('<h4>■VS classification system</h4>');
                $hb->puts('<table class="selection-table"><tbody>');
                putRadioButtons($hb, 'DL', TRB_DL, 4, $aaRadioTable[TRB_DL], $aixSel[TRB_DL]);
                putRadioButtons($hb, 'V' , TRB_V , 4, $aaRadioTable[TRB_V ], $aixSel[TRB_V ]);
                putRadioButtons($hb, 'S' , TRB_S , 4, $aaRadioTable[TRB_S ], $aixSel[TRB_S ]);
                $hb->puts('</tbody></table>');
                $hb->puts('<h4>■Grade分類</h4>');
                $hb->puts('<table class="selection-table"><tbody>');
                putRadioButtons($hb, NULL, TRB_GRADE, 5, $aaRadioTable[TRB_GRADE], $aixSel[TRB_GRADE]);
                $hb->puts('</tbody></table>');
                $hb->puts('</form>');

                $hb->puts('<p>');
                $hb->puts('Grade 1 M-NBIにて非癌と確信できる病変。<br>');
                $hb->puts('Grade 2 M-NBIにて非癌を疑う病変。<br>');
                $hb->puts('Grade 3 M-NBIでは癌・非癌の判定が困難な病変。<br>');
                $hb->puts('Grade 4 M-NBIにて癌を疑う病変。<br>');
                $hb->puts('Grade 5 M-NBIにて癌と確信できる病変。');
                $hb->puts('</p>');
                break;
            case TSS_CONFIRM:
                // [最終確認画面]
                putHeader($hb, HTT_EXAM, JSF_SETDISPTIMER | JSF_SETPASSEDMSEC, $dStartMsec);
                putCaptions($hb, '最終確認画面', $iCase);
                $hb->puts('<form action="' . PFILE_EXAM . '" method="post">');
                putHiddenParams($hb, $ixChapter, $ixPhase, $ixStep);
                $hb->puts('<input type="hidden" id="time" name="' . FNAME_TIME . '" value="' . $dStartMsec . '">');
                putHiddenSelections($hb, $aixSel);
                $hb->puts('<div class="button-neat">');
                putSubmitButton($hb, 'btn btn-primary btn-fixed', '戻る', FBTN_BACKWARD);
                putClickEventButton($hb, 'btn btn-warning btn-fixed', '確定', FBTN_FORWARD, "setPassedMsec('time')");
                $hb->puts('</div>');
                $hb->puts('</form>');
                $hb->puts('<div class="diagnosis-table">');
                $hb->puts('<p>あなたの診断は...</p>');
                $hb->puts('<table class="table">');
                putTable2($hb, 'DL'   , classToStr(TCS_DL, $aixSel[TRB_DL]));
                putTable2($hb, 'V'    , classToStr(TCS_V , $aixSel[TRB_V ]));
                putTable2($hb, 'S'    , classToStr(TCS_S , $aixSel[TRB_S ]));
                putTable2($hb, 'Grade', $aixSel[TRB_GRADE]                 );
                $hb->puts('</table>');
                $hb->puts('<p>です。</p>');
                $hb->puts('</div>');
                $hb->puts('<p class="diagnosis-table-caution">間違いなければ右上の「確定」ボタンをクリックしてください。<br>「確定」ボタンを押すと、前の設問に戻ったり回答を訂正することはできませんのでご注意ください。</p>');
                break;
            case TSS_RELAY:
                // [「次の設問へ」画面]
                putHeader($hb, HTT_EXAM, JSF_DISPTIME, $dStartMsec);
                putCaptions($hb, 'Break Time', $iCase);
                $hb->puts('<hr>');
                $hb->puts('<div class="break-region">');
                $hb->puts('<div class="break-region-unit">');
                $hb->puts('<p>一旦終了するには、「ホーム」ボタンを押してください。</p>');
                putLinkButton($hb, 'btn btn-success btn-fixed btn-down', 'ホーム', PFILE_HOME);
                $hb->puts('</div>');
                $hb->puts('<div class="break-region-unit">');
                $hb->puts('<p>次の設問に移るには、「進む」ボタンを押してください。</p>');
                $hb->puts('<form action="' . PFILE_EXAM . '" method="post">');
                putHiddenParams($hb, $ixChapter, $ixPhase, $ixStep);
                $hb->puts('<input type="hidden" id="time" name="' . FNAME_TIME . '" value="0">');
                putHiddenSelections($hb, $aixSel);
                putClickEventButton($hb, 'btn btn-primary btn-fixed btn-down', '進む', FBTN_FORWARD, "setStartMsec('time')");
                $hb->puts('</form>');
                $hb->puts('</div>');
                $hb->puts('</div>');
                $hb->puts('<hr>');
                $hb->puts('<p class="diagnosis-table-caution">ホームに戻った場合は、続行ボタンで続きから再開できます。</p>');
                break;
            }
            putFooter($hb, HTT_EXAM);
        } else {
            // [「あなたの得点」ページ]
            $sScoreColumn = getChapterConst($ixChapter, CC_SCORECOLUMN);
            assertStr($sScoreColumn);
            putScorePage($hb, $db->getInt($sScoreColumn), N_EXAM);
        }
        break;
    case CI_EXAM2A:
        // [解答表]
        putHeader($hb, HTT_LESSON, 0, 0);
        $hb->puts('<section class="main-section text-center">');
        $hb->puts('<h5>40問テスト（e-learning受講後）の解答</h5>');
        putAnswerTable($hb, $db, CI_EXAM2);
        $hb->puts('<p>※<span class="wrong-answer">赤文字</span>は直近のPost-testで不正解だった部分です。</p>');
        $hb->puts('<div class="foot-center">');
        $hb->puts('<h5>40問テスト（e-learning受講前）の解答</h5>');
        $hb->puts('<p>e-learning受講前と受講後の40問テストは画像も出題順も全く同じです。</p>');
        putAnswerTable($hb, $db, CI_EXAM1);
        $hb->puts('<p>※<span class="wrong-answer">赤文字</span>はPre-testで不正解だった部分です。</p>');
        $hb->puts('</div>');
        $hb->puts('<div class="foot-center">');
        putLinkButton($hb, 'btn btn-success btn-fixed', 'ホーム', PFILE_HOME);
        $hb->puts('</div>');
        $hb->puts('</section>');
        putFooter($hb, HTT_LESSON);
        break;
    }

    return MRC_OK;
}



// ■■■■■■■■■■■■■■■■
// ■         メイン処理         ■
// ■■■■■■■■■■■■■■■■

startMain('Exam_main', 'putErrorMessage');

?>
