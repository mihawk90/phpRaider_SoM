{capture assign="attributes"}
	<table width="100%" cellpadding="3" cellspacing="1" border="0">
	{section name=atts loop=$a_data}
			<tr>
				<td width="25%" align="right" valign="top" class="name_class">{$a_data[atts].text}</td>
				<td width="75%" align="left" valign="top" class="field_class">{$a_data[atts].field}{validate id="`$a_data[atts].name`" message="<span class=formError>`$a_data[atts].errortext`</span>"}</td>
			</tr>
	{/section}
	</table>
{/capture}