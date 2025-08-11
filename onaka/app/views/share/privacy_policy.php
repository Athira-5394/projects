<script type="text/javascript" charset="utf8">
<!--
function policy_check()
{
	$("#policy").attr('disabled', ! $("#form_policy").prop('checked'));
}
$(document).ready(function() {
	policy_check();
});
//-->
</script>
<h2 class="h2_title_std">個人情報の取り扱いについて</h2>
<div class="mb30 ml30">
	<ol>
		<li>あらかじめお客さまの同意のある場合、または法令で許容されている場合を除き、お客さまからご提供いただきました個人情報は、主として以下の利用目的、または取得の状況から明らかな利用目的のためにのみ利用いたします。</li>
		<div class="mt15 mb15 ml15">
		<ul>
			<li style="list-style-type:disc">お問い合わせに対する回答、ご要望に対する対応のため</li>
			<li style="list-style-type:disc">当社の事業に関する市場調査・統計作成・商品開発のため</li>
		</ul>
		</div>
		<div class="mt15 mb15">上記以外で個人情報を利用する必要が生じた場合には、新たな利用目的についてお客さまに事前に同意をいただいた上で利用いたします。	</div>

		<li>個人情報保護法に基づき、お客さまからご提供いただいた個人情報については、ご本人の個人情報の開示、訂正または削除等をご請求いただくことが可能です。詳細は、「<a href="https://www.olympus.co.jp/products/contact/privacy.html" target="_blank">個人情報に関するお問い合わせ</a>」をご覧ください。 </li>

		<div class="mt15 mb15"></div>
		<li>当社における個人情報の取り扱いについては「<a href="https://www.olympus.co.jp/products/policy/privacy_management/" target="_blank">オリンパスグループにおける個人情報の取り扱いについて</a>」をご覧ください。 </li>

	</ol>
</div>
<div class="formagree">
	<p class="text">
<?php
echo \Form::checkbox('policy', 'ON', $initial, array('onclick' => 'policy_check();'));
echo \Form::label('個人情報の取り扱いについて理解した上で同意します。', 'policy');
?>
	</p>
</div>
