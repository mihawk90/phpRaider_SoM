<form method="post" action="index.php?option=com_register">
<div class="contentHeader">{$header}</div><br />
	<div align="center">
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$usernameText}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="username" value="{$username|escape}"> 
						{validate id="username" message="<span class=formError>$usernameError</span>"}					</div>				</td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$passwordText}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="password" class="post" style="width: 200px" name="password" value="{$password|escape}"> 
						{validate id="password" message="<span class=formError>$passwordError</span>"}					</div>				</td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$password2Text}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="password" class="post" style="width: 200px" name="password2" value="{$password2|escape}"> 
						{validate id="match" message="<span class=formError>$password2Error</span>"}					</div>				</td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$emailText}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="email" value="{$email|escape}"> 
						{validate id="email" message="<span class=formError>$emailError</span>"}					</div>				</td>
			</tr>
	  </table>
	</div>
<br />
<div class="button"> 
	<input type="submit" class="mainoption" value="{$submit}" /> <input type="reset" class="liteoption" value="{$reset}" />
</div>
</form>   