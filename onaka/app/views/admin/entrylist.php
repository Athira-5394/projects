<script type="text/javascript" charset="utf8">
<!--
function search(){
	button_status(true);
	$.ajax({
		type : "POST",
		url : "<?php echo $search_url; ?>",
		data : {
				"event" : $("#form_events").val(),
		},
		success : function(data, datatype){
			$("#list").empty().append(data);
			$("#result").tablesorter({
					widgets: ['zebra'],
			});
			$(".result_pager").jPages({
		            containerID : "result_body",
		            previous : "←", //前へのボタン
		            next : "→", //次へのボタン
		            perPage : 25, //1ページに表示する個数
		            delay : 10, //要素間の表示速度
			});
			if(data.indexOf("</table>") > -1){
				button_status(false);
			}
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			$("#list").empty().html(XMLHttpRequest.responseText);
		},
		dataType : "html",
	});
}
function button_status(bool){
		$("#csvoutput").attr('disabled', bool);
}
//-->
</script>
<div id="bodySection">

	<!-- headSection -->
	<?php echo \View::forge('share/body_header'); ?>
	<!-- /headSection -->

	<!-- mainContents -->
	<div class="mainContents clearfix">

		<!-- fullcolum Contents -->
		<div class="fullcolumContents">
			<form name="entry" id="entry" method="POST" action="<?php echo $csv_url ;?>"></form>
			<ul class="inputbtn_2set_right clearfix">
				<li class="mar_r10"><input type="button" onclick="javascript:search();return false;" value="検索" /></li>
				<li class="mar_r10"><input type="button" id="csvoutput" onclick="document.entry.submit();" value="CSV" disabled="disabled" /></li>
				<li><input type="button" value="戻る" onclick="javascript:page_move('<?php echo $backlink; ?>');return false;" /></li>
			</ul>
			<div class="formarea mar_b30">
				<dl class="formitem clearfix">
					<dt>開催イベント</dt>
					<dd><?php echo \Form::select('events', null, $lists); ?></dd>
				</dl>
			</div>
			<div id="list"></div>
		</div>
		<!-- /fullcolum Contents -->

	</div>
	<!-- /mainContents -->

	<!-- footSection -->
	<?php echo \View::forge('share/body_footer'); ?>
	<!-- /footSection -->

</div>
