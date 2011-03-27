<?php
// page authentication
defined( '_VALID_RAID' ) or die( 'Resricted Access' );

if( !$pMain->checkPerm( 'edit_raids_any' ) && !$pMain->checkPerm( 'edit_raids_own' ) ) {
		pRedirect( 'index.php?option=com_login&task=login' );
	}

//Request Files
require_once('./includes/database.php');
require_once('./includes/functions.date.php');

class Output_Data	

	{
	
	function GetClassIdByClassName($class)
	{
	
		
		switch(strtolower($class))
		{
			case  'druid':
				return 1;
			case  'hunter':
				return 2;
			case  'mage':
				return 3;
			case  'shaman':
				return 4;
			case  'paladin':
				return 5;
			case  'priest':
				return 6;
			case  'rogue':
				return 7;
			case  'warlock':
				return 8;
			case  'warrior':
				return 9;
			case  'death knight':
				return 10;
		}
	}
	
	function GetClassNameByClassId($id)
	{
	
		
		switch($id)
		{
			case 0:
				return 'queue';
			case 1:
				return 'druids';
			case 2:
				return 'hunters';
			case 3:
				return 'mages';
			case 4:
				return 'shamans';
			case 5:
				return 'paladins';
			case 6:
				return 'priests';
			case 7:
				return 'rogues';
			case 8:
				return 'warlocks';
			case 9:
				return 'warriors';
			case 10:
				return 'deathknights';
		}
	}
	
		function GETROLEID($role_id,$role)
	{
	
		
		switch($role_id)
		{
			case $role[1]:
				return "1";
			case $role[2]:
				return "2";
			case $role[3]:
				return "3";
			case $role[4]:
				return "4";
			case $role[5]:
				return "5";
			case $role[6]:
				return "6";
			case $role[7]:
				return "7";
			case $role[8]:
				return "8";
			case $role[9]:
				return "9";
			case $role[10]:
				return "10";				

		}
	}
	
	
	
		function Output_Lua()
	{
		
		global $out, $db_raid;
		$lua_version = "250";
		$out .= "<b>Beginning LUA output</b><br>";
		
		// open/create file
		$file = fopen('./raid_lua/phpRaid_Data.lua','w');
		
		
		// base output
		
		$lua_output  = "phpRaid_Data = {\n";
		$lua_output .= "\t[\"lua_version\"] = \"{$lua_version}\",\n";
	
		// Pulls roles from phpraider
			$sql["SELECT"] = "*";
      		$sql["FROM"] = "role";
      		$sql["WHERE"] = "";
      		$db_raid->set_query('select', $sql, __FILE__, __LINE__);
			
			$i=0;
		
		while($role_data = $db_raid->fetch())
		{
		  	 $i++;
			$role[$i] = $role_data['role_id'];
		  	
			
			$lua_output .= "\t[\"role$i\"] = \"{$role_data['role_name']}\",\n";
			
		}
		$lua_output .= "\t[\"role_count\"] = \"".$db_raid->sql_numrows()."\",\n";
		
		
	
	// sql query
	$sql["SELECT"] = "*";
	$sql["FROM"] = "raid";
	$sql["WHERE"] = "expired = 0";
 	$raids=	$db_raid->set_query('select', $sql, __FILE__, __LINE__);
			
		$lua_output .= "\t[\"raid_count\"] = \"".$db_raid->sql_numrows($raids_result)."\",\n";
		$lua_output .= "\t[\"raids\"] = {\n";
		
		
		
		// parse result
		$count = 0;
		
		while($raid_data = $db_raid->sql_fetchrow($raids))
		{
			
		  	$invite_time_hour = newDate( 'H', $raid_data['invite_time'], 0 );
			$invite_time_minute = newDate( 'i', $raid_data['invite_time'], 0 );
			$start_time_hour = newDate( 'H', $raid_data['start_time'], 0 );
			$start_time_minute = newDate( 'i', $raid_data['start_time'], 0 );
			$lua_output .= "\t\t[{$count}] = {\n";
			$lua_output .= "\t\t\t[\"location\"] = \"{$raid_data['location']}\",\n";
			$lua_output .= "\t\t\t[\"date\"] = \"" . date('m/d/y',$raid_data['start_time']) . "\",\n";
			$lua_output .= "\t\t\t[\"invite_time\"] = \"" . $invite_time_hour.":" .$invite_time_minute . "\",\n";
			$lua_output .= "\t\t\t[\"start_time\"] = \"" .  $start_time_hour.":" .$start_time_minute . "\",\n";
		
			// sql string for signups

	$sql["SELECT"] = "*";
	$sql["FROM"] = "signups";
	$sql["WHERE"] = "raid_id = {$raid_data['raid_id']} AND cancel = 0 AND queue = 1";
	$signups = $db_raid->set_query('select', $sql, __FILE__, __LINE__);
		
		$queue = array();	
		
			while($signup = $db_raid->sql_fetchrow($signups))
			{
			  
			
					
			$sql["SELECT"] = "*";
      		$sql["FROM"] = "character";
      		$sql["WHERE"] = "character_id ={$signup['character_id']}";
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);
			$signup1 = $db_raid->fetch();
		
			  
				//Gets Race Name
			$sql["SELECT"] = "*";
      		$sql["FROM"] = "race";
      		$sql["WHERE"] = "race_id ={$signup1['race_id']}";
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);
			$race_nam = $db_raid->fetch();
				
				//Gets Class Name
			$sql["SELECT"] = "*";
      		$sql["FROM"] = "class";
      		$sql["WHERE"] = "class_id ={$signup1['class_id']}";
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);
  			$class_nam = $db_raid->fetch();
			
			
				
				array_push($queue, array(
					'name'		=> ucfirst(strtolower($signup1['char_name'])),
					'level'		=> $signup1['char_level'],
					'race'		=> $race_nam['race_name'],
					'class'		=> $class_nam['class_name'],
					'comment'	=> preg_replace("/\r|\n/s", " ", str_replace('"', '\"', $signup['comments'])),
					'timestamp'	=> date('m/d/y',$signup['timestamp']) . ' - ' . date('h:i A',$signup['timestamp']),
					'role_id'   => $this->GETROLEID($signup['role_id'], $role),
				));
			}
			
			// get data signed up
		$sql["SELECT"] = "*";
		$sql["FROM"] = "signups";
		$sql["WHERE"] = "raid_id = {$raid_data['raid_id']} AND cancel = 0 AND queue = 0";
		$signups2	= $db_raid->set_query('select', $sql, __FILE__, __LINE__);
		$signups = array();	
		
			while($signup = $db_raid->sql_fetchrow($signups2))
			{
			  	
			
			$sql["SELECT"] = "*";
      		$sql["FROM"] = "character";
      		$sql["WHERE"] = "character_id ={$signup['character_id']}";
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);
  			$signup1 = $db_raid->fetch();					
	
			  
				//Gets Race Name
			$sql["SELECT"] = "*";
      		$sql["FROM"] = "race";
      		$sql["WHERE"] = "race_id ={$signup1['race_id']}";
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);
  			$race_nam = $db_raid->fetch();		
				
				//Gets Class Name
			$sql["SELECT"] = "*";
      		$sql["FROM"] = "class";
      		$sql["WHERE"] = "class_id ={$signup1['class_id']}";
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);
  			$class_nam = $db_raid->fetch();		
				
				array_push($signups, array(
					'name'		=> ucfirst(strtolower($signup1['char_name'])),
					'level'		=> $signup1['char_level'],
					'race'		=> $race_nam['race_name'],
					'class'		=> $class_nam['class_name'],
					'comment'	=> preg_replace("/\r|\n/s", " ", str_replace('"', '\"', $signup['comments'])),
					'timestamp'	=> date('m/d/y',$signup['timestamp']) . ' - ' . date('h:i A',$signup['timestamp']),
					'role_id'   => $this->GETROLEID($signup['role_id'], $role),
				));
			}
			
		
			
			
						
			// begin - add data to lua output
			for($i=0; $i<11; $i++)
				$lua_signups[$i] = "\t\t\t[\"".$this->GetClassNameByClassId($i)."\"] = {\n";
				
			// init counter vars
			$cnt[0] = 0;
			$cnt[1] = 0;
			$cnt[2] = 0;
			$cnt[3] = 0;
			$cnt[4] = 0;
			$cnt[5] = 0;
			$cnt[6] = 0;
			$cnt[7] = 0;
			$cnt[8] = 0;
			$cnt[9] = 0;
			$cnt[10] = 0;			
			
			foreach($queue as $char)
			{
				$lua_signups[0] .= "\t\t\t\t[{$cnt[0]}] = {\n";
				$lua_signups[0] .= "\t\t\t\t\t[\"name\"] = \"{$char['name']}\",\n";
				$lua_signups[0] .= "\t\t\t\t\t[\"level\"] = \"{$char['level']}\",\n";
				$lua_signups[0] .= "\t\t\t\t\t[\"class\"] = \"".$char['class']."\",\n";
				$lua_signups[0] .= "\t\t\t\t\t[\"race\"] = \"{$char['race']}\",\n";
				$lua_signups[0] .= "\t\t\t\t\t[\"comment\"] = \"{$char['comment']}\",\n";
				$lua_signups[0] .= "\t\t\t\t\t[\"timestamp\"] = \"{$char['timestamp']}\",\n";
				$lua_signups[0] .= "\t\t\t\t\t[\"role_id\"] = \"{$char['role_id']}\",\n";
				$lua_signups[0] .= "\t\t\t\t},\n";
				$cnt[0]++;
			}
			
			foreach($signups as $char)
			{
				$class_id = $this->GetClassIdByClassName($char['class']);
				$lua_signups[$class_id] .= "\t\t\t\t[{$cnt[$class_id]}] = {\n";
				$lua_signups[$class_id] .= "\t\t\t\t\t[\"name\"] = \"{$char['name']}\",\n";
				$lua_signups[$class_id] .= "\t\t\t\t\t[\"level\"] = \"{$char['level']}\",\n";
				$lua_signups[$class_id] .= "\t\t\t\t\t[\"class\"] = \"".$char['class']."\",\n";
				$lua_signups[$class_id] .= "\t\t\t\t\t[\"race\"] = \"{$char['race']}\",\n";
				$lua_signups[$class_id] .= "\t\t\t\t\t[\"comment\"] = \"{$char['comment']}\",\n";
				$lua_signups[$class_id] .= "\t\t\t\t\t[\"timestamp\"] = \"{$char['timestamp']}\",\n";
				$lua_signups[$class_id] .= "\t\t\t\t\t[\"role_id\"] = \"{$char['role_id']}\",\n";
				$lua_signups[$class_id] .= "\t\t\t\t},\n";
				$cnt[$class_id]++;
			}
			
			// add class counts
			$lua_output .= "\t\t\t[\"queue_count\"] = \"".$cnt[0]."\",\n";
			$lua_output .= "\t\t\t[\"druids_count\"] = \"".$cnt[1]."\",\n";
			$lua_output .= "\t\t\t[\"hunters_count\"] = \"".$cnt[2]."\",\n";
			$lua_output .= "\t\t\t[\"mages_count\"] = \"".$cnt[3]."\",\n";
			$lua_output .= "\t\t\t[\"shamans_count\"] = \"".$cnt[4]."\",\n";
			$lua_output .= "\t\t\t[\"paladins_count\"] = \"".$cnt[5]."\",\n";
			$lua_output .= "\t\t\t[\"priests_count\"] = \"".$cnt[6]."\",\n";
			$lua_output .= "\t\t\t[\"rogues_count\"] = \"".$cnt[7]."\",\n";
			$lua_output .= "\t\t\t[\"warlocks_count\"] = \"".$cnt[8]."\",\n";
			$lua_output .= "\t\t\t[\"warriors_count\"] = \"".$cnt[9]."\",\n";
			$lua_output .= "\t\t\t[\"deathknights_count\"] = \"".$cnt[10]."\",\n";			
			
			for($i=0; $i<11; $i++)
				$lua_output .= $lua_signups[$i] . "\t\t\t},\n";
			$lua_output .= "\t\t},\n";
			
			$count++;
		}
		$lua_output .= "\t}\n}";
		// end - add data to lua output
		
		// write to file
		fwrite($file,utf8_encode($lua_output));
		
		// output to textarea

		$out .= 'LUA file created.</b><br>';
		$out .= 'Download <a href="./raid_lua/phpRaid_Data.lua">phpRaid_Data.lua</a> and save it to [wow-dir]\interface\addons\phpraidviewer\<br>';
	
			
	}
