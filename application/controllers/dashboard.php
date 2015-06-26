<?php 
class Dashboard extends CI_Controller
{
	function __construct()
	{
		parent::__construct();		
		if(!checksession('logged_in'))
		{
			$this->my_session->set_userdata('returnUrl',current_url());
			redirect(site_url('user'));
		}
		if(userPrivilege('dashboard')!=1)
		{
			redirect(site_url());
			exit();
		}
		unset($_SESSION['user']);		
		$this->load->model('mdash');	
	}
	
	function index()
	{
		$data['uname'] = $this->my_session->userdata('uname');
		$data['title'] = "Dashboard";
		$data['content'] = "vdashboard";
		$data['dateval'] = isset($_SESSION['dateval']) && $_SESSION['dateval']!=""?$_SESSION['dateval']:date("F j, Y",strtotime(NOW));
		$data['th'] = $this->weekdaysarray(date('Y-m-d',strtotime(date('Y-m-d',strtotime($data['dateval']))."+1 day")));
		$dashboard = array();
		$status = isset($_SESSION['status'])?$_SESSION['status']:"Won";
		foreach ($this->mdash->getPrograms() as $program)
		{
			$days['monfilter'] = array('day'=>$data['th'][0],'program'=>$program['id'],'status'=>$status);
			$days['mon'] = $this->mdash->getRecords($days['monfilter']);//monday
			$days['tuefilter'] = array('day'=>$data['th'][1],'program'=>$program['id'],'status'=>$status);
			$days['tue'] = $this->mdash->getRecords($days['tuefilter']);//tuesday
			$days['wedfilter'] = array('day'=>$data['th'][2],'program'=>$program['id'],'status'=>$status);
			$days['wed'] = $this->mdash->getRecords($days['wedfilter']);//wednesday
			$days['thufilter'] = array('day'=>$data['th'][3],'program'=>$program['id'],'status'=>$status);
			$days['thu'] = $this->mdash->getRecords($days['thufilter']);//thursday
			$days['frifilter'] = array('day'=>$data['th'][4],'program'=>$program['id'],'status'=>$status);
			$days['fri'] = $this->mdash->getRecords($days['frifilter']);//friday
			$program['days'] = $days;
			array_push($dashboard, $program);
		}
		$data['programs']=$dashboard;	
		$final = array();	
		$dailyTotals = array();$monTot=0;$tueTot=0;$wedTot=0;$thuTot=0;$friTot=0;$prevWeekTot=0;$allTot=0;
		foreach ($dashboard as $each)
		{
			$temp['program'] = $each['title'];
			$temp['batch'] = $each['batch'];
			$temp['dateStart'] = $each['dateStart'];
			$temp['dateEnd'] = $each['dateEnd'];
			$temp['logo'] = $each['logo'];
			
			$temp['monfilter'] = $each['days']['monfilter'];
			$temp['mon'] = count($each['days']['mon']);
			$temp['tuefilter'] = $each['days']['tuefilter'];
			$temp['tue'] = count($each['days']['tue']);
			$temp['wedfilter'] = $each['days']['wedfilter'];
			$temp['wed'] = count($each['days']['wed']);
			$temp['thufilter'] = $each['days']['thufilter'];
			$temp['thu'] = count($each['days']['thu']);
			$temp['frifilter'] = $each['days']['frifilter'];
			$temp['fri'] = count($each['days']['fri']);	
			
			$temp['lastweeksfilter'] = array('lastweeks'=>$data['th'][0],'program'=>$each['id'],'status'=>$status);
			$temp['lastweeks'] = count($this->mdash->getRecords($temp['lastweeksfilter']));		
			
			$i=0;
			$i = $temp['mon'] + $temp['tue'] + $temp['wed'] + $temp['thu'] + $temp['fri'] + $temp['lastweeks'];
			$temp['total'] = $i;
			$temp['weektotalfilter'] = array('weekfrom'=>$data['th'][0],'status'=>$status,'program'=>$each['id']);//binago ko, bali ung total, overall total na xa includeling previous weeks
			
			$dailyTotals['prevWeekTot'] = $prevWeekTot+=$temp['lastweeks'];
			$dailyTotals['monTot'] = $monTot+=$temp['mon'];
			$dailyTotals['tueTot'] = $tueTot+=$temp['tue'];
			$dailyTotals['wedTot'] = $wedTot+=$temp['wed'];
			$dailyTotals['thuTot'] = $thuTot+=$temp['thu'];
			$dailyTotals['friTot'] = $friTot+=$temp['fri'];
			$dailyTotals['allWeekTot'] = $allTot+=$temp['total'];
			
			array_push($final,$temp);
		}
		$data['dailyTotals'] = $dailyTotals;
		$data['final'] = $final;
		$data['status'] = $status;//won/loss/pending/all
		$this->load->view('template',$data);			
	}
	
