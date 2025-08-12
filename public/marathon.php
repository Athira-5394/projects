<?php

// 「100問マラソン」ページ

// フォームと画面遷移について
//   FNAME_CHAPTER  チャプタ  CI_MARATHON1～CI_MARATHON3
//   FNAME_PHASE    フェーズ  -1…現在位置(0～N_MARATHON-1)をデータベースから取得して処理
//                            0～N_MARATHON-1…症例1～症例N_MARATHONの処理
//                            N_MARATHON…「あなたの得点」画面  ←ここに到達したら集計してデータベースを更新
// (Phase:0～N_MARATHON-1の時のみ)
//   FNAME_STEP     ステップ  MSI_CAPTION…(3-2の必要な箇所のみ)キャプション画面
//                            MSI_QUESTION…出題画面
//                            MSI_JUDGMENT…判定画面  ←ここに到達したとき、コンティニュー位置と一致していたらデータベースを更新
//                            MSI_ANSWER…正解画面
// (Step:MSI_JUDGMENT/MSI_ANSWERの時のみ)
//   FNAME_SELECT   ラジオボタンのセレクト状態



// ■■■■■■■■■■■■■■■■
// ■        外部ファイル        ■
// ■■■■■■■■■■■■■■■■

// 共通処理
require __DIR__ . '/common.php';



// ■■■■■■■■■■■■■■■■
// ■          内部定数          ■
// ■■■■■■■■■■■■■■■■

const N_CAPTION = 4;    // 3-2のキャプション画面の枚数
if (N_MARATHON !== MAX_MARATHON) {
    define('N_MARATHON2', N_MARATHON + 1);
} else {
    define('N_MARATHON2', N_MARATHON + N_CAPTION);
}

// ステップのインデックス
const MSI_CAPTION  = 0;    // (3-2の必要な箇所のみ)キャプション画面
const MSI_QUESTION = 1;    // 出題画面
const MSI_JUDGMENT = 2;    // 判定画面
const MSI_ANSWER   = 3;    // 正解画面
const MSI_COUNT    = 4;

// 症例定数のインデックス
const MDD_DIAG  = 0;    // 診断
const MDD_DL    = 1;    // DL
const MDD_V     = 2;    // V
const MDD_S     = 3;    // S
const MDD_COUNT = 4;

// 診断(Diagnosis)のインデックス
const MDN_NONE      = 0;    // (出題     )無回答
const MDN_CANCER    = 1;    // (出題/正解)癌
const MDN_NONCANCER = 2;    // (     正解)非癌
const MDN_GASTRI    = 3;    // (     正解)非癌(胃炎)
const MDN_ADENOMA   = 4;    // (     正解)非癌(腺腫)
const MDN_ANOTHER   = 5;    // (出題     )非癌(胃炎・腺腫)
const MDN_COUNT     = 6;

// DL(Demarcation line)のインデックス
const MDL_NONE    = 0;      // (出題     )無回答
const MDL_ABSENT  = 1;      // (出題/正解)Absent
const MDL_PRESENT = 2;      // (出題/正解)Present
const MDL_COUNT   = 3;

// V(Microvascular pattern)のインデックス
const MMV_NONE      = 0;    // (出題     )無回答
const MMV_ABSENT    = 1;    // (出題/正解)Absent
const MMV_REGULAR   = 2;    // (出題/正解)Regular
const MMV_IRREGULAR = 3;    // (出題/正解)Irregular
const MMV_COUNT     = 4;

// S(Microsurface pattern)のインデックス
const MMS_NONE      = 0;    // (出題     )無回答
const MMS_ABSENT    = 1;    // (出題/正解)Absent
const MMS_REGULAR   = 2;    // (出題/正解)Regular
const MMS_IRREGULAR = 3;    // (出題/正解)Irregular
const MMS_COUNT     = 4;