//Macro Output

		function Output_Macro()
	{
		
		global $out,$db_raid,$pConfig_db_prefix, $macro_output, $pConfig_time_format, $pConfig_dst, $pConfig_timezone;
	
	
		
		$macro_output .= "<b>Beginning Macro output</b><br>";
		

			
		
	
	
		
		// parse result
		$count = 0;
	$sql["SELECT"] = "*";
	$sql["FROM"] = "raid";
	$sql["WHERE"] = "expired = 0";
		$raids = $db_raid->set_query('select', $sql, __FILE__, __LINE__);
		
		while($raid_data = $db_raid->sql_fetchrow($raids))
		{
		
			$macro_output .= "<br><Br><br>location = {$raid_data['location']}<br><br><br>\n";
		
		
			// sql string for signups
			$macsign = array();
			
			$sql["SELECT"] = "*";
			$sql["FROM"] = "signups";
			$sql["WHERE"] = "raid_id ={$raid_data['raid_id']} AND cancel = 0 AND queue = 0";
			$sign =$db_raid->set_query('select', $sql, __FILE__, __LINE__);
			

			while($signup = $db_raid->sql_fetchrow($sign))
			{
			  	
					
			$sql["SELECT"] = "*";
      		$sql["FROM"] = "character";
      		$sql["WHERE"] = "character_id ={$signup['character_id']} ";
			$db_raid->set_query('select', $sql, __FILE__, __LINE__);
			$signup1 = $db_raid->fetch();
					
				array_push($macsign, array(
					'name'		=> ucfirst(strtolower($signup1['char_name']))
				
				));
			}
			
		
			
			foreach($macsign as $char)
			{						
				$macro_output .= "/invite {$char['name']}<br>";			
			}
					
		}
					
	}
		
		
}


		


global $out;

$Output_Data = new Output_Data;
$Output_Data->Output_Lua();
$Output_Data->Output_Macro();




// Start output of page

$p->assign( 'header', sprintf( $pLang['viHeader'], '<font color=red>Lua Output</font>' ) );
	$p->assign(
		array(
			'output_data'=>$out,
			'macro_data' =>$macro_output
			
		)
	);
	
	$p->display(RAIDER_TEMPLATE_PATH.'lua_output.tpl' );


?>
