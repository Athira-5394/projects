<div id="bodySection">

	<!-- headSection -->
	<?php echo \View::forge('share/body_header_onaka'); ?>
	<!-- /headSection -->

	<!-- mainContents -->
	<div class="mainContents clearfix">

		<!-- fullcolum Contents -->
		<div class="fullcolumContents">

			<h1 class="title">おなかの健康ドットコム　メールマガジン配信停止</h1>
			<p class="text"><?php echo isset($readtext) ? $readtext : ''; ?></p>
			<div class="formarea">
<?php
if( ! empty($err_msg))
{
	echo '<span class="formerror">'.$err_msg.'</span>';
}
else
{
?>
			<dl class="formheadtitle clearfix">
					<dt>メールアドレス</dt>
					<dd><?php echo $email; ?></dd>
				</dl>
			</div>

			<div class="inputbtnarea">
				<ul class="inputbtn_1set clearfix">
					<li><input type="button" id="policy" onclick="javascript:page_move('<?php echo $complete_url; ?>');return false;" value="解除完了"></li>
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
<?php
}
?>