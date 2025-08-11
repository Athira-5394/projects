<div id="bodySection">

	<!-- headSection -->
	<?php echo \View::forge('share/body_header'); ?>
	<!-- /headSection -->

	<!-- mainContents -->
	<div class="mainContents clearfix">

		<!-- fullcolum Contents -->
		<div class="fullcolumContents">

			<form name="entry" id="entry" action="<?php echo $action; ?>" method="POST">
<?php
echo \Form::hidden(\Config::get('security.csrf_token_key'), '');
echo Security::js_set_token();
?>
			<div class="adminarea">
<?php
if(isset($err_msg))
{
	echo '<span class="formerror">'.$err_msg.'</span>';
}
?>
					<dl class="formitem clearfix">
						<dt>アカウント</dt>
						<dd><input type="text" name="account" size="40" value="<?php echo isset($account) ? $account : ''; ?>"></dd>
					</dl>
					<dl class="formitem clearfix">
						<dt>パスワード</dt>
						<dd><input type="password" name="password"></dd>
					</dl>
			</div>

			</form>
			<div class="inputbtnarea">
				<ul class="inputbtn_1set clearfix">
					<li><input type="button" id="policy" onclick="javascript:form_submit(document.entry);" value="ログイン"></li>
				</ul>
			</div>
		</div>
		<!-- /fullcolum Contents -->

	</div>
	<!-- /mainContents -->

	<!-- footSection -->
	<?php echo \View::forge('share/body_footer'); ?>
	<!-- /footSection -->

</div>
