<form method="post" action="index.php?option=com_attributes&task={$task}">
<div class="contentHeader">{$header}</div><br />
	<div class="contentBody" align="center">
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$typeText}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
					  <select name="att_type">
					    <option value="numeric" {if $att_type == numeric} selected{/if}>{$numericText}</option>
					    <option value="text" {if $att_type == text} selected{/if}>{$textText}</option>
					  </select>
					</div>			  </td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$iconText}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
					  <select name="att_icon">
					    {$attributes}
					  </select>
					</div>			  </td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$nameText}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="att_name" value="{$att_name|escape}"> 
						{validate id="name" message="<span class=formError>$nameError</span>"}					</div>			  </td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$showText}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="checkbox" class="post" value="1" name="att_show" onfocus="blur();"{if $att_show} checked="checked"{/if}> 
					</div>			  </td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$hoverText}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="checkbox" class="post" value="1" name="att_hover" onfocus="blur();"{if $att_hover} checked="checked"{/if}> 
					</div>			  </td>
			</tr>
	  </table>
	</div>
<br />
<div class="button"> 
	<input type="submit" class="mainoption" value="{$submit}" /> <input type="reset" class="liteoption" value="{$reset}" />
</div>
</form>   