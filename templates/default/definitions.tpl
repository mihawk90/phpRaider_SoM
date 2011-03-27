<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr valign="top">
	  <td width="47%">
	  	<div class="contentHeader">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="50%" align="left"><strong>{$races_header}</strong></td>
					<td align="right"><a href="index.php?option=com_definitions&amp;task=new&amp;mode=race">{$create_new}</a></td>
				</tr>
			</table>
		</div>
	  </td>
	  <td width="6%">&nbsp;</td>
		<td width="47%">
			<div class="contentHeader">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="46%"><div align="left"><strong>{$classes_header}</strong></div></td>
					<td><div align="right"><a href="index.php?option=com_definitions&amp;task=new&amp;mode=class">{$create_new}</a></div></td>
				</tr>
			</table>
			</div>
		</td>
	</tr>
	<tr valign="top">
	  <td width="47%">
	  	<form method="POST" action="index.php?option=com_definitions&amp;task=delete&amp;mode=race" onSubmit="return display_confirm('{$confirm_delete}')"><br />{$races}<br />
<div style="text-align:right">
	<input type="image" src="templates/{$template}/images/icons/icon_delete.png">
</div></form>
	  </td>
	  <td width="6%">&nbsp;</td>
	  <td width="47%">
	  	<form method="POST" action="index.php?option=com_definitions&amp;task=delete&amp;mode=class" onSubmit="return display_confirm('{$confirm_delete}')">
			<br />{$classes}<br />
<div style="text-align:right">
	<input type="image" src="templates/{$template}/images/icons/icon_delete.png">
</div></form>
	  </td>
  </tr>
</table>
