<form name="new_raid" method="post" action="index.php?option=com_raids&amp;task={$task}">
<div class="contentHeader">{$generic_header}</div>
<br />
	<div class="contentBody" align="center">
		<table width="100%" cellpadding="3" cellspacing="1" border="0">
			<tr>
				<td width="25%" align="right" valign="top" class="name_class">
					<div align="right">{$templateText}:</div>
				</td>
				<td width="75%" valign="top" class="field_class">
					<div align="left">
						<select name="raid_template" onchange="MM_jumpMenu('self',this,0)">
							{$templates}
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td align="right" valign="top" class="name_class"><div align="right">{$locationText}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input name="location" style="width:125px" type="text" class="post" id="location" value="{$location|escape}">{validate id="location" message="<span class=formError>$locationError</span>"}
				</div></td>
			</tr>
			<tr>
				<td align="right" valign="top" class="name_class"><div align="right">{$dateText}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input name="date" style="width:125px" type="text" class="post" id="date" value="{$date|escape}"> {validate id="date" message="<span class=formError>$dateError</span>"}
				</div></td>
			</tr>
			<tr>
				<td align="right" valign="top" class="name_class"><div align="right">{$inviteText}:</div></td>
				<td valign="top" class="field_class">
					<select name="invite_time_hour" id="invite_time_hour">
						<option value="00" {if $invite_time_hour == '00'} selected{/if}>00</option>
						<option value="01" {if $invite_time_hour == '01'} selected{/if}>01</option>
						<option value="02" {if $invite_time_hour == '02'} selected{/if}>02</option>
						<option value="03" {if $invite_time_hour == '03'} selected{/if}>03</option>
						<option value="04" {if $invite_time_hour == '04'} selected{/if}>04</option>
						<option value="05" {if $invite_time_hour == '05'} selected{/if}>05</option>
						<option value="06" {if $invite_time_hour == '06'} selected{/if}>06</option>
						<option value="07" {if $invite_time_hour == '07'} selected{/if}>07</option>
						<option value="08" {if $invite_time_hour == '08'} selected{/if}>08</option>
						<option value="09" {if $invite_time_hour == '09'} selected{/if}>09</option>
						<option value="10" {if $invite_time_hour == '10'} selected{/if}>10</option>
						<option value="11" {if $invite_time_hour == '11'} selected{/if}>11</option>
						<option value="12" {if $invite_time_hour == '12'} selected{/if}>12</option>
						<option value="13" {if $invite_time_hour == '13'} selected{/if}>13</option>
						<option value="14" {if $invite_time_hour == '14'} selected{/if}>14</option>
						<option value="15" {if $invite_time_hour == '15'} selected{/if}>15</option>
						<option value="16" {if $invite_time_hour == '16'} selected{/if}>16</option>
						<option value="17" {if $invite_time_hour == '17'} selected{/if}>17</option>
						<option value="18" {if $invite_time_hour == '18'} selected{/if}>18</option>
						<option value="19" {if $invite_time_hour == '19'} selected{/if}>19</option>
						<option value="20" {if $invite_time_hour == '20'} selected{/if}>20</option>
						<option value="21" {if $invite_time_hour == '21'} selected{/if}>21</option>
						<option value="22" {if $invite_time_hour == '22'} selected{/if}>22</option>
						<option value="23" {if $invite_time_hour == '23'} selected{/if}>23</option>
					</select>
					<select name="invite_time_minute" id="invite_time_minute">
						<option value="00" {if $invite_time_minute == '00'} selected{/if}>00</option>
						<option value="05" {if $invite_time_minute == '05'} selected{/if}>05</option>
						<option value="10" {if $invite_time_minute == '10'} selected{/if}>10</option>
						<option value="15" {if $invite_time_minute == '15'} selected{/if}>15</option>
						<option value="20" {if $invite_time_minute == '20'} selected{/if}>20</option>
						<option value="25" {if $invite_time_minute == '25'} selected{/if}>25</option>
						<option value="30" {if $invite_time_minute == '30'} selected{/if}>30</option>
						<option value="35" {if $invite_time_minute == '35'} selected{/if}>35</option>
						<option value="40" {if $invite_time_minute == '40'} selected{/if}>40</option>
						<option value="45" {if $invite_time_minute == '45'} selected{/if}>45</option>
						<option value="50" {if $invite_time_minute == '50'} selected{/if}>50</option>
						<option value="55" {if $invite_time_minute == '55'} selected{/if}>55</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right" valign="top" class="name_class"><div align="right">{$startText}:</div></td>
				<td valign="top" class="field_class">
					<select name="start_time_hour" id="start_time_hour">
						<option value="00" {if $start_time_hour == '00'} selected{/if}>00</option>
						<option value="01" {if $start_time_hour == '01'} selected{/if}>01</option>
						<option value="02" {if $start_time_hour == '02'} selected{/if}>02</option>
						<option value="03" {if $start_time_hour == '03'} selected{/if}>03</option>
						<option value="04" {if $start_time_hour == '04'} selected{/if}>04</option>
						<option value="05" {if $start_time_hour == '05'} selected{/if}>05</option>
						<option value="06" {if $start_time_hour == '06'} selected{/if}>06</option>
						<option value="07" {if $start_time_hour == '07'} selected{/if}>07</option>
						<option value="08" {if $start_time_hour == '08'} selected{/if}>08</option>
						<option value="09" {if $start_time_hour == '09'} selected{/if}>09</option>
						<option value="10" {if $start_time_hour == '10'} selected{/if}>10</option>
						<option value="11" {if $start_time_hour == '11'} selected{/if}>11</option>
						<option value="12" {if $start_time_hour == '12'} selected{/if}>12</option>
						<option value="13" {if $start_time_hour == '13'} selected{/if}>13</option>
						<option value="14" {if $start_time_hour == '14'} selected{/if}>14</option>
						<option value="15" {if $start_time_hour == '15'} selected{/if}>15</option>
						<option value="16" {if $start_time_hour == '16'} selected{/if}>16</option>
						<option value="17" {if $start_time_hour == '17'} selected{/if}>17</option>
						<option value="18" {if $start_time_hour == '18'} selected{/if}>18</option>
						<option value="19" {if $start_time_hour == '19'} selected{/if}>19</option>
						<option value="20" {if $start_time_hour == '20'} selected{/if}>20</option>
						<option value="21" {if $start_time_hour == '21'} selected{/if}>21</option>
						<option value="22" {if $start_time_hour == '22'} selected{/if}>22</option>
						<option value="23" {if $start_time_hour == '23'} selected{/if}>23</option>
					</select>
					<select name="start_time_minute" id="start_time_minute">
						<option value="00" {if $start_time_minute == '00'} selected{/if}>00</option>
						<option value="05" {if $start_time_minute == '05'} selected{/if}>05</option>
						<option value="10" {if $start_time_minute == '10'} selected{/if}>10</option>
						<option value="15" {if $start_time_minute == '15'} selected{/if}>15</option>
						<option value="20" {if $start_time_minute == '20'} selected{/if}>20</option>
						<option value="25" {if $start_time_minute == '25'} selected{/if}>25</option>
						<option value="30" {if $start_time_minute == '30'} selected{/if}>30</option>
						<option value="35" {if $start_time_minute == '35'} selected{/if}>35</option>
						<option value="40" {if $start_time_minute == '40'} selected{/if}>40</option>
						<option value="45" {if $start_time_minute == '45'} selected{/if}>45</option>
						<option value="50" {if $start_time_minute == '50'} selected{/if}>50</option>
						<option value="55" {if $start_time_minute == '55'} selected{/if}>55</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right" valign="top" class="name_class"><div align="right">{$freezeText}:</div></td>
				<td valign="top" class="field_class">
					<select name="freeze_time" id="freeze_time">
						<option value="00" {if $freeze_time == '00'} selected{/if}>00</option>
						<option value="01" {if $freeze_time == '01'} selected{/if}>01</option>
						<option value="02" {if $freeze_time == '02'} selected{/if}>02</option>
						<option value="03" {if $freeze_time == '03'} selected{/if}>03</option>
						<option value="04" {if $freeze_time == '04'} selected{/if}>04</option>
						<option value="05" {if $freeze_time == '05'} selected{/if}>05</option>
						<option value="06" {if $freeze_time == '06'} selected{/if}>06</option>
						<option value="07" {if $freeze_time == '07'} selected{/if}>07</option>
						<option value="08" {if $freeze_time == '08'} selected{/if}>08</option>
						<option value="09" {if $freeze_time == '09'} selected{/if}>09</option>
						<option value="10" {if $freeze_time == '10'} selected{/if}>10</option>
						<option value="11" {if $freeze_time == '11'} selected{/if}>11</option>
						<option value="12" {if $freeze_time == '12'} selected{/if}>12</option>
						<option value="13" {if $freeze_time == '13'} selected{/if}>13</option>
						<option value="14" {if $freeze_time == '14'} selected{/if}>14</option>
						<option value="15" {if $freeze_time == '15'} selected{/if}>15</option>
						<option value="16" {if $freeze_time == '16'} selected{/if}>16</option>
						<option value="17" {if $freeze_time == '17'} selected{/if}>17</option>
						<option value="18" {if $freeze_time == '18'} selected{/if}>18</option>
						<option value="19" {if $freeze_time == '19'} selected{/if}>19</option>
						<option value="20" {if $freeze_time == '20'} selected{/if}>20</option>
						<option value="21" {if $freeze_time == '21'} selected{/if}>21</option>
						<option value="22" {if $freeze_time == '22'} selected{/if}>22</option>
						<option value="23" {if $freeze_time == '23'} selected{/if}>23</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right" valign="top" class="name_class"><div align="right">{$descriptionText}:</div></td>
				<td valign="top" class="field_class">{validate id="description" message="<span class=formError>$descriptionError</span>"}
					<textarea name="description" cols="50" rows="10" id="description">{$description|escape}</textarea>
				</td>
			</tr>
			<tr>
				<td align="right" valign="top" class="name_class"><div align="right">{$iconText}:</div></td>
				<td valign="top" class="field_class"><select name="icon_name" id="icon_name">{$icons}</select></td>
			</tr>
			<tr>
				<td align="right" valign="top" class="name_class"><div align="right">{$minText}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input name="minimum_level" style="width:30px" type="text" class="post" id="minimum_level" value="{$minimum_level|escape}">{validate id="minimum_level" message="<span class=formError>$minError</span>"}
				</div></td>
			</tr>
			<tr>
				<td align="right" valign="top" class="name_class"><div align="right">{$maxText}:</div></td>
				<td valign="top" class="field_class"><div align="left">
					<input name="maximum_level" style="width:30px" type="text" class="post" id="maximum_level" value="{$maximum_level|escape}">{validate id="maximum_level" message="<span class=formError>$maxError</span>"}
				</div></td>
			</tr>
			<tr>
				<td align="right" valign="top" class="name_class"><div align="right">{$raidersText}:</div></td>
				<td valign="top" class="field_class"><div align="left">
				    <input name="maximum" style="width:30px" type="text" class="post" id="raiders" value="{$maximum|escape}">{validate id="maximum" message="<span class=formError>$maximumError</span>"}
				</div></td>
			</tr>
			<tr>
				<td align="right" valign="top" class="name_class">{$saveText}</td>
				<td valign="top" class="field_class"><input name="save" type="checkbox" id="save" value="1" onfocus="blur();"{if $save} checked="checked"{/if}></td>
			</tr>
		</table>
	</div>
<br />
<div class="contentHeader">{$limits_header}</div><br>
<div class="contentBody">{$class_limits}</div><br>
<div class="button">
	<input type="submit" class="mainoption" value="{$submit}" /> <input type="reset" class="liteoption" value="{$reset}" />
</div>
</form>
