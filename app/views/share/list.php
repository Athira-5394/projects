<?php
if(empty($tbody))
{
	$emsg = isset($err_msg) ? $err_msg : '検索結果がありません';
	echo '<span class="formerror">'.$emsg.'</span>';
}
else
{
?>
<?php
if(isset($condition))
{
?>
<p class="descript">検索条件 : <?php echo $condition; ?></p>
<?php
}
?>
<p class="descript">対象件数 : <?php echo number_format(count($tbody)).' 件'; ?></p>
<table id="result" class="tablesorter selector">
<thead>
	<tr>
	<?php
	foreach($thead as $head)
	{
		echo '<th>'.$head.'</th>';
	}
	?>
	</tr>
</thead>
<tbody id="result_body">
	<?php
	foreach($tbody as $row)
	{
		echo '<tr>';
		foreach($row as $column)
		{
			echo '<td>'.$column.'</td>';
		}
		echo '</tr>';
	}
	?>
</tbody>
</table>
<div class="result_pager holder" style="text-align: center"></div>
<?php
}
?>