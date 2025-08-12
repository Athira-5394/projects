<?php

// 「初めに」ページ



// ■■■■■■■■■■■■■■■■
// ■        外部ファイル        ■
// ■■■■■■■■■■■■■■■■

// 共通処理
require __DIR__ . '/common.php';



// ■■■■■■■■■■■■■■■■
// ■          内部関数          ■
// ■■■■■■■■■■■■■■■■

// メイン関数
function Preface_main($hb)
{

    // ユーザデータを準備
    $db = prepareUserData(FALSE);
    if (!is_object($db)) {
        // エラーコード(MRC_～)を返す
        assertInt($db);
        return $db;
    }

    // formを確認
    if (getFormStr(FNAME_BUTTON) === FBTN_FORWARD) {
        // [[進む]ボタン]
        if ($db->getInt(DBC_LEVEL) === CI_PREFACE) {
            $db->setInt(DBC_LEVEL, CI_PREFACE + 1);
        }
        // ホームへリダイレクト
        header('Location: ' . PFILE_HOME);
        exit;
    }

    // ページを作成
    putHeader($hb, HTT_LESSON, 0, 0);
    $hb->puts('<section class="main-section">');
    $hb->puts('<h4>E-learningについて</h4>');
    $hb->puts('<section class="main-section-body">');
    $hb->puts('<p>このコンテンツは、【40問テスト（e-learning受講前）】【教材1（入門編講義）動画】【教材2（応用編講義）動画】【100問マラソン】【40問テスト（e-learning受講後）】【補助教材スライド】の6パートから構成されています。<br>');
    $hb->puts('初めは【「40問テスト（e-learning受講前）】から<span class="text-danger">順序通りに</span>進めてください。</p>');
    $hb->puts('<dl>');
    $hb->puts('<dt>【40問テスト（e-learning受講前）】</dt>');
    $hb->puts('<dd>動画などのコンテンツを受講する前の、現時点の実力を試すテストです。この試験は最初に1度しか受けられません。40問解答後に得点が表示されます。終了すると次の教材1（入門編講義）動画に進むことができます。</br>');
    $hb->puts('なお、e-learning受講後の40問テストを解答した後には、DL/V/S、癌・非癌の判定を記載した40問テスト（e-learning受講前）の解答を、40問テスト（e-learning受講後）の解答と同時に閲覧できます。</dd>');
    $hb->puts('<dt>【教材1（入門編講義）動画】</dt>');
    $hb->puts('<dd>1-1～1-4まであり、初めはこの順にご覧ください。各パート視聴後は、画面内の<span class="text-danger">「最後まで閲覧しました」</span>ボタンを必ずクリックしてください。視聴カウントとなります。1-4を終えたら次の教材2へ進めます。</dd>');
    $hb->puts('<dt>【教材2（応用編講義）動画】</dt>');
    $hb->puts('<dd>2-1～2-6まであり、初めはこの順にご覧ください。各パート視聴後は、画面内の<span class="text-danger">「最後まで閲覧しました」</span>ボタンを必ずクリックしてください。視聴カウントとなります。2-6を終えたら次の100問マラソンへ進めます。</dd>');
    $hb->puts('<dt>【100問マラソン】</dt>');
    $hb->puts('<dd>3-1ランダムテスト1、3-2体系的並び順による解答、3-3ランダムテスト2の3種類があります。これらも初めはこの順に受講してください。<br>');
    $hb->puts('<span class="text-danger">ランダムテスト</span>では100枚のM-NBI画像がランダムに表示されるテストで、<span class="text-danger">DL/V/S</span>と、<span class="text-danger">癌・非癌</span>を判定してください。<span class="text-danger">「確定」</span>ボタンを押した際、解答が判定されます。<br>');
    $hb->puts('<span class="text-danger">体系的並び順による解答</span>では、胃炎（DLなし）、胃炎（DLあり）、腺腫、癌のカテゴリー順での表示となります。同じタイプの画像を続けて見ることで、特徴をつかんでください。<br>');
    $hb->puts('100問マラソンはすべての問題に答え終えると全体の得点が表示されます。最後に<span class="text-danger">「完了」</span>ボタンが表示されますので必ずクリックしてください。</dd>');
    $hb->puts('<dt>【40問テスト（e-learning受講後）】</dt>');
    $hb->puts('<dd>動画や100問マラソンなどの教材を受講した後の実力を試すテストです。40問解答後に得点が表示され、その後全40問の<span class="text-danger">DL/V/S</span>、<span class="text-danger">癌・非癌</span>の判定を記載した解答を閲覧できます。</dd>');
    $hb->puts('<dt>【補助教材スライド】</dt>');
    $hb->puts('<dd>動画コンテンツで使用されたスライドを1枚ずつ閲覧することができます。必要に応じて参考資料としてご利用ください。</dd>');
    $hb->puts('</dl>');
    $hb->puts('<p>40問テスト（e-learning受講前）以外の5パートは、一度閲覧した部分は後から<span class="text-danger">何度でも</span>繰り返し、閲覧・解答できます。<br>各パートの閲覧・解答回数はカウントされ、40問テストと100問マラソンはハイスコア（最高得点）も記憶されます。なお得点は各問とも癌／非癌が合致していれば1点としています。動画コンテンツや100問マラソンに繰り返しチャレンジし、40問テスト（e-learning受講後）のスコア向上を目指してください。<br>途中まで進めたコンテンツを途中で中断したりブラウザを閉じた場合は、<span class="text-danger">「続行」</span>ボタンで再開することができます。この時、40問テストや100問マラソンのスコアは維持されます。同じコンテンツを再び最初から受講したい場合は、一度最後まで進めてから、改めて<span class="text-danger">「開始」</span>ボタンを押してください。<br>閲覧回数やハイスコアは他の受講者（メディカルタウン会員）に公開されることはございません。</p>');
    $hb->puts('<h5>不許複製</h5>');
    $hb->puts('本サイトが提供する画像、映像、音声等を無断で複製し配布することは、有形・無形を問わず固くお断り致します。');
    $hb->puts('<div class="foot-center">');
    $hb->puts('<form action="' . PFILE_PREFACE . '" method="post">');
    putSubmitButton($hb, 'btn btn-danger', '了解しました', FBTN_FORWARD);
    $hb->puts('</form>');
    $hb->puts('</div>');
    $hb->puts('</section>');
    $hb->puts('</section>');
    putFooter($hb, HTT_LESSON);

    return MRC_OK;
}



// ■■■■■■■■■■■■■■■■
// ■         メイン処理         ■
// ■■■■■■■■■■■■■■■■

startMain('Preface_main', 'putErrorMessage');

?>
