<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache, no-store" />
<meta http-equiv="Expires" content="-1">
<link rel="shortcut icon" href="/image/common/favicon.ico" type="image/vnd.microsoft.icon" />
<link rel="icon" href="/image/common/favicon.ico" type="image/vnd.microsoft.icon" />
<meta name="description" content="おなかの病気の解説や、がんの早期発見・治療につながる内視鏡検査の情報を掲載。内視鏡による検査や治療について理解を深めていただくため、主な消化器疾患や内視鏡に関する情報提供を行っています。食道、胃、十二指腸、膵臓、胆道（胆管・胆のう）、小腸、大腸（直腸・結腸）" />
<meta name="keywords" content=""内視鏡,がん,癌,ガン,胃カメラ,大腸,胃,消化器,腫瘍,オリンパス,検査" />
<title><?php echo isset($breadcrumb) ? $breadcrumb.' ' : ''; ?>オリンパス おなかの健康ドットコム</title>
<?php
echo \Asset::js(array('jquery-1.11.1.min.js', 'jquery.tablesorter.min.js', 'jPages.js', 'default.js'));
echo \Asset::css(array('propertyreset.css', 'common.css', 'form.css', 'tablesorter.css', 'jPages.css'));
?>
</head>
<body>
<?php
if(Fuel::$env == Fuel::PRODUCTION)
{
?>
<!-- Google Tag Manager -->
<noscript>
<iframe src="//www.googletagmanager.com/ns.html?id=GTM-PXSCG6" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<script>
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PXSCG6');
</script>
<!-- End Google Tag Manager -->
<?php
}
?>
<a name="pagetop" id="pagetop"></a>
<?php
\Cookie::set(\Config::get('security.csrf_token_key'), \Security::generate_token());
echo $body;
	//if (Fuel::$env === Fuel::STAGING || Fuel::$env === Fuel::TEST)
	if (Fuel::$env === Fuel::TEST)
	{
		echo '<div style="clear: both">';
		echo \Debug::dump(\Session::get());
		echo '</div>';
	}
?>
</body>
</html>
