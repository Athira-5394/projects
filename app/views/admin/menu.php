<div id="bodySection">

	<!-- headSection -->
	<?php echo \View::forge('share/body_header'); ?>
	<!-- /headSection -->

	<!-- mainContents -->
	<div class="mainContents clearfix">

		<!-- fullcolum Contents -->
		<div class="fullcolumContents">
			<h1 class="title mar_b40 clearfix">管理ツールメニュー<a href="<?php echo $logout; ?>" class="logout"><?php echo \Asset::img('logout.png');?></a></h1>

			<div class="inputbtnarea">
				<ul class="inputbtn_1set clearfix">
					<li class="mar_r20 admin_menu"><input type="button" value="メルマガ会員検索・出力" onclick="javascript:page_move('<?php echo $maillist; ?>');return false;" /></li>
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
