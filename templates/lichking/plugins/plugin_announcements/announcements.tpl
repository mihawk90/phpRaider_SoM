{capture assign="plugin_announcements"}
	{section name=announcements loop=$a_data}
		<div class="contentHeader">{$a_data[announcements].actions}{$a_data[announcements].title}</div>
		<div class="contentBody">
			<div class="announcementMessage">{$a_data[announcements].message}</div>
			<div class="announcementPoster">{$a_data[announcements].author} @ {$a_data[announcements].date} - {$a_data[announcements].time}</div>
		</div><br />
	{/section}
{/capture}