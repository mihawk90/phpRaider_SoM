<form method="post" action="upgrade.php">
<div class="contentHeader">phpRaider Upgrade</div><br />
<div class="contentBody">Before upgrading be sure to BACKUP your database. Failure to do so
may result in losing all phpRaider data.</div><br>
<div class="contentBody">
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td width="25%"><div align="right">Upgrading from: </div></td>
			<td width="75%">
				<div align="left">
					<select name="upgrade_file" class="post">
{foreach name='upgrades' from=$upgrades item='version'}
						<option value="{$version}">{$version}</option>
{/foreach}					</select>
				</div>
			</td>
		</tr>
	</table>
</div>
<br />
<div align="center" class="contentBody">
<div class="button">
	<input type="submit" class="mainoption" value="Submit" /> <input type="reset" class="liteoption" value="Reset" /> </div>
</div>
</form>