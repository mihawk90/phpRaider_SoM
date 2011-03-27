<form method="POST" action="index.php?option=com_raids&task=delete" onSubmit="return display_confirm('{$confirm_delete}')">
<div class="contentHeader">{$new_header}</div><br />
{$new}<br />
<div style="text-align:right">
	<input type="image" src="templates/{$template}/images/icons/icon_delete.png">
</div>
<br />
<div class="contentHeader">{$old_header}</div><br />
{$old}<br />
<div style="text-align:right">
	<input type="image" src="templates/{$template}/images/icons/icon_delete.png">
</div>
<br />
</form>