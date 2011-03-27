<form method="post" action="install.php?option=4">
<div class="contentHeader">phpRaider Installation - Database Configuration</div><br>
<div class="contentBody">
	<div align="center">{$error}
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">Hostname:</div>
				</td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" name="pConfig_db_server" value="{$pConfig_db_server|escape}">
						{validate id="hostname" message="<span class=formError>Hostname must be entered</span>"}
					</div>
				</td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">Database name :</div>
				</td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" name="pConfig_db_name" value="{$pConfig_db_name|escape}">
						{validate id="name" message="<span class=formError>Server must be entered</span>"}
					</div>
				</td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">Username:</div>
				</td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" name="pConfig_db_user" value="{$pConfig_db_user|escape}">
						{validate id="username" message="<span class=formError>Username must be entered</span>"}
					</div>
				</td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">Password:</div>
			  </td>
				<td width="75%" valign="top" class="field_class">
				  <div align="left">
						<input type="password" class="post" name="pConfig_db_pass" value="{$pConfig_db_pass|escape}">
					</div>
			  </td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">Table prefix:</div>
			  </td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="text" class="post" name="pConfig_db_prefix" value="{$pConfig_db_prefix|escape}">
						{validate id="prefix" message="<span class=formError>Prefix must be entered</span>"}
					</div>
			  </td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">Persistent connection:</div>
				</td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="checkbox" class="post" name="pConfig_db_pers"{if $pConfig_db_pers} checked="checked"{/if}>
					</div>
				</td>
			</tr>
			<tr>
				<td width="25%" valign="top" class="name_class">
					<div align="right">Non shared connection:</div>
				</td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<input type="checkbox" class="post" name="pConfig_db_newlink"{if $pConfig_db_newlink} checked="checked"{/if}>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>
<br>
<div align="center" class="contentBody">
	<div class="button">
		<input type="submit" class="mainoption" value="Submit"> <input type="reset" class="liteoption" value="Reset">
	</div>
</div>
</form>