	private function weekdaysarray($date = null, $format = null, $start = 'monday')
	{
		if(is_null($date)) $date = 'now';
	
		// get the timestamp of the day that started $date's week...
		$weekstart = strtotime('last '.$start, strtotime($date));
	
		// add 86400 to the timestamp for each day that follows it...
		for($i = 0; $i < 5; $i++) {
			$day = $weekstart + (86400 * $i);
			if(is_null($format)) $dates[$i] = $day;
			else $dates[$i] = date($format, $day);
		}
		return $dates;
	}	


	function reload()
	{
		if(isset($_POST['ajax']))
		{
			$date = $this->input->post('week');
			if($date=="") $date = 'now +1 day';else $date = date('Y-m-d',strtotime(date('Y-m-d',strtotime($date))."+1 day"));
			$data['th'] = $this->weekdaysarray($date);
			$dashboard = array();
			$status = $this->input->post('type');
			$_SESSION['status'] = $status;
			$_SESSION['dateval'] = $this->input->post('week');
			foreach ($this->mdash->getPrograms() as $program)
			{
				$days['monfilter'] = array('day'=>$data['th'][0],'program'=>$program['id'],'status'=>$status);
				$days['mon'] = $this->mdash->getRecords($days['monfilter']);//monday
				$days['tuefilter'] = array('day'=>$data['th'][1],'program'=>$program['id'],'status'=>$status);
				$days['tue'] = $this->mdash->getRecords($days['tuefilter']);//tuesday
				$days['wedfilter'] = array('day'=>$data['th'][2],'program'=>$program['id'],'status'=>$status);
				$days['wed'] = $this->mdash->getRecords($days['wedfilter']);//wednesday
				$days['thufilter'] = array('day'=>$data['th'][3],'program'=>$program['id'],'status'=>$status);
				$days['thu'] = $this->mdash->getRecords($days['thufilter']);//thursday
				$days['frifilter'] = array('day'=>$data['th'][4],'program'=>$program['id'],'status'=>$status);
				$days['fri'] = $this->mdash->getRecords($days['frifilter']);//friday
				$program['days'] = $days;
				array_push($dashboard, $program);
			}
			$data['programs']=$dashboard;	
			$final = array();	
			$dailyTotals = array();$monTot=0;$tueTot=0;$wedTot=0;$thuTot=0;$friTot=0;$prevWeekTot=0;$allTot=0;
			foreach ($dashboard as $each)
			{
				$temp['program'] = $each['title'];
				$temp['batch'] = $each['batch'];
				$temp['dateStart'] = $each['dateStart'];
				$temp['dateEnd'] = $each['dateEnd'];
				$temp['logo'] = $each['logo'];
				$i=0;
				foreach ($each['days'] as $day)
				{
					 $i += count($day);
					 $temp['total'] = $i;
				}
				
				$temp['monfilter'] = $each['days']['monfilter'];
				$temp['mon'] = count($each['days']['mon']);
				$temp['tuefilter'] = $each['days']['tuefilter'];
				$temp['tue'] = count($each['days']['tue']);
				$temp['wedfilter'] = $each['days']['wedfilter'];
				$temp['wed'] = count($each['days']['wed']);
				$temp['thufilter'] = $each['days']['thufilter'];
				$temp['thu'] = count($each['days']['thu']);
				$temp['frifilter'] = $each['days']['frifilter'];
				$temp['fri'] = count($each['days']['fri']);	
				
				$temp['lastweeksfilter'] = array('lastweeks'=>$data['th'][0],'program'=>$each['id'],'status'=>$status);
				$temp['lastweeks'] = count($this->mdash->getRecords($temp['lastweeksfilter']));		
				
				$i=0;
				$i = $temp['mon'] + $temp['tue'] + $temp['wed'] + $temp['thu'] + $temp['fri'] + $temp['lastweeks'];
				$temp['total'] = $i;
				$temp['weektotalfilter'] = array('weekfrom'=>$data['th'][0],'status'=>$status,'program'=>$each['id']);	//binago ko, bali ung total, overall total na xa includeling previous weeks

				$dailyTotals['prevWeekTot'] = $prevWeekTot+=$temp['lastweeks'];
				$dailyTotals['monTot'] = $monTot+=$temp['mon'];
				$dailyTotals['tueTot'] = $tueTot+=$temp['tue'];
				$dailyTotals['wedTot'] = $wedTot+=$temp['wed'];
				$dailyTotals['thuTot'] = $thuTot+=$temp['thu'];
				$dailyTotals['friTot'] = $friTot+=$temp['fri'];
				$dailyTotals['allWeekTot'] = $allTot+=$temp['total'];
				array_push($final,$temp);
			}
			$data['dailyTotals'] = $dailyTotals;
			$data['final'] = $final;
			$data['status'] = $status;//won/loss/pending/all
			$data['dateval'] = $_SESSION['dateval'];
			$this->load->view('vDashAjax',$data);				
		}
	}
	
