<?php 
class Statistics extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if(!checksession('logged_in'))
		{
			$this->my_session->set_userdata('returnUrl',current_url());
			redirect(site_url('user'));
		}
		if(userPrivilege('statistics')!=1)
		{
			redirect(site_url());
			exit();
		}
		unset($_SESSION['status']);//session sa dashboard
		unset($_SESSION['dateval']);//session sa dashboard		
		$this->load->model('mstats');	
	}
	
	private $aveType = "daily";
	private $rankGroup = "won";
	private $rankSubGroup = "raw";
	private $selDateType = 'career';
	private $dateFrom = '';
	private $dateTo = '';
	private $columns = array('won'=>'Won','pending'=>'Pending','loss'=>'Loss','rejected'=>'Rejected','ic'=>'Incoming Call','im'=>'Incoming Mail','oc'=>'Outgoing Call','om'=>'Outgoing Mail');
	
	private $skipday = array('2011-08-29','2011-08-30','2011-09-27','2011-10-31','2011-11-01','2011-11-07');
	private $dateType = array('career'=>'Career','thisWeek'=>'This week','thisMonth'=>'This month','thisYear'=>'This year',
								'prevWeek'=>'Previous week','prevMonth'=>'Previous month','prevYear'=>'Previous year','specificDate'=>'Specific range');
		
	private function stats()
	{
		$columns = $this->columns;
		$userdata = array();
		$userlist = array();
		$users = $this->mstats->getUsers();

		foreach ($users as $user)
		{
			switch ($this->aveType)
			{
				case "daily": $timeStamp = 86400;break;
				case "weekly": $timeStamp = 604800;break;
				case "monthly": $timeStamp = 2629743;break;
			}
			// to get the divisor per user
				$temp = $this->getStartEnd(NOW, $user['dateAdded']);
				$start = $temp['start']<strtotime($user['dateAdded'])?strtotime($user['dateAdded']):$temp['start'];
				$end = $temp['end'];
				
				//echo $user['name'].' - '.date("M d, Y",$start).' | '.date("M d, Y",$end)."<br>";
				
				$c = ceil((($end+86400)-$start)/$timeStamp);	
				$d = explode("-", date("Y-n-j",$start));
				$daysCount = 0;							
				for($i = 0;$i<$c;$i++){
					if($this->aveType == "daily" && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=6 && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=7 && !in_array(date("Y-m-d",mktime(1,0,0,$d[1],$d[2]+$i,$d[0])), $this->skipday))
					{
						$daysCount = $daysCount+=1;
					}
					elseif($this->aveType != "daily")
					{
						$daysCount = $daysCount+=1;
					}
					//echo date("M d, Y",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))."<br>";//for debug
				}
			//end
			//echo $daysCount.' '.$user['name'].'<br>';
			
			$userdata = $user;
			$userPrograms = $this->mstats->getUserProgram($user['userID'],$start,$end);
			
			$careerTotal = count($this->mstats->getUserProgramRecords("","",array('start'=>$start,'end'=>$end),$user['userID'])->result_array());//total;
			$careerGrouped = $daysCount;
			foreach($columns as $k=>$v)
			{
				$raw = count($this->mstats->getUserProgramRecords("",$v,array('start'=>$start,'end'=>$end),$user['userID'])->result_array());
				$userdata['career'][$k]['ave'] = round(($raw/($careerTotal==0?1:$careerTotal))*100,1);
				$userdata['career'][$k]['raw'] = $raw;
				$userdata['career'][$k]['groupAve'] = round($raw/($careerGrouped==0?1:$careerGrouped),1);
			}
			$userdata['career']['total']['raw'] = $careerTotal;
			$userdata['career']['total']['groupAve'] = round($careerTotal/($careerGrouped==0?1:$careerGrouped),1);
			$userdata['career']['total']['divisor'] = $careerGrouped;
			$userdata['programs'] = array();
			foreach($userPrograms as $userProgram)
			{
				// to get the divisor per program
					$forEnd = strtotime($userProgram['dateEnd'])>strtotime(NOW)?NOW:$userProgram['dateEnd'];// pag mas mataas ung tapos ng program kesa current date.. ung current ang set na end
					//$temp = $this->getStartEnd($forEnd, $userProgram['dateStart']);
					//$start = $temp['start'];
					//$end = $temp['end'];
					$start2 = strtotime($userProgram['dateStart']);
					$end2 = strtotime($forEnd);
					$c = round(($end2-$start2)/$timeStamp);
					$d = explode("-", date("Y-n-j",$start2));
					$daysCount = 1;
					for($i = 0;$i<$c;$i++){
						if($this->aveType == "daily" && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=6 && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=7 && !in_array(date("Y-m-d",mktime(1,0,0,$d[1],$d[2]+$i,$d[0])), $this->skipday))
						{
							$daysCount = $daysCount+=1;
						}
						elseif($this->aveType != "daily")
						{
							$daysCount = $daysCount++;
						}
					}
				//end
				//$grouped  is ung group kung daily, weekly or monthly na nag run ung program
				$userProgTotal = count($this->mstats->getUserProgramRecords($userProgram['userProgramID'],"",array('start'=>$start,'end'=>$end))->result_array());//total				
				$grouped = $daysCount;
				foreach($columns as $k=>$v)
				{
					$raw = count($this->mstats->getUserProgramRecords($userProgram['userProgramID'],$v,array('start'=>$start,'end'=>$end))->result_array());
					if(isset($userProgTotal))
					{
						$total = $userProgTotal==0?1:$userProgTotal;
						$userdata['programs'][$userProgram['program']][$k]['ave'] = round(($raw/$total)*100,1);
					}
					$userdata['programs'][$userProgram['program']][$k]['raw'] = $raw;
					$userdata['programs'][$userProgram['program']][$k]['groupAve'] = round($raw/($grouped==0?1:$grouped),1);
				}
				$userdata['programs'][$userProgram['program']]['total']['raw'] = $userProgTotal;
				$userdata['programs'][$userProgram['program']]['total']['groupAve'] = round($userProgTotal/($grouped==0?1:$grouped),1);
				$userdata['programs'][$userProgram['program']]['total']['divisor'] = $grouped;
			}
			array_push($userlist, $userdata);
		}
		$userlist = array_sort($userlist, 'career.'.$this->rankGroup.'.'.$this->rankSubGroup,SORT_DESC);
		return $userlist;		
	}
	
	private function getStartEnd($forEnd,$defaultStart)
	{
		$currentDate = date("Y-W-n-d",strtotime(NOW)); // Year YYYY week 1-53 month 1-12 days 1-31
		$dC = explode('-', $currentDate);
		switch ($this->selDateType)
		{
			case 'career': 				
				$start = strtotime($defaultStart);
				$end = strtotime($forEnd);
				break;
			case 'thisWeek':
				$start = strtotime($dC[0].'W'.sprintf('%02d',$dC[1]).'1');//format 2011W531 week=53 monday =1
				$end = strtotime($forEnd);
				break;
			case 'thisMonth':
				$start = strtotime($dC[0].'-'.$dC[2].'-1');//format yyyy-m-d
				$end = strtotime($forEnd);
				break;
			case 'thisYear':
				$start = strtotime($dC[0].'-1-1');//format yyyy-m-d
				$end = strtotime($forEnd);
				break;
			case 'prevWeek':
				$start = strtotime($dC[0].'W'.sprintf('%02d',$dC[1]-1).'1');//format 2011W531 week=53 monday =1
				$end = strtotime($dC[0].'W'.sprintf('%02d',$dC[1]-1).'5');//format 2011W531 week=53 monday =1
				break;
			case 'prevMonth':
				$start = strtotime($dC[0].'-'.($dC[2]-1).'-1');//format yyyy-m-d
				$end = strtotime($dC[0].'-'.($dC[2]-1).'-'.date('t',strtotime($dC[0].'-'.($dC[2]-1).'-1')));// end of the prev month
				break;
			case 'prevYear':
				$start = strtotime(($dC[0]-1).'-1-1');//format yyyy-m-d
				$end = strtotime(($dC[0]-1).'-12-31');//format yyyy-m-d
				break;
			case 'specificDate':
				$start = $this->dateFrom!=""?strtotime($this->dateFrom):strtotime($defaultStart);
				$end = $this->dateTo!=""?strtotime($this->dateTo):strtotime($forEnd);
				break;					
		}
			if(date('Y-m-d',$start)=='1970-01-01')$start = strtotime($defaultStart);
			if(date('Y-m-d',$end)=='1970-01-01')$end = strtotime($forEnd);

		return array('start'=>$start,'end'=>$end);
	}
	
	function index()
	{
		$data['uid'] = $this->my_session->userdata('uid');
		$data['uname'] = $this->my_session->userdata('uname');
		$data['title'] = "Statistics";
		$data['content'] = "vUserStats";
		$data['col'] = $this->columns;
		$data['aveType'] = $this->aveType;
		$data['rankGroup'] = $this->rankGroup;
		$data['rankSubGroup'] = $this->rankSubGroup;
		$data['dateType'] = $this->dateType;
		$data['selDateType'] = $this->selDateType;
		$data['dateFrom'] = $this->dateFrom;
		$data['dateTo'] = $this->dateTo;
		$data['users'] = $this->stats();
		$this->load->view('template',$data);
		//echo "<pre>";print_r($data);
	}
	function changeAveType()
	{
		if(isset($_POST['ajax']))
		{
			$this->aveType = $this->input->post('aveType');
			$this->rankGroup = $this->input->post('rankGroup');
			$this->rankSubGroup = $this->input->post('rankSubGroup');
			$this->selDateType = $this->input->post('dateType');
			$this->dateFrom = $this->input->post('dateFrom');
			$this->dateTo = $this->input->post('dateTo');
			$data['users'] = $this->stats();
			$data['dateType'] = $this->dateType;
			$data['selDateType'] = $this->selDateType;
			$data['dateFrom'] = $this->input->post('dateType') == 'specificDate'?$this->input->post('dateFrom'):$this->dateFrom;
			$data['dateTo'] = $this->input->post('dateType') == 'specificDate'?$this->input->post('dateTo'):$this->dateTo;
			$data['col'] = $this->columns;
			$data['aveType'] = $this->aveType;
			$data['rankGroup'] = $this->rankGroup;
			$data['rankSubGroup'] = $this->rankSubGroup;
			echo $this->load->view('vUserStats',$data,TRUE);
		}else redirect(site_url());
	}

}

?>