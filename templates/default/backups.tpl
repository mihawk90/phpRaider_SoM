<form method="post" action="index.php?option=com_backups">
<div class="contentHeader">{$header}</div><br>
	<div align="center">
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$chooseTables}:</div>
			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<select name="tables[]" multiple size="8">
{foreach name=backupTableNames from=$tables item=table}
							<option value="{$table}">{$table}</option>
{/foreach}
						</select>
					</div>
			  </td>
			</tr>
	  </table>
	</div>
<br>
<div class="button">
	<input type="submit" class="mainoption" value="{$submit}"> <input type="reset" class="liteoption" value="{$reset}">
</div>
</form>