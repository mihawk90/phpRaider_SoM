<?php
class prCalendar {
	var $_events;
	var $_month;
	var $_year;
	var $_selectedDate;
	var $_now;
	var $_firstWeekDay;
	var $_monthNames = array(1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	var $_weekDayNames = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');

	function prCalendar($year=false, $month=false, $day=false) {
		global $pConfig,$pLang;

		$this->setFirstWeekDay((empty($pConfig['first_day_of_week'])?0:$pConfig['first_day_of_week']));
		$this->_year = ((!$year)?date('Y'):$year);
		$this->_month = ((!$month)?date('n'):$month);
		$this->_selectedDate = ((!$day)?date('j'):$day);
		$this->_now = time();
		if (!empty($pConfig['timezone']) || $pConfig['dst'] != 0 ) {
			$this->_now = getUTCTime()+ 36*($pConfig['timezone'] + $pConfig['dst']);
		}
		$this->_events = array();
		if (isset($pLang['calDayNames'])) {
			$this->setDayNames($pLang['calDayNames']);
		}
		if (isset($pLang['calMonthNames'])) {
			$this->setMonthNames($pLang['calMonthNames']);
		}
	}

	function setDayNames($weekDayNames){
		if (is_array($weekDayNames) && count($weekDayNames)==7) {
			$this->_weekDayNames = $weekDayNames;
		}
	}

	function addEventContent($year, $month, $day, $calendar_output){
		$this->_events[$year][$month][$day][] = $calendar_output;
	}

	function getEvents($day,$month,$year){
		return (isset($this->_events[$year][$month][$day])?$this->_events[$year][$month][$day]:array());
	}

	/*
	*/
	function setFirstWeekDay($daynum){
		if ($daynum >=0 && $daynum <=6) {
			$this->_firstWeekDay = $daynum;
		}
	}

	function setMonthNames($namesArray){
		if (!is_array($namesArray) || count($namesArray)!=12) {
			return false;
		} else {
			$m = array(1,2,3,4,5,6,7,8,9,10,11,12);
			$this->_monthNames = array_combine($m, $namesArray);
		}
	}

	function getNextParameters(){
		if ($this->_month==12) {
			$result = 'yearID='.($this->_year+1).'&amp;monthID=1';
		} else {
			$result = 'yearID='.$this->_year.'&amp;monthID='.($this->_month+1);
		}
		return $result;
	}

	function getPrevParameters(){
		if ($this->_month==1) {
			$result = 'yearID='.($this->_year-1).'&amp;monthID=12';
		} else {
			$result = 'yearID='.$this->_year.'&amp;monthID='.($this->_month-1);
		}
		return $result;
	}

	function getWeekDayNames(){
		$weekDayNames = array();
		for ($i=0;$i<7;$i++) {
			$weekDayNames[$i] = $this->_weekDayNames[($i+$this->_firstWeekDay) % 7];
		}
		return $weekDayNames;
	}

	function getYears(){
		$years = array();
		for ($i=-1;$i<=2;$i++) {
			$years[] = $this->_year + $i;
		}
		return $years;
	}

	function render(){
		$weekDayNames = $this->getWeekDayNames();
		$period = array();
		$thisMonth = mktime(0,0,0,$this->_month,1,$this->_year);
		$maxMonthDays = date('t',$thisMonth);
		$firstWeekday = date('w',$thisMonth);
		$firstDayOffset = $firstWeekday - $this->_firstWeekDay;
		if ($firstDayOffset < 0) {
			$firstDayOffset += 7;
			$daysLeft = 7;
		} else {
			$daysLeft = 0;
		}
		$dayCounter = $maxMonthDays + ($firstWeekday - $this->_firstWeekDay);
		$daysLeft += (($dayCounter % 7 == 0)?0:(7 - ($dayCounter % 7)));
		$dayCounter += $daysLeft;
		$weekDayNr = $firstWeekday;
		if ($this->_month == date('n',$this->_now) && $this->_year == date('Y',$this->_now)) {
			$today = date('j',$this->_now);
		} else {
			$today = -1;
		}
		for($count=0;$count<=$dayCounter;$count++) {
			if ($count % 7 == 0) {
				if ($count > 0) {
					$period[] =  array('days'=>$week);
				}
				$week = array();
			}
			if ($count<$firstDayOffset) {
				array_push($week,array('empty'=>true));
			} elseif ($count>$firstDayOffset+$maxMonthDays-1) {
				array_push($week,array('empty'=>true));
			} else {
				$day = $count-($firstDayOffset-1);
				array_push($week,array('empty'=>false,'day'=>$day,'isToday'=>(($day==$today)?true:false),'wday'=>$weekDayNr % 7,'events'=>$this->getEvents($day,$this->_month,$this->_year)));
				$weekDayNr++;
			}
		}
		return $period;
	}

	/*
	* render and return the calendar output as a string
	*/
	function fetch($template){
		global $p;

		$weekDayNames = $this->getWeekDayNames();

		$periods = $this->render();

		$p->assign(array('weekDayNames'=>$weekDayNames,'periods'=>$periods,'currentMonth'=>$this->_month,'currentYear'=>$this->_year,'baseURL'=>$_SERVER['PHP_SELF'],'monthNames'=>$this->_monthNames,'years'=>$this->getYears(),'previous'=>$this->getPrevParameters(),'next'=>$this->getNextParameters()));
		return $p->fetch($template);
	}

	/*
	* render and display rhe calendar output
	*/
	function display($template){
		global $p;

		$weekDayNames = $this->getWeekDayNames();

		$periods = $this->render();

		$p->assign(array('weekDayNames'=>$weekDayNames,'periods'=>$periods,'currentMonth'=>$this->_month,'currentYear'=>$this->_year,'baseURL'=>$_SERVER['PHP_SELF'],'monthNames'=>$this->_monthNames,'years'=>$this->getYears(),'previous'=>$this->getPrevParameters(),'next'=>$this->getNextParameters()));
		$p->display($template);
	}
}
?>