<form method="post" action="index.php?option=com_profile">
<div class="contentHeader">{$header}</div><br>
	<div class="contentBody" align="center">
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
		    <tr>
              <td valign="top" class="name_class"><div align="right">{$emailText}:</div></td>
		      <td valign="top" class="field_class"><div align="left">
                  <input type="text" class="post" style="width: 200px" name="user_email" value="{$user_email|escape}">
		        {validate id="email" message="<span class="formError">$emailError</span>"} </div></td>
	      </tr>
		    <tr>
              <td valign="top" class="name_class"><div align="right">{$newPasswordText}:</div></td>
		      <td valign="top" class="field_class"><input type="password" class="post" style="width: 200px" name="new_password" value="{$new_password|escape}"></td>
	      </tr>
		    <tr>
              <td valign="top" class="name_class"><div align="right">{$confirmPasswordText}:</div></td>
		      <td valign="top" class="field_class"><input type="password" class="post" style="width: 200px" name="confirm_password" value="{$confirm_password|escape}">
		        {$confirmPasswordError}</td>
	      </tr>
			<tr>
				<td width="35%" valign="top" class="name_class">
					<div align="right">{$enterPasswordText}:</div></td>
				<td width="65%" valign="top" class="field_class"><input type="password" class="post" style="width: 200px" name="enter_password" value="{$enter_password|escape}">
						{$enterPasswordError}</td>
			</tr>
	  </table>
	</div>
<br>
<div class="button">
	<input type="submit" class="mainoption" value="{$submit}"> <input type="reset" class="liteoption" value="{$reset}">
</div>
</form>