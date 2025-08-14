<dd>
	<ul class="character">
		<li class="characterimg">
			<img src="<?php echo $captcha_route; ?>" alt="Simple Captcha" height="<?php echo $captcha_height; ?>" width="<?php echo $captcha_width; ?>" />
		</li>
		<li>
<?php
if (isset($captcha_error))
{
?>
			<span class="formerror"><?php echo $captcha_error; ?></span>
<?php
}
?>
			<input type="text" id="form_simplecaptcha" size="10" value="" name="<?php echo $captcha_post_name; ?>"><br/>
			<p class="text">半角数字&nbsp;&nbsp;例：1234<br/>上の画像に表示されている数字を入力してください</p>
		</li>
	</ul>
</dd>
