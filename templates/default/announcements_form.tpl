<form method="post" action="index.php?option=com_announcements&task={$task}">
<div class="contentHeader">{$header}</div><br />
	<div align="center">
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$titleText}:</div>
			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="announcement_title" value="{$announcement_title|escape}"> 
						{validate id="title" message="<span class=formError>$titleError</span>"}
					</div>
			  </td>
			</tr>
			<tr>
				<td valign="top" class="name_class">
					<div align="right">{$messageText}:</div>
			  </td>
				<td valign="top" class="field_class">
					<div align="left"> 
						{validate id="message" message="<span class=formError>$messageError</span>"}
						<textarea name="announcement_msg" cols="50" rows="10">{$announcement_msg|escape}</textarea>
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