// ラジオボタンのインデックス
const MRB_DL    = 0;    // DL
const MRB_V     = 1;    // V
const MRB_S     = 2;    // S
const MRB_DIAG  = 3;    // 診断
const MRB_COUNT = 4;

// $amRadioTableのカラムのインデックス
const MRT_CLASS   = 0;    // クラス(MDD_～)
const MRT_CAPTION = 1;    // キャプション
const MRT_RADIO   = 2;    // ラジオボタン(MDN_～など)
const MRT_COUNT   = 3;



// ■■■■■■■■■■■■■■■■
// ■          内部関数          ■
// ■■■■■■■■■■■■■■■■

// フォトの定数を取得
// <引数1> フォト番号(1:"4001.jpg"～100:"4100.jpg")
// <引数2> フォト定数のインデックス(MDD_～)
// <戻り値> 状態
//          MDD_DIAG指定時: MDN_～
//          MDD_DL指定時:   MDL_～
//          MDD_V指定時:    MMV_～
//          MDD_S指定時:    MMS_～
function getPhotoConst($iPhoto, $ixPhotoConst)
{
    assertIndex($ixPhotoConst, MDD_COUNT);
    static $aiTable = [
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_REGULAR  , ],    // 4001
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_REGULAR  , ],    // 4002
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_REGULAR  , ],    // 4003
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_REGULAR  , ],    // 4004
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_REGULAR  , ],    // 4005
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_REGULAR  , ],    // 4006
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_REGULAR  , ],    // 4007
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_REGULAR  , ],    // 4008
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_REGULAR  , ],    // 4009
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_REGULAR  , ],    // 4010
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_REGULAR  , ],    // 4011
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_REGULAR  , ],    // 4012
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_REGULAR  , ],    // 4013
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_ABSENT   , ],    // 4014
        [ MDN_GASTRI , MDL_ABSENT , MMV_REGULAR  , MMS_ABSENT   , ],    // 4015
        [ MDN_GASTRI , MDL_ABSENT , MMV_IRREGULAR, MMS_REGULAR  , ],    // 4016
        [ MDN_GASTRI , MDL_ABSENT , MMV_IRREGULAR, MMS_ABSENT   , ],    // 4017
        [ MDN_GASTRI , MDL_ABSENT , MMV_ABSENT   , MMS_REGULAR  , ],    // 4018
        [ MDN_GASTRI , MDL_ABSENT , MMV_ABSENT   , MMS_REGULAR  , ],    // 4019
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4020*
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4021
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4022
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4023
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4024
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4025
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4026
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4027
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4028
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4029
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4030
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4031
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4032*
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4033
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4034*
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_ABSENT   , ],    // 4035
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_ABSENT   , ],    // 4036
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_ABSENT   , ],    // 4037
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_ABSENT   , ],    // 4038
        [ MDN_GASTRI , MDL_PRESENT, MMV_REGULAR  , MMS_ABSENT   , ],    // 4039
        [ MDN_GASTRI , MDL_PRESENT, MMV_ABSENT   , MMS_REGULAR  , ],    // 4040
        [ MDN_ADENOMA, MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4041
        [ MDN_ADENOMA, MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4042
        [ MDN_ADENOMA, MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4043
        [ MDN_ADENOMA, MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4044
        [ MDN_ADENOMA, MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4045
        [ MDN_ADENOMA, MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4046
        [ MDN_ADENOMA, MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4047
        [ MDN_ADENOMA, MDL_PRESENT, MMV_REGULAR  , MMS_REGULAR  , ],    // 4048*
        [ MDN_ADENOMA, MDL_PRESENT, MMV_ABSENT   , MMS_REGULAR  , ],    // 4049
        [ MDN_ADENOMA, MDL_PRESENT, MMV_ABSENT   , MMS_REGULAR  , ],    // 4050
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4051
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4052
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4053
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4054
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4055
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4056
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4057
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4058
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4059
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4060
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4061
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4062
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4063
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4064
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4065
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4066
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4067
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4068*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4069*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4070*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4071*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4072*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4073*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_IRREGULAR, ],    // 4074*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4075*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4076*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4077*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4078*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4079*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4080*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4081*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4082*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4083*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4084*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4085*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4086*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4087*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4088*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4089*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4090*
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4091
        [ MDN_CANCER , MDL_PRESENT, MMV_IRREGULAR, MMS_ABSENT   , ],    // 4092*
        [ MDN_CANCER , MDL_PRESENT, MMV_ABSENT   , MMS_IRREGULAR, ],    // 4093
        [ MDN_CANCER , MDL_PRESENT, MMV_ABSENT   , MMS_IRREGULAR, ],    // 4094
        [ MDN_CANCER , MDL_PRESENT, MMV_ABSENT   , MMS_IRREGULAR, ],    // 4095
        [ MDN_CANCER , MDL_PRESENT, MMV_ABSENT   , MMS_IRREGULAR, ],    // 4096
        [ MDN_CANCER , MDL_PRESENT, MMV_ABSENT   , MMS_IRREGULAR, ],    // 4097
        [ MDN_CANCER , MDL_PRESENT, MMV_ABSENT   , MMS_IRREGULAR, ],    // 4098
        [ MDN_CANCER , MDL_PRESENT, MMV_ABSENT   , MMS_IRREGULAR, ],    // 4099
        [ MDN_CANCER , MDL_PRESENT, MMV_ABSENT   , MMS_IRREGULAR, ],    // 4100
    ];
    assertArray2Column($aiTable, MDD_COUNT);

    return $aiTable[$iPhoto - 1][$ixPhotoConst];
}

// 症例インデックス(0～99)をフォト番号(1～100)に変換
function caseToPhoto($ixMrt, $ixPhase)
{
    assertIndex($ixMrt, 3);
    assertIndex($ixPhase, N_MARATHON);
    static $aiTable = [
        // ランダムテスト1
        [ 34,  92,  87,  98,  47,  25,  81,  26,  36,  83,
          76,  68,  44,  70,   4,  67,  24,  62,  84,  11,
          85,  15,  53,  51,  10,  57,  20,  71,  27,  35,
          30,  69,  12,  91,  86,  63,  93,  60,  78,  54,
          58,  94,  41,   3,  90,  96,  64,  89,  40,  14,
          59,  73,  38,  82,  19,   9,   1,  33,  45,  21,
           2,  37,  32,  46,   6,  79,  74,  77,  17,  31,
          49,  43,  22,  16,  39,  48,  50,  99,  23,  80,
          55,  28,   7,   5,  65,  95,  75,  29,  97,  88,
          52,  72,  61, 100,  66,  42,  18,  56,  13,   8, ],
        // 体系的並び順による解答
        [  1,   2,   3,   4,   5,   6,   7,   8,   9,  10,
          11,  12,  13,  14,  15,  16,  17,  18,  19,  20,
          21,  22,  23,  24,  25,  26,  27,  28,  29,  30,
          31,  32,  33,  34,  35,  36,  37,  38,  39,  40,
          41,  42,  43,  44,  45,  46,  47,  48,  49,  50,
          51,  52,  53,  54,  55,  56,  57,  58,  59,  60,
          61,  62,  63,  64,  65,  66,  67,  68,  69,  70,
          71,  72,  73,  74,  75,  76,  77,  78,  79,  80,
          81,  82,  83,  84,  85,  86,  87,  88,  89,  90,
          91,  92,  93,  94,  95,  96,  97,  98,  99, 100, ],
        // ランダムテスト2
        [ 12,  88,  85,   3,  72,  41,  77,  78,  26,   8,
          51,  29,  92,  74,   9,  53,  16,  28,  96,  57,
          50,  18,  76,  23,  54,  21,  59,  97,  60,  48,
          38,  90,  80,  24,  69,  32,  84,  73,  55,  87,
          13,  30,  22,  64,   7,  93,  27,  82,  35,  47,
          33,  67,  58,  52,  91,  71,  42,  17,  40,  56,
          44,  86,  94,  61,  49,  43,   2,  36,  95,  11,
          79,  10,  75,  39,  20, 100,  98,  83,  31,  62,
          81,  68,   5,  14,  70,  15,  19,  37,   1,  46,
          99,   4,   6,  65,  89,  34,  25,  66,  45,  63, ],
    ];
    //assertArray2($aiTable, CC_COUNT, CI_COUNT);

    return $aiTable[$ixMrt][$ixPhase];
}

// 「体系的並び順」のフェーズ番号をフォト番号(1～100)またはキャプション番号(-1～-4)に変換
function orderToPhoto($ixPhase)
{
    assertIndex($ixPhase, N_MARATHON2);
    static $aiTable = [
        -1,   1,  2,  3,  4,  5,  6,  7,  8,  9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19,
        -2,  20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
        -3,  41, 42, 43, 44, 45, 46, 47, 48, 49, 50,
        -4,  51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100,
    ];

    return $aiTable[$ixPhase];
}

// 症例の前にキャプションがあればインデックスを取得
// <引数1> 症例インデックス(0～99)
// <戻り値> キャプションがあればインデックス、なければ-1
function caseToCaption($ixCase)
{
    assertIndex($ixCase, N_MARATHON);
    static $aixTable = [0, 19, 40, 50,];

    $nTable = count($aixTable);
    for ($i = 0; $i < $nTable; ++$i) {
        $ixCap = $aixTable[$i];
        if ($ixCase < $ixCap) {
            break;
        }
        if ($ixCase === $ixCap) {
            return $i;
        }
    }
    return -1;
}

// 診断/DL/V/Sを表す文字列に変換
// <引数1> 文字列の種類(MDD_～)
// <引数2> MDN_～/MDL_～/MMV_～/MMS_～
function classToStr($ixClass, $ixDiag)
{
    assertIndex($ixClass, MDD_COUNT);
    static $asTable = [
        ['（無回答）', '癌', '非癌', '非癌（胃炎）', '非癌（腺腫）', '非癌（胃炎・腺腫）',],
        ['（無回答）', 'Absent', 'Present',             ],
        ['（無回答）', 'Absent', 'Regular', 'Irregular',],
        ['（無回答）', 'Absent', 'Regular', 'Irregular',],
    ];
    assertArray1($asTable, MDD_COUNT);
    assertArray1($asTable[MDD_DIAG], MDN_COUNT);
    assertArray1($asTable[MDD_DL  ], MDL_COUNT);
    assertArray1($asTable[MDD_V   ], MMV_COUNT);
    assertArray1($asTable[MDD_S   ], MMS_COUNT);

    return $asTable[$ixClass][$ixDiag];
}

// 回答は正解か？
function isSelectionCorrect($iPhoto, $aixSel)
{
    $ixDiag = getPhotoConst($iPhoto, MDD_DIAG);
    return ($ixDiag === MDN_CANCER && $aixSel[MRB_DIAG] === 2 || $ixDiag !== MDN_CANCER && $aixSel[MRB_DIAG] === 1);
}

// メイン関数
function Marathon_main($hb)
{
    // ラジオボタンの情報テーブル
    static $amRadioTable = [
        [MDD_DL  , 'DL'  , [MDL_ABSENT , MDL_PRESENT,               ]],
        [MDD_V   , 'V'   , [MMV_ABSENT , MMV_REGULAR, MMV_IRREGULAR,]],
        [MDD_S   , 'S'   , [MMS_ABSENT , MMS_REGULAR, MMS_IRREGULAR,]],
        [MDD_DIAG, '診断', [MDN_ANOTHER, MDN_CANCER ,               ]],
    ];
    assertArray2($amRadioTable, MRT_COUNT, MRB_COUNT);

    // ユーザデータを準備
    $db = prepareUserData(FALSE);
    if (!is_object($db)) {
        // エラーコード(MRC_～)を返す
        assertInt($db);
        return $db;
    }

    // パラメータを取得、データベースを更新
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // [POST]
        $ixChapter = getFormIntRange(FNAME_CHAPTER, CI_MARATHON1, CI_MARATHON3);
        if ($ixChapter === NULL) {
            return MRC_FORMINVALID;
        }
        if ($db->getInt(DBC_LEVEL) < $ixChapter) {
            return MRC_FORMINVALID;
        }
        $sContColumn = getChapterConst($ixChapter, CC_CONTCOLUMN);
        assertStr($sContColumn);
        switch ($ixChapter) {
        default:
            $sScoreColumn = getChapterConst($ixChapter, CC_SCORECOLUMN);
            assertStr($sScoreColumn);
            $ixStep = 0;
            $aixSel = [];
            switch (getFormStr(FNAME_BUTTON)) {
            case FBTN_START:
                // [開始]
                $db->setInt($sScoreColumn, 0);
                $db->setInt($sContColumn, 0);
                $ixPhase = 0;
                break;
            case FBTN_CONTINUE:
                // [続行]
                $ixPhase = $db->getInt($sContColumn);
                break;
            default:
                // [進む]([戻る]はない)
                $ixPhase = getFormIntRange(FNAME_PHASE, 0, N_MARATHON - 1);
                if ($ixPhase === NULL) {
                    return MRC_FORMINVALID;
                }
                $ixStep = getFormIndex(FNAME_STEP, MSI_COUNT);
                if ($ixStep === NULL) {
                    return MRC_FORMINVALID;
                }
                ++$ixStep;
                switch ($ixStep) {
                case MSI_JUDGMENT:
                case MSI_ANSWER:
                    // [判定画面/正解画面]
                    // ラジオボタンのセレクト状態を取得
                    for ($i = 0; $i < MRB_COUNT; ++$i) {
                        $iState = getFormRadioState(FNAME_SELECT, $i, count($amRadioTable[$i][MRT_RADIO]));
                        if ($iState < 0) {
                            return MRC_FORMINVALID;
                        }
                        $aixSel[] = $iState;
                    }
                    if ($ixStep === MSI_ANSWER) {
                        // [正解画面]
                        // 妥当なコンティニュー位置だったときのみデータベースを更新
                        $db->beginTransaction();
                        if ($ixPhase === $db->getInt($sContColumn)) {
                            $iPhoto = caseToPhoto($ixChapter - CI_MARATHON1, $ixPhase);
                            if (isSelectionCorrect($iPhoto, $aixSel)) {
                                $db->addInt($sScoreColumn, 1);
                            }
                            $db->addInt($sContColumn, 1);
                        }
                        $db->commit();
                    }
                    break;
                case MSI_COUNT:
                    $ixPhase = $db->getInt($sContColumn);        // コンティニュー位置を上書き
                    $ixStep = 0;
                    break;
                }
                break;
            }
            if ($ixPhase !== N_MARATHON) {
                // キャプション画面を表示しないなら出題画面にする
                if ($ixStep === MSI_CAPTION && ($ixChapter !== CI_MARATHON2 || caseToCaption($ixPhase) < 0)) {
                    $ixStep = MSI_QUESTION;
                }
            } else {
                // 最高得点/受講回数/コンティニュー位置/レベルを更新
                updateExamDataBase($db, $ixChapter, $ixPhase, $sContColumn, $sScoreColumn);
            }
            $_SESSION[SKEY_MARATHON] = [$ixChapter, $ixPhase, $ixStep, $aixSel];
            break;
        case CI_MARATHON2:
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
                $ixPhase = getFormIntRange(FNAME_PHASE, 0, N_MARATHON2 - 1);
                if ($ixPhase === NULL) {
                    return MRC_FORMINVALID;
                }
                ++$ixPhase;
                if ($ixPhase === N_MARATHON2) {
                    $ixPhase = 0;
                }
                $db->setInt($sContColumn, $ixPhase);
                if ($ixPhase === 0) {
                    // 閲覧回数をカウントアップ
                    updateDataBaseView($db, $ixChapter);
                    // ホームへリダイレクト
                    header('Location: ' . PFILE_HOME);
                    exit;
                }
                break;
            case FBTN_BACKWARD:
                // [戻る]
                $ixPhase = getFormIntRange(FNAME_PHASE, 1, N_MARATHON2 - 1);
                if ($ixPhase === NULL) {
                    return MRC_FORMINVALID;
                }
                --$ixPhase;
                $db->setInt($sContColumn, $ixPhase);
                break;
            }
            $_SESSION[SKEY_MARATHON] = [$ixChapter, $ixPhase, 0, 0];
            break;
        }
    } else {
        // [GET]
        if (!isset($_SESSION[SKEY_MARATHON])) {
            return MRC_PAGEINVALID;
        }
        list($ixChapter, $ixPhase, $ixStep, $aixSel) = $_SESSION[SKEY_MARATHON];
    }

    // ページを作成
    switch ($ixChapter) {
    default:
        // [ランダムテスト1][ランダムテスト2]
        if ($ixPhase !== N_MARATHON) {
            $iCase = $ixPhase + 1;
            $iPhoto = caseToPhoto($ixChapter - CI_MARATHON1, $ixPhase);
            putHeader($hb, HTT_MARATHON, 0, 0);
            $hb->puts('<form action="' . PFILE_MARATHON . '" method="post">');
            putHiddenParams($hb, $ixChapter, $ixPhase, $ixStep);
            switch ($ixStep) {
            case MSI_CAPTION:
                // [キャプション画面]
                $ixCaption = caseToCaption($ixPhase);
                $hb->puts('<h3 class="caption-center">Case: ' . $iCase . '/' . N_MARATHON . '</h3>');
                $hb->puts('<div class="button-neat">');
                putLinkButton($hb, 'btn btn-success btn-fixed', 'ホーム', PFILE_HOME);
                putSubmitButton($hb, 'btn btn-primary btn-fixed', '進む', FBTN_FORWARD);
                $hb->puts('</div>');
                $hb->puts('<section class="row">');
                $hb->puts('<div class="col-sm-12 col-lg-9">');
                putUncopiableImage($hb, sprintf(DIR_MARATHON . '49%02d.jpg', $ixCaption + 1));
                $hb->puts('</div>');
                $hb->puts('<div class="col-sm-12 col-lg-3">');
                $hb->puts('<p>「進む」ボタンを押してください。</p>');
                $hb->puts('</div>');
                $hb->puts('</section>');
                break;
            case MSI_QUESTION:
                // [出題画面]
                $hb->puts('<h3 class="caption-center">Case: ' . $iCase . '/' . N_MARATHON . '</h3>');
                $hb->puts('<div class="button-neat">');
                putLinkButton($hb, 'btn btn-success btn-fixed', 'ホーム', PFILE_HOME);
                putSubmitButton($hb, 'btn btn-warning btn-fixed', '確定', FBTN_FORWARD);
                $hb->puts('</div>');
                $hb->puts('<section class="row">');
                $hb->puts('<div class="col-sm-12 col-lg-9">');
                putUncopiableImage($hb, DIR_MARATHON . sprintf('4%03d.jpg', $iPhoto));
                $hb->puts('</div>');
                $hb->puts('<div class="col-sm-12 col-lg-3">');
                $hb->puts('<p><span class="text-danger">' . $iCase . '番目の問題です。</span><br>この症例を診断してください。</p>');
                // ラジオボタン
                for ($i = 0; $i < MRB_COUNT; ++$i) {
                    $amItem = $amRadioTable[$i];
                    $aixAns = $amItem[MRT_RADIO];
                    $nButton = count($aixAns);
                    $hb->puts('<div class="form-block">');
                    $hb->puts('<div class="form-caption">' . $amItem[MRT_CAPTION] . '</div><div class="form-radio">');
                    for ($j = 0; $j < $nButton; ++$j) {
                        $hb->puts('<label><input type="radio" name="' . FNAME_SELECT . '[' . $i . ']' . '" value="' . ($j + 1). '">' . classToStr($amItem[MRT_CLASS], $aixAns[$j]) . '</label>');
                    }
                    $hb->puts('</div>');
                    $hb->puts('</div>');
                }
                $hb->puts('<hr>');
                $hb->puts('<p>※正解・不正解は、癌・非癌のみ判定しております。</p>');
                $hb->puts('</div>');
                $hb->puts('</section>');
                break;
            case MSI_JUDGMENT:
                // [判定画面]
                // 回答をチェック
                if (isSelectionCorrect($iPhoto, $aixSel)) {
                    $sImageFile = 'correct';
                    $sMsg = '正解';
                } else {
                    $sImageFile = 'incorrect';
                    $sMsg = '不正解';
                }
                foreach ($aixSel as $ix) {
                    putHiddenInput($hb, FNAME_SELECT . '[]', $ix);
                }
                $hb->puts('<h3 class="caption-center">Case: ' . $iCase . '/' . N_MARATHON . '</h3>');
                $hb->puts('<div class="button-neat">');
                putLinkButton($hb, 'btn btn-success btn-fixed', 'ホーム', PFILE_HOME);
                putSubmitButton($hb, 'btn btn-primary btn-fixed', '解説', FBTN_FORWARD);
                $hb->puts('</div>');
                $hb->puts('<div class="img-judge"><img class="img-fluid" src="' . DIR_MARATHON . $sImageFile . '.jpg" alt=""></img>');
                $hb->puts('<div style="font-size: 3em;">' . $sMsg . '！</div>');
                $hb->puts('</div>');
                break;
            default:
                // [正解画面]
                $hb->puts('<h3 class="caption-center">Case: ' . $iCase . '/' . N_MARATHON . '</h3>');
                $hb->puts('<div class="button-neat">');
                putLinkButton($hb, 'btn btn-success btn-fixed', 'ホーム', PFILE_HOME);
                putSubmitButton($hb, 'btn btn-primary btn-fixed', '進む', FBTN_FORWARD);
                $hb->puts('</div>');
                $hb->puts('<section class="row">');
                $hb->puts('<div class="col-sm-12 col-lg-9">');
                putUncopiableImage($hb, sprintf(DIR_MARATHON . '4%03d.jpg', $iPhoto));
                $hb->puts('</div>');
                $hb->puts('<div class="col-sm-12 col-lg-3">');
                $hb->puts('<p><span class="text-danger">正解は…</span></p>');
                $hb->puts('<div class="answer-block">');
                foreach ($amRadioTable as $amItem) {
                    $ixClass = $amItem[MRT_CLASS];
                    $hb->puts('<div class="answer-caption">' . $amItem[MRT_CAPTION] . '</div><div class="answer-content">' . classToStr($ixClass, getPhotoConst($iPhoto, $ixClass)) . '</div>');
                }
                $hb->puts('</div>');
                $hb->puts('<hr>');
                $hb->puts('<p>あなたの答えは…</p>');
                $hb->puts('<div class="answer-block">');
                for ($i = 0; $i < MRB_COUNT; ++$i) {
                    $amItem = $amRadioTable[$i];
                    $ixAns = 0;
                    $ixSel = $aixSel[$i];
                    if ($ixSel !== 0) {
                        $ixAns = $amItem[MRT_RADIO][$ixSel - 1];
                        if ($i === MRB_DIAG && $ixAns === MDN_ANOTHER) {
                            $ixAns = MDN_NONCANCER;
                        }
                    }
                    $hb->puts('<div class="answer-caption">' . $amItem[MRT_CAPTION] . '</div><div class="answer-content">' . classToStr($amItem[MRT_CLASS], $ixAns) . '</div>');
                }
                $hb->puts('</div>');
                $hb->puts('<hr>');
                $hb->puts('<p>※正解・不正解は、癌・非癌のみ判定しております。</p>');
                $hb->puts('</div>');
                $hb->puts('</section>');
                break;
            }
            $hb->puts('</form>');
            putFooter($hb, HTT_MARATHON);
        } else {
            // [「あなたの得点」ページ]
            $sScoreColumn = getChapterConst($ixChapter, CC_SCORECOLUMN);
            assertStr($sScoreColumn);
            putScorePage($hb, $db->getInt($sScoreColumn), N_MARATHON);
        }
        break;
    case CI_MARATHON2:
        // [体系的並び順による解答]
        $iPhoto = orderToPhoto($ixPhase);
        if ($iPhoto < 0) {
            // [キャプション]
            $iCase = orderToPhoto($ixPhase + 1);
            $sFile = sprintf('490%d', -$iPhoto);
        } else {
            // [フォト]
            $iCase = $iPhoto;
            $sFile = sprintf('4%03d', $iPhoto);
        }
        putHeader($hb, HTT_MARATHON, 0, 0);
        $hb->puts('<form action="' . PFILE_MARATHON . '" method="post">');
        putHiddenInput($hb, FNAME_CHAPTER, $ixChapter);
        putHiddenInput($hb, FNAME_PHASE, $ixPhase);
        $hb->puts('<h3 class="caption-center">Case: ' . $iCase . '/' . N_MARATHON . '</h3>');
        $hb->puts('<div class="button-neat">');
        if ($ixPhase === 0) {
            $hb->puts('<span class="btn-fixed"></span>');
        } else {
            putSubmitButton($hb, 'btn btn-primary btn-fixed', '戻る', FBTN_BACKWARD);
        }
        putLinkButton($hb, 'btn btn-success btn-fixed', 'ホーム', PFILE_HOME);
        if ($ixPhase !== N_MARATHON2 - 1) {
            putSubmitButton($hb, 'btn btn-primary btn-fixed', '進む', FBTN_FORWARD);
        } else {
            putSubmitButton($hb, 'btn btn-danger btn-fixed', '完了', FBTN_FORWARD);
        }
        $hb->puts('</div>');
        $hb->puts('<section class="row">');
        $hb->puts('<div class="col-sm-12 col-lg-9">');
        putUncopiableImage($hb, DIR_MARATHON . $sFile . '.jpg');
        $hb->puts('</div>');
        $hb->puts('<div class="col-sm-12 col-lg-3">');
        if ($iPhoto > 0) {
            $hb->puts('<p><span class="text-danger">この症例は…</span></p>');
            $hb->puts('<div class="answer-block">');
            foreach ($amRadioTable as $amItem) {
                $ixClass = $amItem[MRT_CLASS];
                $hb->puts('<div class="answer-caption">' . $amItem[MRT_CAPTION] . '</div><div class="answer-content">' . classToStr($ixClass, getPhotoConst($iPhoto, $ixClass)) . '</div>');
            }
            $hb->puts('</div>');
        }
        $hb->puts('</div>');
        $hb->puts('</section>');
        $hb->puts('</form>');
        putFooter($hb, HTT_MARATHON);
        break;
    }

    return MRC_OK;
}



// ■■■■■■■■■■■■■■■■
// ■         メイン処理         ■
// ■■■■■■■■■■■■■■■■

startMain('Marathon_main', 'putErrorMessage');

?>
