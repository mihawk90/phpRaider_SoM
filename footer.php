<?php
// version information
include_once(RAIDER_BASE_PATH.'version.php');

// get sql count
$count = $db_raid->num_queries;

// get page generation time (Mordon)
$endtime = explode(' ', microtime() );
$endtime = $endtime[1] + $endtime[0];
$totaltime = round($endtime - $pStart_time, 2);

$p->assign(
	array(
		'sql_count'=>$count,
		'version'=>$version,
		'totaltime'=>$totaltime
	)
);

$p->display(RAIDER_TEMPLATE_PATH.'footer.tpl');

if($pConfig['debug_mode']) {
	echo '<br><div class="errorHeader">Debug mode enabled</div>';
	echo '<div class="errorBody" style="text-align:left">';

	// session information
	echo '<strong>Dumping $_SESSION ...<br></strong>';
	echo dump_array($_SESSION);
	echo '<br>';

	// permission information
	echo '<strong>Dumping user permissions ...<br></strong>';
	echo dump_array($pMain->getPerm());
	echo '<br>';

	// post information
	echo '<strong>Dumping $_POST ...<br></strong>';
	echo dump_array($_POST);
	echo '<br>';

	// Dumping SQL queries
	echo '<strong>Dumping generated queries ... <br></strong>';
	echo dump_query_history();
	echo '</div>';
}
?>