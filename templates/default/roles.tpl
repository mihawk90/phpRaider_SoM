<form method="POST" action="index.php?option=com_roles&task=delete" onSubmit="return display_confirm('{$confirm_delete}')">
<div class="contentHeader">{$header}</div><br />
{$output}<br />
<div style="text-align:right">
	<input type="image" src="templates/{$template}/images/icons/icon_delete.png">
</div>
</form>
<div style="text-align:right">
	<a href="index.php?option=com_roles&task=new">{$create_new}</a>
</div>