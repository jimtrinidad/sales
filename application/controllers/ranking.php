<?php 
class Ranking extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if(!checksession('logged_in'))
		{
			$this->my_session->set_userdata('returnUrl',current_url());
			redirect(site_url('user'));
		}
		if(userPrivilege('ranking')!=1)
		{
			redirect(site_url());
			exit();
		}
		unset($_SESSION['status']);//session sa dashboard
		unset($_SESSION['dateval']);//session sa dashboard		
		$this->load->model('mreports');	
	}
	
	private $dateFrom = '';
	private $dateTo = '';
	private $selDateType = 'today';
	private $dateType = array('career'=>'Career','today'=>'Today','thisWeek'=>'This week','thisMonth'=>'This month','thisYear'=>'This year',
								'prevWeek'=>'Previous week','prevMonth'=>'Previous month','prevYear'=>'Previous year','specificDate'=>'Specific range');
	private $sort_type = array('totalPoints'=>'Total Points','monthly_ave'=>'Monthly Average','months'=>'Total Months');
	private $selected_sort_type = 'totalPoints';

	
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
			case 'today':
				$start = strtotime(NOW);
				$end = strtotime(NOW);
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
				$end = strtotime($dC[0].'W'.sprintf('%02d',$dC[1]-1).'5');//format 2011W531 week=53 firday =5
				//echo sprintf('%02d',$dC[1]-1).'<br>';
				//echo date("F d, Y",strtotime($dC[0].'W'.sprintf('%02d',$dC[1]-1).'5')).'<br>';
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
		$data['title'] = "Rankings";
		$data['content'] = "vUserReport";
		
		$data['dateType'] = $this->dateType;
		$data['selDateType'] = $this->selDateType;
		$data['dateFrom'] = $this->dateFrom;
		$data['dateTo'] = $this->dateTo;
		$data['sort_type'] = $this->sort_type;
		//kapag naka career or yearly lang pwede ung per month;
		$data['selected_sort_type'] = (userPrivilege('isAdmin') AND in_array($data['selDateType'], array('career','thisYear','prevYear'))) ? $this->selected_sort_type : 'totalPoints';
		
		$users = $this->mreports->getUsers();
		$userdata = array();
		$userlist = array();
		$data['highest'] = 0;
		foreach ($users as $user)
		{
			$temp = $this->getStartEnd(NOW, $user['dateAdded']);
			//echo $user['lastname'].'-'.date("F j, Y",$temp['end']).'-'.date("F j, Y",$temp['start']).'<br>';
			$start = $temp['start']<strtotime($user['dateAdded'])?strtotime($user['dateAdded']):$temp['start'];
			$end = $temp['end'];
			
			$filters = array('start'=>$start,'end'=>$end);
			
			
			$programdata = array();
			$totalPoints = 0;
			$userdata['name'] = $user['name'];
			$userdata['userid'] = $user['id'];
			
			$bonus = $this->mreports->getUserBonusPoints($user['id'],$filters);
			$userdata['adjustment'] = 0;
			$userdata['inhouse'] = 0;
			
			$month = floor(($end - $start) / 2629743);
			
			if($this->selDateType=="career")
			{
				if(is_array($bonus))
				{
					foreach ($bonus as $v)
					{
						if($v['bonusType']=="adjustment")
						{
							$userdata['adjustment']+=$v['points'];
						}
						elseif($v['bonusType']=="inhouse")
						{
							$userdata['inhouse']+=$v['points'];
						}
					}
				}
			}
			$programs = $this->mreports->getUserProgram($user['id']);
			foreach($programs as $program)
			{
				$program['closeDeal'] = $this->mreports->getWonPerUser($program['pid'],$user['id'],$filters);
				$program['closeDealCount'] = count($program['closeDeal']);
				$program['points'] = round($program['closeDealCount']*$program['pointReference'],1);
				$totalPoints += $program['points']; 
				array_push($programdata, $program);
			}
			
			$userdata['monthly_ave'] = round(($totalPoints/($month == 0 ? 1 : $month)),1);
			
			$totalPoints += ($userdata['inhouse']+$userdata['adjustment']);
			$userdata['programs'] = $programdata;
			$userdata['totalPoints'] = $totalPoints;
			array_push($userlist, $userdata);
		}
		$userlist = $this->array_sort($userlist, $data['selected_sort_type'],SORT_DESC);
		$data['users'] = $userlist;
		//echo "<pre>";print_r($data);
		$this->load->view('template',$data);
	}
	
	function ajaxranking()
	{
		if(isset($_POST['ajax']))
		{			
			$users = $this->mreports->getUsers();
			$userdata = array();
			$userlist = array();
			$data['highest'] = 0;
			
			$this->selDateType = $this->input->post('dateType');
			$this->dateFrom = $this->input->post('dateFrom');
			$this->dateTo = $this->input->post('dateTo');
			$this->selected_sort_type = $this->input->post('sort_type');
			
			$data['dateType'] = $this->dateType;
			$data['selDateType'] = $this->selDateType;
			$data['dateFrom'] = $this->input->post('dateType') == 'specificDate'?$this->input->post('dateFrom'):$this->dateFrom;
			$data['dateTo'] = $this->input->post('dateType') == 'specificDate'?$this->input->post('dateTo'):$this->dateTo;
			$data['sort_type'] = $this->sort_type;
			
			//kapag naka career or yearly lang pwede ung per month;
			$data['selected_sort_type'] = (userPrivilege('isAdmin') AND in_array($data['selDateType'], array('career','thisYear','prevYear'))) ? $this->selected_sort_type : 'totalPoints';
			
						
			foreach ($users as $user)
			{
				
				$temp = $this->getStartEnd(NOW, $user['dateAdded']);
				//echo $user['lastname'].'-'.date("F j, Y",$temp['end']).'-'.date("F j, Y",$temp['start']).'<br>';
				$start = $temp['start']<strtotime($user['dateAdded'])?strtotime($user['dateAdded']):$temp['start'];
				$end = $temp['end'];
				
				$filters = array('start'=>$start,'end'=>$end);
				
				$month = round(($end - $start) / 2629743,1);
				
				$programdata = array();
				$totalPoints = 0;
				$userdata['name'] = $user['name'];
				$userdata['userid'] = $user['id'];
				
				$bonus = $this->mreports->getUserBonusPoints($user['id'],$filters);
				$userdata['adjustment'] = 0;
				$userdata['inhouse'] = 0;
				if($this->selDateType=="career")
				{
					if(is_array($bonus))
					{
						
						foreach ($bonus as $v)
						{
							if($v['bonusType']=="adjustment")
							{
								$userdata['adjustment']+=$v['points'];
							}
							elseif($v['bonusType']=="inhouse")
							{
								$userdata['inhouse']+=$v['points'];
							}
						}
					}
				}	
				//echo "<pre>";print_r($bonus);			
				$programs = $this->mreports->getUserProgram($user['id']);
				foreach($programs as $program)
				{
					$program['closeDeal'] = $this->mreports->getWonPerUser($program['pid'],$user['id'],$filters);
					$program['closeDealCount'] = count($program['closeDeal']);
					$program['points'] = round($program['closeDealCount']*$program['pointReference'],1);
					$totalPoints += $program['points']; 
					array_push($programdata, $program);
				}
				
				$userdata['months'] = $month < 0 ? 0 : $month;
				$userdata['monthly_ave'] = round(($totalPoints/($month == 0 ? 1 : $month)),1);
				
				$totalPoints += ($userdata['inhouse']+$userdata['adjustment']);
				$userdata['programs'] = $programdata;
				$userdata['totalPoints'] = $totalPoints;
				array_push($userlist, $userdata);
			}
			$userlist = $this->array_sort($userlist, $data['selected_sort_type'],SORT_DESC);
			$data['users'] = $userlist;
			//echo "<pre>";print_r($data);
			$this->load->view('ajaxUserReport',$data);
		}
		else
		{
			redirect(site_url());
		}
	}

	function editUserPoints()
	{
		if(isset($_POST['ajax']) && userPrivilege('isAdmin')==1)
		{
			$userbonus = $this->input->post('userbonus');
			if(is_array($userbonus))
			{
				foreach ($userbonus as $v)//0=>userid,1=>bonustype,2=>points
				{
					$data['user_id'] = $v[0];
					$data['bonusType'] = $v[1];
					$data['points'] = $v[2];
					$data['dateAdded'] = date("Y-m-d",strtotime(NOW));
					//echo "<br>";print_r($data);
					$this->mreports->updateBonusPoints($data);
				}
			}
			//print_r($data);
			$this->ajaxranking();			
		}
	}
	
	private function array_sort($array, $on, $order=SORT_ASC)
	{
	    $new_array = array();
	    $sortable_array = array();
	
	    if (count($array) > 0) {
	        foreach ($array as $k => $v) {
	            if (is_array($v)) {
	                foreach ($v as $k2 => $v2) {
	                    if ($k2 == $on) {
	                        $sortable_array[$k] = $v2;
	                    }
	                }
	            } else {
	                $sortable_array[$k] = $v;
	            }
	        }
	
	        switch ($order) {
	            case SORT_ASC:
	                asort($sortable_array);
	            break;
	            case SORT_DESC:
	                arsort($sortable_array);
	            break;
	        }
	
	        foreach ($sortable_array as $k => $v) {
	            $new_array[$k] = $array[$k];
	        }
	    }
	
	    return $new_array;
	}

	function getDetails()
	{
		if(isset($_POST['ajax']))
		{
			$programid = $this->input->post('programid');
			$userid = $this->input->post('userid');
			$this->selDateType = $this->input->post('dateType');
			$this->dateFrom = $this->input->post('dateFrom');
			$this->dateTo = $this->input->post('dateTo');
			
			$user = $this->mreports->getUsers($userid);
			$temp = $this->getStartEnd(NOW, $user->dateAdded);
			$start = $temp['start']<strtotime($user->dateAdded)?strtotime($user->dateAdded):$temp['start'];
			$end = $temp['end'];
			$filters = array('start'=>$start,'end'=>$end);
			
			$data['records'] = $this->mreports->getWonPerUser($programid,$userid,$filters);
			$this->load->view('vRankDetails',$data);
		}
	}
		
}

?>