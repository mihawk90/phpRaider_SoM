<form method="POST" action="index.php?option=com_templates&task=delete" onSubmit="return display_confirm('{$confirm_delete}')">
<div class="contentHeader">{$header}</div><br />
{$output}<br />
<div style="text-align:right">
	<input type="image" src="templates/{$template}/images/icons/icon_delete.png">
</div>
</form>