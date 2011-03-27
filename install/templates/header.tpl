<!-- header.tpl -->
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>phpRaider</title>
		<link rel="stylesheet" type="text/css" href="../templates/default/style/stylesheet.css">
		<link rel="stylesheet" type="text/css" href="../templates/default/style/class.css">
{foreach name='javascripts' from=$javascripts item='js'}
		<script type="text/javascript" language="javascript" src="{$js}"></script>
{/foreach}		{$javascript}
	</head>
	<body>
	<!-- included for tooltips to work properly -->
	{$tooltip}
	<div align="center">
		<div id="bodyContainer" style="text-align:left">
		<div style="text-align:left">
			<table width="100%">
				<tr valign="top">
				  <td width="50%"><div align="left"><img src="../templates/default/images/phpRaider_logo.png" border="0" alt="phpRaider - Raid management made easy!"></div></td>
					<td width="50%">
				  </td>
				</tr>
		  </table>
		</div>
		<br>