	function getDetails()
	{
		if(isset($_POST['ajax']))
		{
			$filters['lw'] = $this->input->post('lw');
			$date = $this->input->post('day');
			$filters['program'] = $this->input->post('program');
			$filters['status'] = !empty($_POST['status'])?$this->input->post('status'):"all";
			if(isset($_POST['lw']) && $_POST['lw']==1)
			{
				$filters['lastweeks'] = $date;
			}
			elseif(isset($_POST['lw']) && $_POST['lw']==2)
			{
				$filters['weekfrom'] = $date;
			}
			else
			{
				$filters['day'] = $date;
			} 
			$data['records'] = $this->mdash->getRecords($filters);
			$data['filters'] = $filters;
			$data['date'] = $date;
			//print_r($data);
			$this->load->view('dashboarddetails',$data);
		}
	}
	
	function showcontacts()
	{
		if(isset($_POST['ajax']))
		{
			$filters['lw'] = $this->input->post('lw');
			$filters['program'] = $this->input->post('program');
			$filters['status'] = !empty($_POST['status'])?$this->input->post('status'):"all";
			$date = $this->input->post('day');
			if(isset($_POST['lw']) && $_POST['lw']==1)
			{
				$filters['lastweeks'] = $date;
			}
			elseif(isset($_POST['lw']) && $_POST['lw']==2)
			{
				$filters['weekfrom'] = $date;
			}
			else
			{
				$filters['day'] = $date;
			}
			$data['contacts']=$this->mdash->getDashContacts($filters);
			$data['fromDashboard'] = 1;
			$this->load->view('vContacts',$data);
		}
	}
	//filter contacts
	function contactfilter()
	{
		if(isset($_POST['ajax']))
		{
			$filters['lw'] = $this->input->post('lw');
			$filters['program'] = $this->input->post('program');
			$filters['status'] = !empty($_POST['status'])?$this->input->post('status'):"all";
			$date = $this->input->post('day');
			if(isset($_POST['lw']) && $_POST['lw']==1)
			{
				$filters['lastweeks'] = $date;
			}
			elseif(isset($_POST['lw']) && $_POST['lw']==2)
			{
				$filters['weekfrom'] = $date;
			}
			else
			{
				$filters['day'] = $date;
			}
			$filters['infoids'] = $this->input->post('infoid');
			$filters['searchkey'] = $this->input->post('searchkey');
			$filters['searchval'] = $this->input->post('searchval');
			$filters['conProgram'] = $this->input->post('conProgram');
			$data['contacts'] = $this->mdash->getDashContacts($filters);
			$this->load->view('vContactAjax',$data);
		}
	}
	
