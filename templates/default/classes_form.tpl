<form method="post" action="index.php?option=com_classes&task={$task}">
<div class="contentHeader">{$header}</div><br />
<div class="contentBody">
	<div align="center">
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$nameText}:</div>
			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="class_name" value="{$class_name|escape}"> 
						{validate id="name" message="<span class=formError>$nameError</span>"}
					</div>
			  </td>
			</tr>
	  </table>
	</div>
</div>
<br />
<div align="center" class="contentBody">
<div class="button"> 
	<input type="submit" class="mainoption" value="{$submit}" /> <input type="reset" class="liteoption" value="{$reset}" /> </div>
</div>
</form>   