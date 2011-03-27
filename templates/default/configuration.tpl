<form method="post" action="index.php?option=com_configuration">
<div class="contentHeader">{$update_header}</div>
<br>
<div align="center">{$update_check}</div><br>
<div class="contentHeader">{$game_header}</div>
<br>
	<div align="center">
		<table width="100%" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<td width="25%" valign="top" class="name_class"><div align="right">{$gaGame_text}:</div></td>
				<td width="75%" valign="top" class="field_class"><div align="left"><select name="pConfig_game" class="post">{$games}</select>
				{$installGame_text}</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$gaMinLvl_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input name="pConfig_min_level" type="text" class="post" id="pConfig_min_level" style="width: 30px" value="{$pConfig_min_level|escape}">
				{validate id="min_level" message="<span class="formError">$minLevelError</span>"}</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$gaMaxLvl_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input name="pConfig_max_level" type="text" class="post" id="pConfig_max_level" style="width: 30px" value="{$pConfig_max_level|escape}">
				{validate id="max_level" message="<span class="formError">$maxLevelError</span>"}</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$gaMinRaiders_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input name="pConfig_min_raiders" type="text" class="post" id="pConfig_min_raiders" style="width: 30px" value="{$pConfig_min_raiders|escape}">
				{validate id="min_raiders" message="<span class="formError">$minRaidersError</span>"}</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$gaMaxRaiders_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input name="pConfig_max_raiders" type="text" class="post" id="pConfig_max_raiders" style="width: 30px" value="{$pConfig_max_raiders|escape}">
				{validate id="max_raiders" message="<span class="formError">$maxRaidersError</span>"}</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$gaMultiClass_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
				<input type="checkbox" class="post" value="1" name="pConfig_multi_class" onfocus="blur();"{if $pConfig_multi_class} checked="checked"{/if}></div></td>
			</tr>
		</table>
	</div><br>
<div class="contentHeader">{$site_header}</div>
<br>
	<div align="center">
		<table width="100%" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<td width="25%" valign="top" class="name_class"><div align="right">{$siLanguage_text}:</div></td>
				<td width="75%" valign="top" class="field_class"><div align="left">
					<select name="pConfig_language" class="post">{$language}</select>
				</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$siTemplate_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<select name="pConfig_template" class="post">{$templates}</select>
				</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$siFirstDayOfWeek_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<select name="pConfig_first_day_of_week" class="post">{$firstdayofweek}</select>
				</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$siDateFormat_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input name="pConfig_date_format" type="text" class="post" id="pConfig_date_format" style="width: 200px" value="{$pConfig_date_format|escape}">
				{validate id="date_format" message="<span class="formError">$dateError</span>"}</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$siTimeFormat_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input name="pConfig_time_format" type="text" class="post" id="pConfig_time_format" style="width: 200px" value="{$pConfig_time_format|escape}">
				{validate id="time_format" message="<span class="formError">$timeError</span>"}</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$siTimezone_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<select name="pConfig_timezone">{$timezone}</select>
					<font color="red">{$siCurrentTime} GMT</font>
				</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$siDst_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input type="checkbox" class="post" value="100" name="pConfig_dst" onfocus="blur();"{if $pConfig_dst} checked="checked"{/if}>
					<font color="red">{$siSetTime} {$siLocalText}</font>
				</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$siAdmin_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input name="pConfig_admin_name" type="text" class="post" id="pConfig_admin_name" style="width: 200px" value="{$pConfig_admin_name|escape}">
				{validate id="admin" message="<span class="formError">$adminError</span>"}</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$siAdminEmail_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input name="pConfig_admin_email" type="text" class="post" id="pConfig_admin_email" style="width: 200px" value="{$pConfig_admin_email|escape}">
				{validate id="admin_email" message="<span class="formError">$adminEmailError</span>"}</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$siURL_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input name="pConfig_site_url" type="text" class="post" id="pConfig_site_url" style="width: 200px" value="{$pConfig_site_url|escape}">
				{validate id="site_url" message="$urlError"}</div></td>
			</tr>
		</table>
	</div>
<br>
<div class="contentHeader">{$misc_header}</div>
<br>
	<div align="center">
		<table width="100%" cellpadding="3" cellspacing="0" border="0">
			<tr>
				<td valign="top" class="name_class"><div align="right">{$miDefaultGroup_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<select name="pConfig_default_group" class="post">{$group}</select>
				</div></td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class"><div align="right">{$miAnon_text}:</div></td>
				<td width="75%" valign="top" class="field_class"><div align="left">
					<input type="checkbox" class="post" value="1" name="pConfig_allow_anonymous" onfocus="blur();"{if $pConfig_allow_anonymous} checked="checked"{/if}>
				</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$miQueue_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input type="checkbox" class="post" value="1" name="pConfig_auto_queue" onfocus="blur();"{if $pConfig_auto_queue} checked="checked"{/if}>
				</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$miDebug_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input type="checkbox" class="post" value="1" name="pConfig_debug_mode" onfocus="blur();"{if $pConfig_debug_mode} checked="checked"{/if}>
				</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$miDisable_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input type="checkbox" class="post" value="1" name="pConfig_disable_site" onfocus="blur();"{if $pConfig_disable_site} checked="checked"{/if}>
				</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$miFreeze_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input type="checkbox" class="post" value="1" name="pConfig_disable_freeze" onfocus="blur();"{if $pConfig_disable_freeze} checked="checked"{/if}>
				</div></td>
			</tr>
			<tr>
				<td valign="top" class="name_class"><div align="right">{$miReport_text}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input name="pConfig_report_max" type="text" class="post" id="pConfig_report_max" style="width: 30px" value="{$pConfig_report_max|escape}">
				{validate id="report_max" message="$reportError"}</div></td>
			</tr>
		</table>
	</div>
<br>
<div class="button">
	<input type="submit" class="mainoption" value="{$submit}"> <input type="reset" class="liteoption" value="{$reset}">
</div>
</form>