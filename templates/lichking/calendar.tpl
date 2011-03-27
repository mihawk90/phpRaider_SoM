<table class="month">
<tr><td class="calendarHeader" colspan="2"><a href="{$baseURL}?option=com_frontpage&amp;{$previous}"> &lt;&lt; </a></td><td class="calendarHeader" colspan="{if $showWeekNumber==true}4{else}3{/if}">{$monthNames[$currentMonth]} {$currentYear}</td><td class="calendarHeader" colspan="2"><a href="{$baseURL}?option=com_frontpage&amp;{$next}"> &gt;&gt; </a></td></tr>
<tr><td class="datepicker" colspan="{if $showWeekNumber==true}8{else}7{/if}">
<form name="datepickerform" class="datepickerform" action="{$baseURL}" method="get">
<input type="hidden" name="option" value="com_frontpage">
<select name="monthID" class="monthpicker">{foreach name=monthpicker from=$monthNames item=monthName key=monthNumber}<option value="{$monthNumber}"{if $currentMonth == $monthNumber} selected{/if}>{$monthName}</option>{/foreach}</select>
<select name="yearID" class="yearpicker">
{foreach name=yearpicker from=$years item=year}
<option{if $currentYear == $year} selected{/if}>{$year}</option>
{/foreach}
</select>
<input type="submit" value="Go" class="post"></input>
</form>
</td></tr>
{if isset($weekDayNames)}<tr>{if $showWeekNumber==true}<td class="weeknumtitle">&nbsp;</td>{/if}{foreach name=dayName from=$weekDayNames item=weekDayName}<td class="dayname">{$weekDayName}</td>{/foreach}</tr>
{/if}
{foreach name=period from=$periods item=period}<tr>{if $showWeekNumber==true}<td class="weeknum">&nbsp;</td>{/if}{foreach name=days from=$period.days item=day}<td class="{if $day.empty == true}nomonthday{elseif $day.isToday == true}today{elseif $day.wday == 6}saturday{elseif $day.wday == 0}sunday{else}monthday{/if}">{if isset($day.day)}{$day.day}{/if}{if count($day.events) > 0}{/if}{foreach name=event from=$day.events item=event}<table class="eventcontent"><tr><td>{$event}</td></tr></table>{/foreach}</td>{/foreach}
</tr>
{/foreach}
</table>