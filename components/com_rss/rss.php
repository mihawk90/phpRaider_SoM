<?php
// no direct access
defined('_VALID_RAID') or die('Restricted Access');

// load footer?
$load_footer = 1;

// no caching for this
$p->caching = false;

if(empty($task) || $task == '') {
	// localizations
	$p->assign(
		array(
			// header
			'header' => $pLang['rssHeader'],

			// text
			'rssAvailable' => $pLang['rssAvailableFeeds'],
			'rssRaids' => '<a href="index.php?option=com_rss&task=raid">'.$pLang['rssRaids'].'</a> - '
							. $pConfig['site_url'].'/index.php?option=com_rss&task=raid',
			'rssAnnouncements' => '<a href="index.php?option=com_rss&task=announcements">'.$pLang['rssAnnouncements'].'</a> - '
							. $pConfig['site_url'].'/index.php?option=com_rss&task=announcements',
		)
	);

	// display form
	$p->display(RAIDER_TEMPLATE_PATH.'rss.tpl');
} else if ($task == 'raid') {
	// do not load footer
	$load_footer = 0;

	// clear buffer
	ob_end_clean();

	// get raids based on date
	$sql["SELECT"] = "*";
	$sql["FROM"] = "raid";
	$sql['WHERE'] = 'expired=0';
	// setup date parameter if specified
	if(!empty($_GET['date'])) {
		$date = strtotime($_GET['date']);
		$sql["WHERE"] .= " AND timestamp <= {$date} AND timestamp >= ".time();
	}
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	// rss feed for raids
	$pConfig['site_url'] .= ((substr($pConfig['site_url'],-1, 1)=='/')?'':'/');
	
	// loop through each RSS item
	$rss_items	= "";
	while($data = $db_raid->fetch()) {
		$rss_items .= "\t<item>\n";
		$rss_items .= "\t\t<title>".utf8_encode(htmlspecialchars($data['location']))." - ".newDate($pConfig['date_format'], $data['invite_time'],0)." @ ".newDate($pConfig['time_format'], $data['invite_time'],0)."</title>\n";
		$rss_items .= "\t\t<link>".$pConfig['site_url']."index.php?option=com_view&amp;id=".$data['raid_id']."</link>\n";
		$rss_items .= "\t\t<guid>".$data['raid_id']."</guid>\n";
		$rss_items .= "\t\t<author>".$data['raid_leader']."</author>\n";
		$rss_items .= "\t\t<pubDate>".newDate(DATE_RFC2822, $data['raid_create_time'],0)."</pubDate>\n";
		$rss_items .= "\t\t<description>".utf8_encode(htmlspecialchars(nl2br($data['description'])))."</description>\n";
		$rss_items .= "\t</item>\n";
	}

	// assign variables for template
	$p->assign(
		array(
			'rss_items'		=> $rss_items,
			'rss_link'		=> $pConfig['site_url'],
			'rss_lastBuildDate' 	=> date(DATE_RFC2822,gmmktime()),
		)
	);

	// parse template
	$rss_tpl_file 	= RAIDER_TEMPLATE_BASE_PATH.'_general_templates'.DIRECTORY_SEPARATOR.'rss_feed.tpl';
	$p->display($rss_tpl_file);

	// generate data for header
	echo strlen($rss_tpl_file);
	header("Content-Length: " .strlen($rss_items) + strlen($rss_tpl_file));
	header('Content-type: text/xml; charset=UTF-8');

//	echo $output;
} else if ($task == 'announcements') {
	// do not load footer
	$load_footer = 0;

	// clear buffer
	ob_end_clean();

	// get raids based on date
	$sql["SELECT"] = "*";
	$sql["FROM"] = "announcements";

	// setup date parameter if specified
	if(!empty($_GET['date'])) {
		$date = strtotime($_GET['date']);
		$sql["WHERE"] = "timestamp <= {$date} AND timestamp >= ".time();
	}
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	// rss feed for raids
	$pConfig['site_url'] .= ((substr($pConfig['site_url'],-1, 1)=='/')?'':'/');
	$output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$output .= "<rss version=\"2.0\">\n<channel>";
	$output .= "<title>phpRaider ".$pLang['rssAnnouncementsFor']." ".$pConfig['game']."</title>\n
				<description>phpRaider ".$pLang['rssAnnouncementsFor']." ".$pConfig['game']."</description>\n
				<link>".$pConfig['site_url']."</link>\n
				<lastBuildDate>".date(DATE_RFC2822,gmmktime())."</lastBuildDate>\n
				<language>en-us</language>\n";

	// loop through each RSS item

	while($data = $db_raid->sql_fetchrow($result)) {
		$output .= "<item>
					<title>".utf8_encode(htmlspecialchars($data['announcement_title']))."</title>
					<link>".$pConfig['site_url']."index.php</link>
					<guid>".$pConfig['site_url']."index.php#".$data['announcement_id']."</guid>
					<pubDate>".newDate(DATE_RFC2822, $data['announcement_timestamp'],0)."</pubDate>
					<description>".utf8_encode(htmlspecialchars(nl2br($data['announcement_msg'])))."</description>
					</item>\n";
	}

	$output .= "</channel>
				</rss>";
	header("Content-Length: " .strlen($output));
	header('Content-type: text/xml; charset=UTF-8');

	echo $output;
} else {
	printError($pLang['rssUnavailable'], 0);
}
?>