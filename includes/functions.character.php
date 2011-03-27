<?php
// gets character data and returns in an associate array
function getCharacterData($pMain, $profile_id = '') {
	global $db_raid, $pConfig, $pLang;

	// setup attributes
	$sql['SELECT'] = '*';
	$sql['FROM'] = 'attribute';
	$db_raid->set_query('select', $sql, __FILE__, __LINE__);

	while($data = $db_raid->fetch()) {
		$pAttributes[$data['attribute_id']] = $data['att_name'];
	}

	$phpr_a = array();

	$sql['SELECT'] = 'c.*,cls.class_name,g.guild_name,r.race_name,ge.gender_name';
	$sql['FROM'] = array('character c','class cls','race r','gender ge');
	$sql['JOIN'] = array('TYPE'=>'LEFT','TABLE'=>'guild g','CONDITION'=>'c.guild_id=g.guild_id');
	$sql['WHERE'] = 'c.class_id=cls.class_id AND c.race_id=r.race_id AND ge.gender_id=c.gender_id';
	if(!empty($profile_id)) $sql['WHERE'] .= ' AND `profile_id`='.$profile_id;
	$char_res = $db_raid->set_query('select', $sql, __FILE__, __LINE__);

	while($data = $db_raid->sql_fetchrow($char_res)) {
		// admin options
		$admin = '';

		if(($pMain->getProfileID() == $data['profile_id']) || $pMain->checkPerm('edit_characters_any')) {
			$admin = '<a href="index.php?option=com_characters&task=edit&id='.$data['character_id'].'"><img src="templates/'.
					  $pConfig['template'].'/images/icons/icon_edit.png" border="0" onMouseover="ddrivetip(\''
					 .$pLang['edit'].'\');" onMouseout="hideddrivetip();" alt="'.$pLang['edit'].'"></a> ';
			$admin .= ' <input type="checkbox" name="select[]" value="'.$data['character_id'].'">';
		}

		// setup race icon
		$path = RAIDER_BASE_PATH.'games/'.$pConfig['game'].'/images/races//%s/%s.png';

		if(is_file(sprintf($path,strtolower($data['gender_name']),strtolower(str_replace(' ', '', $data['race_name']))))) {
			$race = '<img src="'.str_replace(RAIDER_BASE_PATH.'', '', sprintf($path,urlencode(strtolower($data['gender_name'])),urlencode(strtolower(str_replace(' ', '', $data['race_name']))))).'" border="0" onMouseover="ddrivetip(\''.$data['race_name'].'\');" onMouseout="hideddrivetip();" height="18" width="18" alt="'.$data['race_name'].'">';
		} else {
			$race = $data['race_name'];
		}

		$tooltip = $data['class_name'];
		if($pConfig['multi_class']) {
			// get subclasses
			unset($sql);
			$sql['SELECT'] = '*';
			$sql['FROM'] = array('subclass s','class cls');
			$sql['WHERE'] = 's.class_id=cls.class_id AND `character_id`='.$data['character_id'];

			$result2 = $db_raid->set_query('select', $sql, __FILE__, __LINE__);
			if ($db_raid->sql_numrows($result2)>0) {
				$tooltip .= '<br><br><strong>'.$pLang['subclasses'].'</strong>';
				while($data2 = $db_raid->sql_fetchrow($result2)) {
					$tooltip .= '<br>'.$data2['class_name'];
				}
			}

		}

		// setup class icon
		$path = RAIDER_BASE_PATH.'games/'.$pConfig['game'].'/images/classes/%s.png';

		if(is_file(sprintf($path,strtolower(str_replace(' ', '', $data['class_name']))))) {
			$class = '<img src="'.str_replace(RAIDER_BASE_PATH.'', '', sprintf($path,strtolower(urlencode(str_replace(' ', '', $data['class_name']))))).'" border="0" onMouseover="ddrivetip(\''.$tooltip.'\');" onMouseout="hideddrivetip()" height="18" width="18" alt="'.$data['class_name'].'">';
		} else {
			$class = $data['class_name'];
		}

		// attributes
		$tooltip = '';

		unset($sql);
		$sql['SELECT'] = $data['character_id'].' as character_id,IFNULL(d.att_value,\'\') as att_value,a.*';
		$sql['FROM'] = 'attribute a';
		$sql['JOIN'] = array('TYPE'=>'LEFT','TABLE'=>'attribute_data d','CONDITION'=>'d.attribute_id=a.attribute_id AND d.character_id='.$data['character_id']);

		$att_result =  $db_raid->set_query('select', $sql, __FILE__, __LINE__);

		unset($merge);
		while($att_data = $db_raid->sql_fetchrow($att_result)) {
			$merge[$att_data['att_name']] = $att_data['att_value'];

			if($att_data['att_hover']) {
				$tooltip .= $att_data['att_name'].' - '.$att_data['att_value'].'<br>';
			}
		}

		// setup name
		if(empty($tooltip))
			$tooltip = '';
		else
			$tooltip = 'onMouseover="ddrivetip(\''.$tooltip.'\');" onMouseout="hideddrivetip();"';

		$name = '<a href="index.php?option=com_roster&id='.$data['character_id'].'">
				<img src="templates/'.$pConfig['template'].'/images/icons/icon_user_details.png" border="0"
				onMouseover="ddrivetip(\''.$pLang['character_details'].'\');" onMouseout="hideddrivetip();">
				</a>  <a '.$tooltip.'>'.$data['char_name'].'</a>';

		// setup array for data output
		$merge2 = array(
					'name' => $name,
					'guild' => (empty($data['guild_name'])?'':$data['guild_name']),
					'race' => $race,
					'class' => $class,
					'level' => $data['char_level'],
					'checkbox' => $admin
		);

		// merge error check
		if(empty($merge)) {
			$merge = array();
		}

		array_push($phpr_a, array_merge($merge, $merge2));
	}

	return $phpr_a;
}
?>