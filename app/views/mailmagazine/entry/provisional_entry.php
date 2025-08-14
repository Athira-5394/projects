<div id="bodySection">

	<!-- headSection -->
	<?php echo \View::forge('share/body_header_onaka'); ?>
	<!-- /headSection -->

	<!-- mainContents -->
	<div class="mainContents clearfix">

		<!-- fullcolum Contents -->
		<div class="fullcolumContents">

			<h1 class="title">おなかの健康ドットコム メールマガジン配信仮登録</h1>
			<p class="text"><?php echo isset($readtext) ? $readtext : ''; ?></p>
			<form name="entry" id="entry" action="<?php echo $action; ?>" method="POST" >
<?php
echo \Form::hidden(\Config::get('security.csrf_token_key'), '');
echo Security::js_set_token();
?>
			<div class="formarea">
				<dl class="formitem clearfix">
					<dt>メールアドレス<label class="star" for="form_email" style="ime-mode: disabled;"> *</label></dt>
					<dd>
						<?php echo isset($err_email) ? '<span class="formerror">'.$err_email.'</span>' : ''; ?>
						<input type="text" name="email" size="40" value="<?php echo isset($email) ? $email : ''; ?>">
					</dd>
				</dl>
				<dl class="formitem clearfix">
					<dt>文字認証<label class="star" for="form_email"> *</label></dt>
					<dd><?php echo $captcha; ?></dd>
				</dl>
			</div>
			<div class="formnote">
<?php
$initial = isset($init) && $init === 'ON' ? true : false;
echo \View::forge('share/privacy_policy', array('initial' => $initial));
?>
			</div>

			</form>
			<div class="inputbtnarea">
				<ul class="inputbtn_1set clearfix">
					<li><input type="button" id="policy" onclick="javascirpt:form_submit(document.entry);" value="メルマガ配信を希望する"></li>
				</ul>
				<ul class="clearfix">
					<li><p class="text agree_text pad_t20">※個人情報の取り扱いに同意いただくと、クリックできます。</p></li>
				</ul>
			</div>
		</div>
		<!-- /fullcolum Contents -->

	</div>
	<!-- /mainContents -->

	<!-- footSection -->
	<?php echo \View::forge('share/body_footer_olympus_logo'); ?>
	<!-- /footSection -->

</div>
