<form method="post" action="index.php?option=com_login&task={$task}">
<div class="contentHeader">{$lHeader}</div><br><font color="red"><strong>{$invalid_login}</strong></font>
	<div align="center">
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$usernameText}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="username" value="{$username|escape}"> 
						{validate id="username" message="<span class=formError>$usernameError</span>"}					</div>			  </td>
			</tr>
			<tr>
				<td valign="top" class="name_class">
					<div align="right">{$passwordText}:</div>			  </td>
				<td valign="top" class="field_class">
					<div align="left"> 
						<input type="password" class="post" style="width: 200px" name="password" value="{$password|escape}" />
						{validate id="password" message="<span class=formError>$passwordError</span>"}					</div>			  </td>
			</tr>
			<tr>
			  <td align="right" valign="top" class="name_class">{$rememberText}</td>
			  <td valign="top" class="field_class"><input type="checkbox" name="autologin" value="checkbox" checked/></td>
		  </tr>
	  </table>
	</div>
<br />
<div class="button"> 
	<input type="submit" class="mainoption" value="{$submit}" /> <input type="reset" class="liteoption" value="{$reset}" />
</div>
</form>   