<?php
global $pConfig;
$pConfig['db_name'] = '';
$pConfig['db_pass'] = '';
$pConfig['db_user'] = '';
$pConfig['db_server'] = '';
$pConfig['db_prefix'] = '';
$pConfig['db_pers'] = 0;

// Armory Configuration
$pConfig['armory_link'] = 'http://www.landoflegends.de/arsenal/character-sheet.xml';
$pConfig['armory_r_var'] = "r";
$pConfig['armory_realm'] = 'LoL+PvE';
$pConfig['armory_c_var'] = 'cn';

// don't change values from here on
$pConfig['armory'] = $pConfig['armory_link']."?".$pConfig['armory_r_var']."=".$pConfig['armory_realm']."&".$pConfig['armory_c_var']."=%character_name%";
?>