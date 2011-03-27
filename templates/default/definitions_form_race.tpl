<form method="post" action="index.php?option=com_definitions&task={$task}&mode={$mode}">
<div class="contentHeader">{$raceHeader}</div><br />
	<div align="center">
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$raceNameText}:</div>
			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="name" value="{$name|escape}"> 
						{validate id="name" message="<span class=formError>$raceNameError</span>"}
					</div>
			  </td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$restrictionsText}:</div>
			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
					 	<select name="restrictions[]" multiple>
					    	{$rest_option}
						</select>
					</div>
			  </td>
			</tr>
	  </table>
	</div>
<br />
<div class="button"> 
	<input type="submit" class="mainoption" value="{$submit}" /> <input type="reset" class="liteoption" value="{$reset}" /> 
</div>
</form>   