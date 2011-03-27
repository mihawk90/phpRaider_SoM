<form method="POST" action="index.php?option=com_characters&amp;task=delete" onSubmit="return display_confirm('{$confirm_delete}')">
<div class="contentHeader">{$header}</div><br />
<div class="contentBody">{$output}</div><br />
<div style="text-align:right">
	<input type="image" src="templates/{$template}/images/icons/icon_delete.png">
</div>
</form>
<div align="right"><a href="index.php?option=com_characters&task=new">{$create_new}</a></div>