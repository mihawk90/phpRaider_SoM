<form method="post" action="index.php?option=com_password">
<div class="contentHeader">{$header}</div><br />
	<div align="center">
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
		    <tr>
              <td width="35%" valign="top" class="name_class"><div align="right">{$emailText}:</div></td>
		      <td width="65%" valign="top" class="field_class"><div align="left">
                  <input type="text" class="post" style="width: 200px" name="user_email" value="{$user_email|escape}" />
				  {validate id="email" message="<span class=formError>$emailError</span>"}
			 </div></td>
	      </tr>
	  </table>
	</div>
{$error}
<br />
<div class="button"> 
	<input type="submit" class="mainoption" value="{$submit}" /> <input type="reset" class="liteoption" value="{$reset}" />
</div>
</form>   