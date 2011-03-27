<?php
// javascript information
$javascripts = array(
);

$scripts = '';
$tooltip = '<div id="dhtmltooltip"></div><script src="../includes/scripts/tooltips/tooltips.js" language="javascript"></script>';

$p->assign('javascripts', $javascripts);
$p->assign('javascript', $scripts);
$p->assign('tooltip', $tooltip);

if (!empty($id)) {
	$p->assign ('id' , $id);
}


// output header template
$p->display('header.tpl');
?>