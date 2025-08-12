<?php

// 「教材1(入門編講義)動画」「教材2(応用編講義)動画」ページ

// フォーム
//   FNAME_CHAPTER  チャプタ  CI_VIDEO11～CI_VIDEO26



// ■■■■■■■■■■■■■■■■
// ■        外部ファイル        ■
// ■■■■■■■■■■■■■■■■

// 共通処理
require __DIR__ . '/common.php';



// ■■■■■■■■■■■■■■■■
// ■          内部関数          ■
// ■■■■■■■■■■■■■■■■

// メイン関数
function Video_main($hb)
{
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
        $ixChapter = getFormIntRange(FNAME_CHAPTER, CI_VIDEO11, CI_VIDEO26);
        if ($ixChapter === NULL) {
            return MRC_FORMINVALID;
        }
        if ($db->getInt(DBC_LEVEL) < $ixChapter) {
            return MRC_FORMINVALID;
        }
        if (getFormStr(FNAME_BUTTON) !== FBTN_START) {
            // [完了]
            // 閲覧回数をカウントアップ
            updateDataBaseView($db, $ixChapter);
            // ホームへリダイレクト
            header('Location: ' . PFILE_HOME);
            exit;
        }
        // [開始]
        $_SESSION[SKEY_VIDEO] = $ixChapter;
    } else {
        // [GET]
        if (!isset($_SESSION[SKEY_VIDEO])) {
            return MRC_PAGEINVALID;
        }
        $ixChapter = $_SESSION[SKEY_VIDEO];
    }

    // 動画ページを作成
    putHeader($hb, HTT_LESSON, 0, 0);
    $hb->puts('<section class="main-section text-center">');
    $hb->puts('<h4>動画 ' . getChapterConst($ixChapter, CC_NAME) . '</h4>');
    $hb->puts('<p>画面内の三角ボタンをクリックすると再生が始まります。<br><span class="text-danger">※動画を見終わったら、必ず「最後まで閲覧しました」ボタンを押して下さい。</span></p>');
    $hb->puts('<div class="embed-responsive embed-responsive-4by3">');
    $hb->puts('<video controls poster="" id="video" onContextmenu="alert(\'' . MSG_DONTCOPY . '\');return false">');
    $hb->puts('<source src="' . DIR_VIDEO . getVideoConst($ixChapter, VC_FILE) . '">');

    $hb->puts('<p>動画を再生するには、videoタグをサポートしたブラウザが必要です。</p>');
    $hb->puts('</video>');
    $hb->puts('<div class="video-btn" id="video-btn"></div>');
    $hb->puts('</div>');
    $hb->puts('<div class="foot-center">');
    $hb->puts('<form action="' . PFILE_VIDEO . '" method="post">');
    putHiddenInput($hb, FNAME_CHAPTER, $ixChapter);
    putSubmitButton($hb, 'btn btn-danger', '最後まで閲覧しました', FBTN_FORWARD);
    $hb->puts('</form>');
    $hb->puts('<p>↑お忘れなく！</p>');
    $hb->puts('</div>');
    // JavaScript
    $hb->puts('<script>');
    $hb->puts('  var video = document.getElementById("video");');            // video要素の取得
    $hb->puts('  var video_btn = document.getElementById("video-btn");');    // videoボタンの取得
    $hb->puts('  var btn_status = 0;');                                      // 状態保存
    $hb->puts('  video_btn.addEventListener("click", function(){');          // 画面クリックで再生・ポーズ
    $hb->puts('    if (btn_status === 0) {');
    $hb->puts('      video.play();');
    $hb->puts('      $(".video-btn").hide();');
    $hb->puts('      btn_status = 1;');
    $hb->puts('    } else {');
    $hb->puts('      video.pause();');
    $hb->puts('      $(".video-btn").show();');
    $hb->puts('      btn_status = 0;');
    $hb->puts('    }');
    $hb->puts('  });');
    $hb->puts('</script>');
    $hb->puts('</section>');
    putFooter($hb, HTT_LESSON);

    return MRC_OK;
}



// ■■■■■■■■■■■■■■■■
// ■         メイン処理         ■
// ■■■■■■■■■■■■■■■■

startMain('Video_main', 'putErrorMessage');

?>
