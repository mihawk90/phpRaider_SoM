<form method="post" action="index.php?option=com_roles&task={$task}">
<div class="contentHeader">{$header}</div><br />
	<div align="center">
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$nameText}:</div>
			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="role_name" value="{$role_name|escape}"> 
						{validate id="name" message="<span class=formError>$nameError</span>"}
					</div>
			  </td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$headerText}:</div>
			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="header_color" value="{$header_color|escape}"> 
						{validate id="header" message="<span class=formError>$headerError</span>"}
					</div>
			  </td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$bodyText}:</div>
			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="body_color" value="{$body_color|escape}"> 
						{validate id="body" message="<span class=formError>$bodyError</span>"}
					</div>
			  </td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$fontText}:</div>
			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="font_color" value="{$font_color|escape}"> 
						{validate id="font" message="<span class=formError>$fontError</span>"}
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