<form method="POST" action="index.php?option=com_raids&task=delete" onSubmit="return display_confirm('{$confirm_delete}')">
<div class="contentHeader">{$new_header}</div><br />
<div class="contentBody">{$new}</div><br />
<div style="text-align:right">
	<input type="image" src="templates/{$template}/images/icons/icon_delete.png">
</div>
<br />
<div class="contentHeader">{$old_header}</div><br />
<div class="contentBody">{$old}</div><br />
<div style="text-align:right">
	<input type="image" src="templates/{$template}/images/icons/icon_delete.png">
</div>
<br />
</form>