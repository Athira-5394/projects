<?php

// 「ログアウト」表示ページ



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
    // ページを作成
    putHeader($hb, HTT_LESSON, 0, 0);
    $hb->puts('<section class="main-section">');
    $hb->puts('<section class="main-section-body">');
    $hb->puts('<dl>');
    $hb->puts('<dt>E-learning system for EGC by M-NBI コンテンツを終了しました</dt>');
    $hb->puts('<dd>このコンテンツはメディカルタウンに登録された情報（ログイン時に使用されたアカウントの情報）を使用してお客様のお名前を表示しております。</dd>');
    $hb->puts('<dd>PCを共用されているといった理由により表示されているお名前が異なる場合は、<u>実際にご利用になるお客様のアカウント</u>にて改めてログインをお願い致します。<br>');
    $hb->puts('その際、メディカルタウンにログイン中の場合は、あらかじめログアウトを行う必要がございます。下記の手順にてご操作ください。</dd>');
    $hb->puts('<br>');
    $hb->puts('<dt>■ メディカルタウンからログアウト</dt>');
    $hb->puts('<dd>メディカルタウンからログアウトは<a href="' . URL_LOGOUT . '" target="_blank">こちら</a><br>');
    $hb->puts('<div align="center"><img src="/img/logout/mtlogout.png"></div>');
    $hb->puts('</dd>');
    $hb->puts('<br>');
    $hb->puts('<dt>■ログアウト後はE-learning system for EGC by M-NBI コンテンツを再度開いてください</dt>');
    $hb->puts('<dd>メディカルタウンTOPページ⇒消化器内科⇒「E-learning system for EGC by M-NBI」<br>');
    $hb->puts('<div align="center"><img src="/img/logout/step3-1.png"><img src="/img/logout/step3-2.png"></div>');
    $hb->puts('</dd>');
    $hb->puts('<div class="foot-center">');
    $hb->puts('</div>');
    $hb->puts('</section>');
    $hb->puts('</section>');
    putFooter($hb, HTT_LESSON);

    return MRC_OK;

}



// ■■■■■■■■■■■■■■■■
// ■         メイン処理         ■
// ■■■■■■■■■■■■■■■■

startMain('Logout_main', 'putErrorMessage');

?>
