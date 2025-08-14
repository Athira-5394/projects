<div id="bodySection">
	<!-- headSection -->
	<?php echo \View::forge('share/body_header'); ?>
	<!-- /headSection -->

	<!-- mainContents -->
	<div class="mainContents clearfix">

		<!-- fullcolum Contents -->
		<div class="fullcolumContents">

			<h1 class="title"><?php echo isset($title) ? $title : '未設定'; ?></h1>
			<p class="text">既にご登録されております。ありがとうございました。</p>
			<div class="inputbtnarea">
				<ul class="inputbtn_1set clearfix">
					<li><input type="button" value="閉じる" onclick="javascript:myclose();return false;"></li>
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
