<div class="contentHeader">{$header}</div><br>
<div align="left">{$description}</div><br>
<div align="center">{$timeUntil}</div><br>
<div align="center">
	<table width="90%" cellpadding="3" cellspacing="0">
		<tr>
			<td width="22%"><div align="right"><b>{$leaderText}</b>:</div><td>
			<td width="22%"><div align="left">{$leader}</div></td>
			<td width="12%">&nbsp;</td>
			<td width="22%"><div align="right"><b>{$approvedText}</b>:</div></td>
			<td width="32%"><div align="left">{$approvedCount}</div></td>
		</tr>
		<tr>
			<td><div align="right"><b>{$inviteText}</b>:</div><td>
			<td><div align="left">{$invite_time}</div></td>
			<td>&nbsp;</td>
			<td><div align="right"><b>{$queuedText}</b>:</div></td>
			<td><div align="left">{$queuedCount}</div></td>
		</tr>
		<tr>
			<td><div align="right"><b>{$startText}</b>:</div><td>
			<td><div align="left">{$start_time}</div></td>
			<td>&nbsp;</td>
			<td><div align="right"><b>{$cancelledText}</b>:</div></td>
			<td><div align="left">{$cancelledCount}</div></td>
		</tr>
		<tr>
			<td><div align="right"><b>{$minLevelText}</b>:</div><td>
			<td><div align="left">{$min_level}</div></td>
			<td>&nbsp;</td>
			<td><div align="right"><b>{$maxText}</b>:</div></td>
			<td><div align="left">{$maxCount}</div></td>
		</tr>
		<tr>
			<td><div align="right"><b>{$maxLevelText}</b>:</div><td>
			<td><div align="left">{$max_level}</div></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td><div align="right"><b>{$signupText}</b>:</div><td>
			<td><div align="left">{$signupIcon}</div></td>
			<td>&nbsp;</td>
			<td>{if isset($signupOtherText)}<div align="right"><b>{$signupOtherText}</b>:</div>{else}&nbsp;{/if}</td>
			<td>{if isset($signupOtherText)}<div align="left">{$signupOtherIcon}</div>{else}&nbsp;{/if}</td>
		</tr>
	</table>
</div><br>
<div class="contentHeader"><img src="templates/{$template}/images/icons/icon_signed_up.png"> {$approved}</div><br>
<div>{$approved_signups}</div><br>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr valign="top">
		<td width="70%"><div class="contentHeader"><img src="templates/{$template}/images/icons/icon_queue.png"> {$queued}
		</div></td>
		<td width="5%">&nbsp;</td>
		<td width="25%"><div class="contentHeader" style="background-image:url('templates/{$template}/images/header_background_small.png');"><img src="templates/{$template}/images/icons/icon_cancel_signup.png"> {$cancelled}
		</div></td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr valign="top">
		<td width="70%">
			<div>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td><div align="center">{$queued_signups}</div></td>
					</tr>
				</table>
			</div>
		</td>
		<td width="5%">&nbsp;</td>
		<td width="25%"><div>{$cancelled_signups}</div></td>
	</tr>
</table>