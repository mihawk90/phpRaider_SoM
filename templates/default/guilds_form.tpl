<form method="post" action="index.php?option=com_guilds&task={$task}">
<div class="contentHeader">{$header}</div><br />
	<div align="center">
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$nameText}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="guild_name" value="{$guild_name|escape}"> 
						{validate id="name" message="<span class=formError>$nameError</span>"}					</div>				</td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$tagText}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="guild_tag" value="{$guild_tag|escape}"> 
						{validate id="tag" message="<span class=formError>$tagError</span>"}					</div>				</td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$masterText}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="guild_master" value="{$guild_master|escape}"> 
						{validate id="master" message="<span class=formError>$masterError</span>"}					</div>				</td>
			</tr>
	  </table>
	</div>
<br />
<div class="button"> 
	<input type="submit" class="mainoption" value="{$submit}" /> <input type="reset" class="liteoption" value="{$reset}" />
</div>
</form>   