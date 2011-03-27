<?php
// no direct access
defined('_VALID_SETUP') or die('Restricted Access');

// get auth method
include(RAIDER_BASE_PATH.'configuration.php');

// run authentication installation file
include(RAIDER_BASE_PATH.'authentication'.DIRECTORY_SEPARATOR.$_SESSION['pConfig_authentication'].DIRECTORY_SEPARATOR.$_SESSION['pConfig_authentication'].'.install.php');
?>