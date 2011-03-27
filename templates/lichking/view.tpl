
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="contentRaid">
  <tr>
    <td colspan="4"><div class="contentHeader">{$header}</div></td>
  </tr>
  <tr>
    <td width="70%" colspan="4"><div class="contentHeader">{$description}</div></td>
  </tr>
  <tr>
    <td colspan="4"><div class="subcontentHeader">{$timeUntil}</div></td>
  </tr>
  <tr>
    <td width="25%"><div align="right" class="raidcontent"><b>{$leaderText}</b>:</div></td>
    <td width="25%"><div align="left" class="raidcontent"><span class="leader">{$leader}</span></div></td>
    <td width="25%"><div align="right" class="raidcontent"><b>{$approvedText}</b>:</div></td>
    <td width="25%"><div align="left" class="raidcontent">{$approvedCount}</div></td>
  </tr>
  <tr>
    <td width="25%"><div align="right" class="raidcontent"><b>{$inviteText}</b>:</div></td>
    <td width="25%"><div align="left" class="raidcontent">{$invite_time}</div></td>
    <td width="25%"><div align="right" class="raidcontent"><b>{$queuedText}</b>:</div></td>
    <td width="25%"><div align="left" class="raidcontent">{$queuedCount}</div></td>
  </tr>
  <tr>
    <td width="25%"><div align="right" class="raidcontent"><b>{$startText}</b>:</div></td>
    <td width="25%"><div align="left" class="raidcontent">{$start_time}</div></td>
    <td width="25%"><div align="right" class="raidcontent"><b>{$cancelledText}</b>:</div></td>
    <td width="25%"><div align="left" class="raidcontent">{$cancelledCount}</div></td>
  </tr>
  <tr>
    <td width="25%"><div align="right" class="raidcontent"><b>{$minLevelText}</b>:</div></td>
    <td width="25%"><div align="left" class="raidcontent">{$min_level}</div></td>
    <td width="25%"><div align="right" class="raidcontent"><b>{$maxText}</b>:</div></td>
    <td width="25%"><div align="left" class="raidcontent">{$maxCount}</div></td>
  </tr>
  <tr>
    <td width="25%"><div align="right" class="raidcontent"><b>{$maxLevelText}</b>:</div></td>
    <td width="25%"><div align="left" class="raidcontent">{$max_level}</div></td>
    <td width="25%"><div align="right" class="raidcontent"><b>Your Status</b>:</div></td>
    <td width="25%"><div align="left" class="raidcontent">{$signupIcon}</div></td>
  </tr>
</table>
<br />
<table width="100%" cellpadding="0" cellspacing="0" border="0" >
	<tr valign="top">
	  <td><div class="contentHeader"><img src="templates/{$template}/images/icons/icon_signed_up.png" /> {$approved}</div></td>
  </tr>
	<tr valign="top">
	  <td>&nbsp;</td>
  </tr>
	<tr valign="top">
	  <td><div>{$approved_signups}</div></td>
  </tr>
	<tr valign="top">
	  <td>&nbsp;</td>
  </tr>
	<tr valign="top">
		<td width="70%"><div class="contentHeader"><img src="templates/{$template}/images/icons/icon_queue.png" /> {$queued}
		</div></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
    </tr>
	<tr valign="top">
		<td width="70%">
			<div>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td><div>{$queued_signups}</div></td>
					</tr>
				</table>
			</div>		</td>
	</tr>
	<tr valign="top">
	  <td>&nbsp;</td>
  </tr>
	<tr valign="top">
	  <td><div class="contentHeader"> <img src="templates/{$template}/images/icons/icon_cancel.png" /> {$cancelled} </div></td>
  </tr>
	<tr valign="top">
	  <td>&nbsp;</td>
  </tr>
	<tr valign="top">
	  <td><div>{$cancelled_signups}</div></td>
  </tr>
</table>
