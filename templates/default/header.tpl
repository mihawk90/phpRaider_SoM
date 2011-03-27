<!-- header.tpl -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>phpRaider</title>
		{menu_init css="$site_url/templates/$template/style/menu.css"}
		<link rel="stylesheet" type="text/css" href="{$site_url}/templates/{$template}/style/stylesheet.css">
		<link rel="stylesheet" type="text/css" href="{$site_url}/templates/{$template}/style/ajax.css">
		<link rel="stylesheet" type="text/css" href="{$site_url}//templates/{$template}/style/jquery/jquery-ui.css">
		{$javascript}
	</head>
	<body>
	<!-- included for tooltips to work properly -->
	{$tooltip}
	<div align="center">
		<div id="bodyContainer">
		<div style="text-align:left">
			<table width="100%">
				<tr valign="top">
				  <td width="50%"><div align="left"><img src="{$site_url}/templates/{$template}/images/phpRaider_logo.png" border="0" alt="phpRaider - Raid management made easy!"></div></td>
					<td width="50%">
						<div align="right">
							<div style="font-size:12px; text-align:right; padding: 5px;">{$user_info}</div>
						</div>
				  </td>
				</tr>
		  </table>
		</div>
		<br>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td>{menu data=$menu}</td>
			</tr>
		</table>
		<br>