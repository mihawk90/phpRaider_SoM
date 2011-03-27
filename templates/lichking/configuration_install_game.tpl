<div class="contentHeader">{$header}</div><br />
<div class="contentBody">{if isset($zip_support)}
<form method="post" enctype="multipart/form-data" action="index.php?option=com_configuration&task={$task}">
<div align="center">{$game}</div><br />
	<div align="center">
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$fileName_text}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left"><input type="file" class="mainoption" name="game_file" /></div>			  </td>
			</tr>
	  </table>
	</div>
<br />
<div class="button">
	<input type="submit" class="mainoption" value="{$submit}" /> <input type="reset" class="liteoption" value="{$reset}" />
</div>
</form>
{else}
{if isset($zip_disabled)}
<div align="center">{$zip_disabled}</div><br />
{/if}
<div align="center" style="text-align:left; width:600px">{$manual_installation}</div><br />
{/if}</div>