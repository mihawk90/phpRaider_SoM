{capture assign="class_limits"}
	<table width="100%" cellpadding="3" cellspacing="1" border="0">
{section name=limits loop=$l_data}
			<tr>
				<td width="25%" align="right" valign="top" class="name_class">{$l_data[limits].text}</td>
				<td width="75%" align="left" valign="top" class="field_class">{$l_data[limits].field}{validate id="`$l_data[limits].name`" message="<span class=formError>`$l_data[limits].errortext`</span>"}</td>
			</tr>
{/section}
	</table>
{/capture}