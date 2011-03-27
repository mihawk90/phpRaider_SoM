{$scripts}
<form name="character_new" method="post" action="index.php?option=com_characters&amp;task={$task}">
<div class="contentHeader">{$header}</div><br>
	<div class="contentBody" align="center">
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">{$nameText}:</div>			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" style="width: 200px" name="char_name" value="{$char_name|escape}">
						{validate id="name" message="<span class=formError>$nameError</span>"} {$char_exists}					</div>			  </td>
			</tr>
			<tr>
              <td valign="top" class="name_class"><div align="right">{$raceText}:</div></td>
			  <td valign="top" class="field_class"><div align="left">
                  <select name="race_id" class="post" onChange="addItem('race_id', 'class_id' )">{$races}</select> {validate id="race" message="<span class=formError>$raceError</span>"}</div></td>
		  </tr>
			<tr>
              <td valign="top" class="name_class"><div align="right">{$classText}:</div></td>
			  <td valign="top" class="field_class"><div align="left">
  				  <input type="hidden" value="4" id="subInitial">
				  <input type="hidden" value="0" id="subNumber">
                  <select name="class_id" class="post">{$classes}<option></option></select> {$addClassText} {validate id="class" message="<span class=formError>$classError</span>"}<br>
				  <div id="subClass"> </div></div></td>
		  </tr>
			<tr>
              <td valign="top" class="name_class"><div align="right">{$guildText}:</div></td>
			  <td valign="top" class="field_class"><div align="left">
                  <select name="guild_id" class="post">{$guilds}</select></div></td>
		  </tr>
			<tr>
              <td valign="top" class="name_class"><div align="right">{$genderText}:</div></td>
			  <td valign="top" class="field_class"><div align="left">
                  <select name="gender_id" class="post">{$genders}</select></div></td>
		  </tr>
			<tr>
              <td valign="top" class="name_class"><div align="right">{$levelText}:</div></td>
			  <td valign="top" class="field_class"><div align="left">
                  <input type="text" class="post" style="width: 30px" name="char_level" value="{$char_level|escape}">
			    {validate id="level" message="<span class=formError>$levelError</span>"} </div></td>
		  </tr>
	  </table>
	</div>
<br>
<div class="contentHeader">{$attribute_header}</div><br>
{$attributes}<br>
<div class="button">
	<input type="submit" class="mainoption" value="{$submit}"> <input type="reset" class="liteoption" value="{$reset}">
</div>
</form>

<!-- this image is used for the loading of the javascript only -->
<img src="{$site_url}/templates/{$template}/images/pixel.png" onload="setupItems('subInitial', 'race_id', 'class_id' )" alt="">