	function programprogress()
	{
		$data['uname'] = $this->my_session->userdata('uname');
		$data['title'] = "Program Progress";
		$data['content'] = "vProgramProgress";
		$dashboard = array();
		foreach ($this->mdash->getPrograms() as $program)
		{
			$end = strtotime($program['dateEnd']);
			$start = strtotime($program['dateStart']);
			$totalweeks = array();
			$c = round(($end-$start)/86400);
			$d = explode("-", date("Y-n-j",$start));
			$e = explode("-", date("Y-n-j",$end));
			for($i = 0;$i<=$c;$i++)
			{
				if(date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=6 && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=7)
				{
					$tempW = explode('-',date("W-Y",mktime(1,0,0,$d[1],$d[2]+$i,$d[0])));
					$w = $tempW[0] + (52 * $tempW[1]); // add the previous years week
					if(!in_array($w, $totalweeks))
					{
						$totalweeks[] = $w;
					}
				}
			}
			$temp = $totalweeks;
			$program['totalWeeks'] = count($totalweeks);
			$targetPerWeek = round($program['target']/count($totalweeks),1);
			$currentWeek = explode('-',date("W-Y",strtotime(NOW)));
			$weekNo = $currentWeek[0] + (52 * $currentWeek[1]);
			foreach ($temp as $week)
			{
				if($weekNo == $week)
				{
					$program['weekNo'] = key($totalweeks) + 1;
					$prefix = "";
					switch ($program['weekNo'])
					{
						case 1: $prefix = 'st';break;
						case 2: $prefix = 'nd';break;
						case 3: $prefix = 'rd';break;
						default: $prefix = 'th';break;
					}					
					$program['currentWeekTarget'] = $targetPerWeek * $program['weekNo'];
					$program['weekProgress'] = round(($program['weekNo']/(count($totalweeks)!=0?count($totalweeks):1))*100,1);
					$program['weekNo'] = $program['weekNo'].$prefix;
				}				
				next($totalweeks);
			}
			if(end($totalweeks) < $weekNo)
			{
				$program['weekProgress'] = 100;
				$program['currentWeekTarget'] = $program['target'];
				$program['weekNo'] = $weekNo - end($totalweeks).' week'.($weekNo - end($totalweeks)>1?'s':'').' overdue';
			}
			elseif($totalweeks[0]>$weekNo)
			{
				$program['weekProgress'] = 0;
				$program['currentWeekTarget'] = 0;
				$program['weekNo'] = $totalweeks[0] - $weekNo .' week'.($totalweeks[0] - $weekNo>1?'s':'').' ahead';
			}
			
			$program['won'] = $this->mdash->getProgramWon($program['id'])->num_rows();
			$program['accumPercent'] = round(($program['won']/($program['currentWeekTarget']!=0?round($program['currentWeekTarget']):1))*100,1);
			$program['programPercent'] = round(($program['won']/($program['target']!=0?round($program['target']):1))*100,1);			
			
			$program['alertLevel'] = 1;// 0-good,1-normal,2-medium critical,3-critical
			if($weekNo >= (array_key_exists(count($totalweeks)-4,$totalweeks) ? $totalweeks[count($totalweeks)-4] : $totalweeks[0]) && $program['programPercent']<=50)
			{
				$program['alertLevel'] = 3;
			}
			elseif($weekNo >= $totalweeks[abs(count($totalweeks)/2)-1]  && $program['programPercent']<=50)
			{
				$program['alertLevel'] = 2;
			}
			elseif($program['programPercent']>=50)
			{
				$program['alertLevel'] = 0;
			}
		
			array_push($dashboard, $program);
		}
		$dashboard = array_sort($dashboard, 'alertLevel',SORT_DESC);
		$data['programs']=$dashboard;	
		
		//echo "<pre>";print_r($data);
		$this->load->view('template',$data);	
	}
		
}
?>