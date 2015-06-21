<?php 
class Main extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if(!checksession('logged_in'))
		{
			$this->my_session->set_userdata('returnUrl',current_url());
			redirect(site_url('user'));
		}
		$this->load->model('mprograms');
		$this->load->library('AjaxPagination');
		$this->load->helper('trails');
	}
	
	private $excluded_user = array(30);
	
	private $items = array('searchkeyD','searchvalD','etypeD','remarkD','statusD');
	private $type = array('Incoming Call'=>'Incoming Calls','Incoming Mail'=>'Incoming Mails','Outgoing Call'=>'Outgoing Calls','Outgoing Mail'=>'Outgoing Mails');	
	
	function home()
	{
		//unset($_SESSION['userprogID']);
		$this->my_session->unset_userdata('userprogID');
		unset($_SESSION['searchkey']);
		unset($_SESSION['searchval']);
		unset($_SESSION['searchprog']);
		redirect(site_url());
		
	}
	
	
	
	function temp_index()
	{
		if(userPrivilege('isAdmin')==1)
		{
			//redirect(site_url('dashboard'));
		}
		$data['userid'] = $this->my_session->userdata('uid');
		$data['uname'] = $this->my_session->userdata('uname');
		$data['title'] = "Home";
		$data['content'] = "vhome";
		$data['show_on_home'] = TRUE;
		$data['programs'] = $this->mprograms->getUserProgram($this->my_session->userdata('uid'));	
		
		$this->load->model('msettings');
		$total = $this->db->count_all('tb_announcement');
		$config = array(
				'base_url'=>base_url().'index.php/main/ajaxannouncement',
				'total_rows'=>$total,
				'per_page'=>'1',
				'full_tag_open' => '<div>',
				'full_tag_close' => '</div>',
				'prev_link' => 'Newer &rsaquo;',
				'next_link' => '&lsaquo; Older',
				'last_link' => '',
				'first_link' => '',
				'display_pages' => FALSE,
				'uri_segment' =>'3'
		);	
		$this->ajaxpagination->initialize($config);
		$announcement = $this->msettings->old_getAnnouncement($config['per_page'],$this->uri->segment(3));
		if($announcement)
		{	
			$announcement->preview = $this->truncateString($announcement->content, 250,$announcement->id,$announcement->title);
			$announcement->postBy = $this->msettings->getUser($announcement->postBy);
			$data['announce'] = $announcement;
		}
		
		$data['isAdmin'] = userPrivilege('isAdmin');
		
		$note = $this->muser->getUserNotes(my_session_value('uid'));
		$newNotes = array();
		if(count($note)>0)
		{
			foreach ($note as $v)
			{
				$v['postBy'] = $this->msettings->getUser($v['postBy']);
				$newNotes[] = $v;
			}
			$data['notes'] = $newNotes;
		}
		
		$this->load->view('template',$data);
	}
	
	function ajaxannouncement()
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
		{
			$data = array();
			$this->load->model('msettings');
			$total = $this->db->count_all('tb_announcement');
			$config = array(
					'base_url'=>base_url().'index.php/main/ajaxannouncement',
					'total_rows'=>$total,
					'per_page'=>'1',
					'full_tag_open' => '<div>',
					'full_tag_close' => '</div>',
					'prev_link' => 'Newer &rsaquo;',
					'next_link' => '&lsaquo; Older',
					'last_link' => '',
					'first_link' => '',
					'display_pages' => FALSE,
					'uri_segment' =>'3'
			);	
			$this->ajaxpagination->initialize($config);
			$announcement = $this->msettings->old_getAnnouncement($config['per_page'],$this->uri->segment(3));
			if($announcement)
			{	
				$announcement->preview = $this->truncateString($announcement->content, 250,$announcement->id,$announcement->title);
				$announcement->postBy = $this->msettings->getUser($announcement->postBy);
				$data['announce'] = $announcement;
			}
			$data['isAdmin'] = userPrivilege('isAdmin');
			$this->load->view('ajaxAnnouncement',$data);
		}
		else
		{
			redirect(site_url());
		}
	}
	
	private function truncateString($str, $max, $id, $title, $rep = '...') 
	{
		if(strlen($str) > $max) 
		{
			$leave = $max - (strlen($rep) + strlen($title));
			$str = substr_replace($str, $rep, $leave);
			$str .=" <a href='javascript:void()' id='{$id}' class='aTrigger'>more</a>";
			return $str;
		}
		else
		{
			return $str;
		}
	}	
	
	
	function index()
	{
		if(in_array(my_session_value('uid'), $this->excluded_user))
		{
			$this->temp_index();
		}
		else 
		{
			$data['userid'] = $this->my_session->userdata('uid');
			$data['uname'] = $this->my_session->userdata('uname');
			$data['title'] = "Home";
			$data['content'] = "vhome";
			$data['show_on_home'] = FALSE;
			$data['programs'] = $this->mprograms->getUserProgram($this->my_session->userdata('uid'));	
			
			$this->load->model('msettings');
			
			if( ! my_session_value('userprogID'))
			{
				$data['announcements'] = $this->msettings->getAnnouncement();
				
				$data['isAdmin'] = userPrivilege('isAdmin');
				
				$note = $this->muser->getUserNotes(my_session_value('uid'));
				$newNotes = array();
				if(count($note)>0)
				{
					foreach ($note as $v)
					{
						$v['postBy'] = $this->msettings->getUser($v['postBy']);
						$newNotes[] = $v;
					}
					$data['notes'] = $newNotes;
				}
							
				$data['lastweek'] = $this->getUserRanking('lastweek',1);
				$data['weektodate'] = $this->getUserRanking('weektodate');
				
				$data['secondlastweeks'] = $this->getUserRanking('lastweek',2);
				$data['thirdlastweeks'] = $this->getUserRanking('lastweek',3);
				
				//kapag monday at ang bilang ng week to date ay hindi 3, ung last week result muna ang idisplay
				if(date("N",strtotime(NOW)) == 1)
				{
					$data['weektodate'] = countValGreater($data['weektodate']['users'], 'totalPoints') == 3 ? $data['weektodate'] : $data['lastweek'];
				}
				
				$data['hot_cold'] = $this->hot_cold();
				
				if(isset($_POST['graphTime'])){
					$graphTime = $_POST['graphTime'];
					if($graphTime == 'Weekly'){
						$data['graphTime'] = 'Monthly';
					}else{
						$data['graphTime'] = 'Weekly';
					}
				}else{
					$graphTime = 'Weekly';
					$data['graphTime'] = 'Monthly';
				}
				
				$data['userPending'] = $this->pendingGraph($graphTime);
				
				if(userPrivilege('isAdmin'))
				{
					$data['totalGraph'] = $this->total_graph();
				}
				
				$data['stats'] = $this->getStats(my_session_value('uid'));
				$data['rank'] = array(
									'monthly'=>array('won'=>$this->getUserRank('monthly', 'won', my_session_value('uid')),'pending'=>$this->getUserRank('monthly', 'pending', my_session_value('uid'))),
									'yearToDate'=>array('won'=>$this->getUserRank('yearToDate', 'won', my_session_value('uid')),'pending'=>$this->getUserRank('yearToDate', 'pending', my_session_value('uid'))),
									'career'=>array('won'=>$this->getUserRank('career', 'won', my_session_value('uid')),'pending'=>$this->getUserRank('career', 'pending', my_session_value('uid')))
								);
				
				$programs = $this->program_progress();
				$data['top_programs'] = array_slice(array_sort(array_filter($programs,"top_program"),'programPercent',SORT_DESC), 0, 3);
				$data['critical_programs'] = array_slice(array_sort(array_filter($programs,"critical_program"),'programPercent',SORT_ASC), 0, 3);
			}
			//echo "<pre>";print_r($data['rank']);
			$data['isAdmin'] = userPrivilege('isAdmin');
			$this->load->view('template',$data);
		}
	}

	function saveAnnouncement()
	{
		if(isset($_POST['content']))
		{
			$return['status'] = "";
			$return['title'] = "";
			$return['content'] = "";
			$this->load->model('msettings');
			$this->load->library('form_validation');
			$this->form_validation->set_rules('title','title','trim|required');
			$this->form_validation->set_rules('content','content','trim|required');
			$this->form_validation->set_message('required', '<span style="color:red;margin-right:2px;">required</span>');
			if($this->form_validation->run()===TRUE)
			{
				$data['title'] = ucfirst($this->input->post('title'));
				$data['content'] = ucfirst($this->input->post('content'));
				$data['postBy'] = $this->my_session->userdata('uid');
				if(empty($_POST['aID']))
				{
					$this->msettings->addAnnouncement($data);
				}
			}
			else 
			{
				$return['status'] = "error";
				$return['title'] = form_error('title');	
				$return['content'] = form_error('content');	
			}
			echo json_encode($return);
		}
	}
	
	function removePost()
	{
		if(isset($_POST['postID']))
		{
			$this->db->where('id',$_POST['postID'])->delete('tb_announcement');
		}
	}
	
	private function getStats($userID,$columns = '')
	{
		$this->load->model('mstats');
		
		if($columns == '')	
		{
			$columns = array('won'=>'Won','pending'=>'Pending','loss'=>'Loss','rejected'=>'Rejected');
		}
		
		$skipday = get_skip_days();
		$user = $this->mstats->getUsers($userID);
		$aveType = 'daily';
		if($user)
		{

			switch ($aveType)
			{
				case "daily": $timeStamp = 86400;break;
				case "weekly": $timeStamp = 604800;break;
				case "monthly": $timeStamp = 2629743;break;
			}
			// to get the divisor per user
			$start = strtotime($user['dateAdded']);
			$end = strtotime(NOW);
			
			//echo $user['name'].' - '.date("M d, Y",$start).' | '.date("M d, Y",$end)."<br>";
			
			$c = ceil((($end+86400)-$start)/$timeStamp);	
			$d = explode("-", date("Y-n-j",$start));
			$daysCount = 0;
			for($i = 0;$i<$c;$i++){
				if($aveType == "daily" && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=6 && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=7 && !in_array(date("Y-m-d",mktime(1,0,0,$d[1],$d[2]+$i,$d[0])), $skipday))
				{
					$daysCount = $daysCount+=1;
				}
				elseif($aveType != "daily")
				{
					$daysCount = $daysCount+=1;
				}
				//echo date("M d, Y",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))."<br>";//for debug
			}
			//end
			//echo $daysCount.' '.$user['name'].'<br>';
			
			$userdata = $user;
			$userdata['activePrograms'] = $this->mstats->getUserActiveProgram($user['userID'])->num_rows();
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
						if($aveType == "daily" && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=6 && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=7 && !in_array(date("Y-m-d",mktime(1,0,0,$d[1],$d[2]+$i,$d[0])), $skipday))
						{
							$daysCount = $daysCount+=1;
						}
						elseif($aveType != "daily")
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
			
			return $userdata;
		}
		else return FALSE;
	}
	
	function statDetails()
	{
		if(IS_AJAX)
		{
			$data['col'] = array('won'=>'Won','pending'=>'Pending','loss'=>'Loss','rejected'=>'Rejected','ic'=>'Incoming Call','im'=>'Incoming Mail','oc'=>'Outgoing Call','om'=>'Outgoing Mail');
			$data['user'] = $this->getStats(my_session_value('uid'),$data['col']);
			$data['aveType'] = 'daily';
			$selDateType = 'career';
			$this->load->view('individualStats',$data);
		}
	}
	
	private function getUserRanking($type,$week = 1)
	{
		$this->load->model('mreports');	
		
		$users = $this->mreports->getUsersExcept($this->excluded_user);
		$userdata = array();
		$userlist = array();
		
		$currentDate = date("Y-W-n-d",strtotime(NOW)); // Year YYYY week 1-53 month 1-12 days 1-31
		$dC = explode('-', $currentDate);
		foreach ($users as $user)
		{
			switch($type)
			{
				case 'weektodate':
					$start = strtotime($dC[0].'W'.sprintf('%02d',$dC[1]).'1');//format 2011W531 week=53 monday =1
					$end = strtotime(NOW);
					break;
				case 'lastweek':
					$start = strtotime($dC[0].'W'.sprintf('%02d',$dC[1]-$week).'1');//format 2011W531 week=53 monday =1
					$end = strtotime($dC[0].'W'.sprintf('%02d',$dC[1]-$week).'5');//format 2011W531 week=53 firday =5
					//echo sprintf('%02d',$dC[1]-1).'<br>';
					//echo date("F d, Y",strtotime($dC[0].'W'.sprintf('%02d',$dC[1]-1).'5')).'<br>';
					break;
			}
			//echo $user['lastname'].'-'.date("F j, Y",$temp['end']).'-'.date("F j, Y",$temp['start']).'<br>';
			$start =  $start < strtotime($user['dateAdded']) ? strtotime($user['dateAdded']) : $start;
			
			$filters = array('start'=>$start,'end'=>$end);
			
			$programdata = array();
			$totalPoints = 0;
			$userdata= $user;
			$userdata['userid'] = $user['id'];
			
			$programs = $this->mreports->getUserProgram($user['id']);
			foreach($programs as $program)
			{
				$program['closeDeal'] = $this->mreports->getWonPerUser($program['pid'],$user['id'],$filters);
				$program['closeDealCount'] = count($program['closeDeal']);
				$program['points'] = round($program['closeDealCount']*$program['pointReference'],1);
				$totalPoints += $program['points']; 
				array_push($programdata, $program);
			}
			$userdata['programs'] = $programdata;
			$userdata['totalPoints'] = $totalPoints;
			array_push($userlist, $userdata);
		}
		$userlist = array_sort($userlist, 'totalPoints',SORT_DESC);
		
		return array('users'=>array_slice($userlist, 0, 3),'start'=>$start,'end'=>$end);
	}
	
	
	private function getUserRank($time,$type,$userID)
	{
		$this->load->model('mreports');	
		$this->load->model('mstats');
		$user = array();
		
		$currentDate = date("Y-W-n-d",strtotime(NOW)); // Year YYYY week 1-53 month 1-12 days 1-31
		$dC = explode('-', $currentDate);
		
		$user = $this->mstats->getUsers($userID);
		if($user)
		{
			$user['rank'] = 'Unranked';
			$user['points'] = 0;
			switch($time)
			{
				case 'monthly':					
						switch ($type)
						{
							case 'won':
									$start = strtotime(date("Y-m-01",strtotime ( "-1 month" , strtotime ( NOW ))));//format 2011W531 week=53 monday =1
									$end = strtotime(date("Y-m-t",strtotime ( "-1 month" , strtotime ( NOW ))));//format 2011W531 week=53 firday =5
									
									$start =  $start < strtotime($user['dateAdded']) ? strtotime($user['dateAdded']) : $start;
									$filters = array('start'=>$start,'end'=>$end);
									$rank = $this->mreports->won_ranking($user['id'],$filters);
									if($rank->num_rows() > 0)
									{
										$user['rank'] = $rank->row()->rank;
										$user['points'] = $rank->row()->points;
									}
								break;
							case 'pending':
									$start = strtotime(date("Y-m-01",strtotime ( "-1 month" , strtotime ( NOW ))));//format 2011W531 week=53 monday =1
									$end = strtotime(date("Y-m-t",strtotime ( "-1 month" , strtotime ( NOW ))));//format 2011W531 week=53 firday =5
									
									$start =  $start < strtotime($user['dateAdded']) ? strtotime($user['dateAdded']) : $start;
									$filters = array('start'=>$start,'end'=>$end);
									$rank = $this->mreports->pending_ranking($user['id'],$filters);
									if($rank->num_rows() > 0)
									{
										$user['rank'] = $rank->row()->rank;
										$user['points'] = $rank->row()->pendings;
									}
								break;
						}
					break;
				case 'yearToDate':
						switch ($type)
						{
							case 'won':
									$start = strtotime(date("Y-01-01", strtotime ( NOW )));
									$end = strtotime(date("Y-m-d", strtotime ( NOW )));
									
									$start =  $start < strtotime($user['dateAdded']) ? strtotime($user['dateAdded']) : $start;
									$filters = array('start'=>$start,'end'=>$end);
									$rank = $this->mreports->won_ranking($user['id'],$filters);
									if($rank->num_rows() > 0)
									{
										$user['rank'] = $rank->row()->rank;
										$user['points'] = $rank->row()->points;
									}
								break;
							case 'pending':
									$start = strtotime(date("Y-01-01", strtotime ( NOW )));
									$end = strtotime(date("Y-m-d", strtotime ( NOW )));
									
									$start =  $start < strtotime($user['dateAdded']) ? strtotime($user['dateAdded']) : $start;
									$filters = array('start'=>$start,'end'=>$end);
									$rank = $this->mreports->pending_ranking($user['id'],$filters);
									if($rank->num_rows() > 0)
									{
										$user['rank'] = $rank->row()->rank;
										$user['points'] = $rank->row()->pendings;
									}								
								break;
						}					
					break;
				case 'career':
						switch ($type)
						{
							case 'won':
									$rank = $this->mreports->won_ranking($user['id'],'',TRUE);
									if($rank->num_rows() > 0)
									{
										$user['rank'] = $rank->row()->rank;
										$user['points'] = $rank->row()->points;
										$user['bonus'] = $rank->row()->bonus;
										$user['totalPoints'] = $rank->row()->totalPoints;
									}
								break;
							case 'pending':
									$rank = $this->mreports->pending_ranking($user['id']);
									if($rank->num_rows() > 0)
									{
										$user['rank'] = $rank->row()->rank;
										$user['points'] = $rank->row()->pendings;
									}								
								break;
						}
					break;
			}
			$user['rank'] = number_prefix($user['rank']);
		}		
		return $user;	
	}
	
	private function hot_cold()
	{
		$this->load->model('mreports');	
		
		$users = $this->mreports->getUsersExcept($this->excluded_user);
		
		$userHot = array();
		$userCold = array();

		$date = array();

		$skipday = get_skip_days();
		$i = 0;
		$limit = 14;
		$d = explode("-", date("Y-n-j",strtotime(NOW)));
		
		do
		{
			$day = date("N",mktime(1,0,0,$d[1],($d[2] - $i),$d[0]));
			$bool = $i % 2 == 0;
			if(!in_array($day, array(6,7)))
			{
				$date[] = date("Y-m-d",mktime(1,0,0,$d[1],($d[2] - $i),$d[0]));				
			}
			else $limit++;
			$i++;			
		} while( $i <= $limit );
		
		$this->mreports->getLatestWonsPerUser(12,'second',$date);
		foreach ($users as $user)
		{
			$user['today'] = $this->mreports->getLatestWonsPerUser($user['id'],'today')->num_rows();
			$user['yesterday'] = $this->mreports->getLatestWonsPerUser($user['id'],'yesterday')->num_rows();
			$user['totals'] = $this->mreports->getLatestWonsPerUser($user['id'],'all',$date)->num_rows();
			
			if($user['totals'] >= 9) array_push($userHot, $user);
			if(strtotime($user['dateAdded']) < strtotime ( '-15 day' , strtotime ( NOW )) ) array_push($userCold, $user);
		}
				
		foreach($userHot as $k=>$v)
		{
			$totalsH[$k] = $v['totals'];
			$firstH[$k] = $v['today'];
			$secondH[$k] = $v['yesterday'];
		}
		
		foreach($userCold as $k=>$v)
		{
			$totalsC[$k] = $v['totals'];
			$firstC[$k] = $v['today'];
			$secondC[$k] = $v['yesterday'];
		}		
		
		if(isset($totalsH)) array_multisort($totalsH, SORT_DESC, $firstH, SORT_DESC, $secondH, SORT_DESC, $userHot);
		if(isset($totalsC)) array_multisort($totalsC, SORT_ASC, $firstC, SORT_ASC, $secondC, SORT_ASC, $userCold);
		//echo "<pre>";print_r($date);
		return array('hot'=>array_slice($userHot, 0, 3),'cold'=>array_slice($userCold, 0, 3),'date'=>$date);
	}

	private function program_progress()
	{
		$this->load->model('mdash');	
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
			$total_week_count = count($totalweeks) > 0 ? count($totalweeks) : 1;
			$targetPerWeek = round($program['target']/$total_week_count,1);
			$currentWeek = explode('-',date("W-Y",strtotime(NOW)));
			$weekNo = $currentWeek[0] + (52 * $currentWeek[1]);
			$program['weekBefore'] = ((date('Y',$end) * 52 ) + date('W',$end)) - $weekNo;
			foreach ($temp as $week)
			{
				if($weekNo == $week)
				{
					$program['weekNo'] = key($totalweeks) + 1;
					$prefix = "";
					switch ($program['weekNo'])
					{
						case 1: $prefix = 'st week';break;
						case 2: $prefix = 'nd week';break;
						case 3: $prefix = 'rd week';break;
						default: $prefix = 'th week';break;
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
			
			$program['users'] = $this->mdash->getProgramUsers($program['id'])->result_array();
			
			array_push($dashboard, $program);
		}
		return  array_sort($dashboard, 'alertLevel',SORT_DESC);
	}
	
	function test()
	{
		echo $this->total_graph();
	}
	
	private function total_graph()
	{
		$this->load->library('amcharts/ambarchart');
		$weekBar = new AmBarChart();
		$this->load->model('mreports');	
		
		$i = 4;
				
		while($i >= 1)
		{
			$date = date('Y-W-n-d',strtotime ( "-{$i} week" , strtotime ( NOW )));
			
			$dC = explode('-', $date);
			$week_start = strtotime($dC[0].'W'.sprintf('%02d',$dC[1]).'1');
			$week_end = strtotime($dC[0].'W'.sprintf('%02d',$dC[1]).'5');

			
			$weeks[$date] = $this->mreports->getWeeklyWon('previous',$i)->row()->wons;
if($weeks[$date] >= 300) {
				$weeks[$date] = ($weeks[$date] / 10);
			}
			$points[$date] = round($this->mreports->getWeeklyWon('previous',$i)->row()->points,1);
			$weekBar->addSerie($date, date('n/j', $week_start)."<br>to<br>".date('n/j', $week_end) );
			//echo $i;
			$i--;
		}
		
		$weeks['current'] = $this->mreports->getWeeklyWon('current')->row()->wons;
		$points['current'] = round($this->mreports->getWeeklyWon('current')->row()->points,1);
		$weekBar->addSerie('current', 'This<br>Week');	
		
		$weekBar->addGraph('weeks','Totals ', $weeks,array('visible_in_legend'=>'false','width'=>'1','balloon_text'=>'<b>{value} wons</b>'));
		$weekBar->addGraph('points','Points ', $points,array('visible_in_legend'=>'false','width'=>'1','balloon_text'=>'<b>{value} points</b>'));
		
		$weekBar->setConfigAll(array("background.color"=>"#ffffff,#c1c1c1",
										'text_size'=>'10','text_color'=>'#333333','decimals_separator'=>'.',
										'width'=>'100%',
										'height'=>'364',
										'colors'=>'#5C9CCC,#ff5400',
										'column.width'=>80,'column.sequenced_grow'=>true,'column.grow_time'=>3,'column.hover_brightness'=>-20,'column.spacing'=>0,'column.corner_radius_top'=>5,
										'balloon.alpha'=>90,
										'axes.category.width'=>1,'axes.value.width'=>1,'values.category.text_size'=>9,
										'plot_area.color'=>'#ffffff,#c1c1c1','plot_area.margins.top'=>30,'plot_area.margins.left'=>30,'plot_area.margins.right'=>10,'plot_area.margins.bottom'=>0,
										'export_as_image.file'=>'index.php/charts/export','export_as_image.x'=>'!150','export_as_image.y'=>'1',
										'legend.margins'=>2,'legend.text_size'=>9,'legend.spacing'=>0,'legend.text_color_hover'=>'#333333','legend.key.size'=>12
									));		
									
		return $weekBar->getCode();
		//print_r($weeks);
	}

	function total_graph_big()
	{
		if(IS_AJAX)
		{
			$this->load->library('amcharts/ambarchart');
			$weekBar = new AmBarChart();
			$this->load->model('mreports');	
                        
                        $months = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
			
			$type = isset($_POST['group_type']) ? $_POST['group_type'] : 'Weekly';
                        $date_from = isset($_POST['date_from']) ? strtotime('next monday', strtotime($_POST['date_from'])) : strtotime('next monday', strtotime('2011-07-07'));
                        
                        $date_to = isset($_POST['date_to']) ? $_POST['date_to'] : NOW;
                        $w = date('w',strtotime($date_to));
                        if($w == 5){
                            $date_to = strtotime($date_to);
                        }elseif($w == 6 OR $w == 0){
                            $date_to = strtotime('last friday', strtotime($date_to));
                        }else{
                            $date_to = strtotime('next friday', strtotime($date_to));
                        }
                        //$date_to = isset($_POST['date_to']) ? strtotime('next friday', strtotime($_POST['date_to'])) : strtotime('next friday', strtotime(NOW));
                        $note = '';
			
			//$start = strtotime($this->mreports->getStartEndDate("Start",FALSE));
			//$i = ceil((strtotime(NOW) - $start) / 604800);
			switch ($type)
			{
				case 'Weekly':
							//$i = 20; //para ung last 20 weeks lang and ididisplay.. wala kasing scroll ung graph kaya kailangan ilimit
                                                        $count = round(($date_to-$date_from)/604800);
                                                        $i = $count;
							while($i >= 1){
								
								$weeks[date('Y-m-d',strtotime ( "-{$i} week" , $date_to))] = $this->mreports->getWeeklyWon('previous',$i,'',date('Y-m-d',$date_to))->row()->wons;
if($weeks[date('Y-m-d',strtotime ( "-{$i} week" , $date_to))] >= 300) {
									$weeks[date('Y-m-d',strtotime ( "-{$i} week" , $date_to))] = ($weeks[date('Y-m-d',strtotime ( "-{$i} week" , $date_to))] / 10);
								}
								$points[date('Y-m-d',strtotime ( "-{$i} week" , $date_to))] = round($this->mreports->getWeeklyWon('previous',$i,'',date('Y-m-d',$date_to))->row()->points,1);
								$weekBar->addSerie(date('Y-m-d',strtotime ( "-{$i} week" , $date_to)), date('n/j',strtotime ( "-{$i} week - 4 day" , $date_to))."<br>to<br>".date('n/j',strtotime ( "-{$i} week" , $date_to)));
								//echo $i;
								$i--;
							}
							if(date('W-Y', strtotime(NOW)) == date('W-Y',$date_to)){
                                                            $weeks['current'] = $this->mreports->getWeeklyWon('current')->row()->wons;
                                                            $points['current'] = round($this->mreports->getWeeklyWon('current')->row()->points,1);
                                                            $weekBar->addSerie('current', 'This<br>Week');	
                                                        }                                                                                                                
                                                        
                                                        $note = 'from '.date('M d, Y',strtotime ( "-{$count} week" , $date_to)).' to '.date('M d, Y',$date_to);
                                                        
							break;
							
				case 'Monthly':
							$count = round(($date_to-$date_from)/2629743);
                                                        $i = $count;
							while($i >= 1){
								
								$weeks[date('Y-m-d',strtotime ( "-{$i} month" , $date_to))] = $this->mreports->getGroupWon('previous',$i,'MONTH','%Y-%m')->row()->wons;
								$points[date('Y-m-d',strtotime ( "-{$i} month" , $date_to))] = round($this->mreports->getGroupWon('previous',$i,'MONTH','%Y-%m')->row()->points,1);
								$weekBar->addSerie(date('Y-m-d',strtotime ( "-{$i} month" , $date_to)), date('M-y',strtotime ( "-{$i} month" , $date_to)));
								//echo $i;
								$i--;
							}
							
                                                        if(date('m-Y', strtotime(NOW)) == date('m-Y',$date_to)){
                                                            $weeks['current'] = $this->mreports->getGroupWon('current',0,'MONTH','%Y-%m')->row()->wons;
                                                            $points['current'] = round($this->mreports->getGroupWon('current',0,'MONTH','%Y-%m')->row()->points,1);
                                                            $weekBar->addSerie('current', 'This Month');
                                                        }
                                                        
                                                        $note = 'from '.date('M Y',strtotime ( "-{$count} month" , $date_to)).' to '.date('M Y',$date_to);
                                                        
							break;
												
				case 'Yearly':
							$count = round(($date_to-$date_from)/31556926);
                                                        $i = $count;
							while($i >= 1){
								
								$weeks[date('Y-m-d',strtotime ( "-{$i} year" , $date_to))] = $this->mreports->getGroupWon('previous',$i,'YEAR','%Y')->row()->wons;
								$points[date('Y-m-d',strtotime ( "-{$i} year" , $date_to))] = round($this->mreports->getGroupWon('previous',$i,'YEAR','%Y')->row()->points,1);
								$weekBar->addSerie(date('Y-m-d',strtotime ( "-{$i} year" , $date_to)), date('Y',strtotime ( "-{$i} year" , $date_to)));
								//echo $i;
								$i--;
							}
							
							$weeks['current'] = $this->mreports->getGroupWon('current',0,'YEAR','%Y')->row()->wons;
							$points['current'] = round($this->mreports->getGroupWon('current',0,'YEAR','%Y')->row()->points,1);
							$weekBar->addSerie('current', 'This Year');
                                                        
                                                        $note = 'from '.date('Y',strtotime ( "-{$count} year" , $date_to)).' to '.date('Y',$date_to);
                                                        
							break;	
                                                        
                                case 'YTD':
                                                        $year = $this->mreports->getSalesYear();
                                                        foreach($year as $y){
                                                            $start = date('Y-m-d',  mktime(1, 0, 0, 1, 1, $y['year']));
                                                            $end = date('Y-m-d',  mktime(1, 0, 0, $_POST['months'], cal_days_in_month(CAL_GREGORIAN, $_POST['months'], $y['year']), $y['year']));
                                                            
                                                            $data = $this->mreports->getGroupWonYTD($start, $end)->row();
                                                            $weeks[$end] = $data->wons;
                                                            $points[$end] = round($data->points,1);
                                                            $weekBar->addSerie($end, date('M j, Y',strtotime ($start)).' to '.date('M j, Y',strtotime ($end)));
                                                        }
                                                        $type = 'Year to '.$months[$_POST['months']];
                                                        $note = '';
                                                        break;
						
			}
                                                
			
			$weekBar->addGraph('weeks','Totals ', $weeks,array('visible_in_legend'=>'false','width'=>'1','balloon_text'=>'<b>{value} wons</b>'));
			$weekBar->addGraph('points','Points ', $points,array('visible_in_legend'=>'false','width'=>'1','balloon_text'=>'<b>{value} points</b>'));
			$weekBar->addLabel("<b>{$type} Total Sales {$note}</b>", 0, 0,array('align'=>'center','text_size'=>'11'));
			$weekBar->setConfigAll(array("background.color"=>"#ffffff,#c1c1c1",
											'text_size'=>'10','text_color'=>'#333333','decimals_separator'=>'.',
											'width'=>'100%',
											'height'=>'350',
											'colors'=>'#5C9CCC,#ff5400',
											'column.width'=>80,'column.sequenced_grow'=>true,'column.grow_time'=>3,'column.hover_brightness'=>-20,'column.spacing'=>0,'column.corner_radius_top'=>5,
											'balloon.alpha'=>90,
											'axes.category.width'=>1,'axes.value.width'=>1,'values.category.text_size'=>9,
											'plot_area.color'=>'#ffffff,#c1c1c1','plot_area.margins.top'=>30,'plot_area.margins.left'=>40,'plot_area.margins.right'=>10,'plot_area.margins.bottom'=>0,
											'export_as_image.file'=>'index.php/charts/export','export_as_image.x'=>'!150','export_as_image.y'=>'1',
											'legend.margins'=>2,'legend.text_size'=>9,'legend.spacing'=>0,'legend.text_color_hover'=>'#333333','legend.key.size'=>12
										));		

			if(isset($_POST['graph_only']))
			{
				echo $weekBar->getCode();
			}
			else 
			{
				$data['graph'] = $weekBar->getCode();
                                $data['months'] = $months;
				echo $this->load->view('total_bar_graph_expand',$data,TRUE);
			}
		}
	}
	
	private function pendingGraph($time = 'Weekly')
	{
		$this->load->library('amcharts/ambarchart');
		$usersBar = new AmBarChart();
	
		$this->load->model('mreports');

		$users = $this->mreports->getUsersExcept($this->excluded_user);
		$userDetails = array();
		
		foreach($users as $user)
		{
			$userPrevPendings[$user['id']] = $this->mreports->getPendingPerUser($user['id'],$time,'previous')->num_rows();
			$userCurrentPendings[$user['id']] = $this->mreports->getPendingPerUser($user['id'],$time,'current')->num_rows();
			$usersBar->addSerie($user['id'], $user['firstname']);			
		}
		
		$usersBar->addGraph('prevWeek','Previous '.substr($time, 0, -2), $userPrevPendings,array('hidden'=>'false','width'=>'1','balloon_text'=>'<b>{value}</b>'));
		$usersBar->addGraph('currentWeek','Current '.substr($time, 0, -2), $userCurrentPendings,array('hidden'=>'false','width'=>'1','balloon_text'=>'<b>{value}</b>'));
		//echo "<pre>";print_r($userPrevPendings);
		$usersBar->addLabel("<b>{$time} Pending</b>", 0, 0,array('align'=>'center','text_size'=>'11'));
		$usersBar->setConfigAll(array("background.color"=>"#ffffff,#c1c1c1",
										'text_size'=>'10','text_color'=>'#333333',
										'width'=>'100%',
										'height'=>'200',
										'colors'=>'#00a608,#3f84be',
										'column.width'=>80,'column.sequenced_grow'=>true,'column.grow_time'=>5,'column.hover_brightness'=>-20,'column.spacing'=>0,'column.corner_radius_top'=>5,
										'balloon.alpha'=>90,
										'axes.category.width'=>1,'axes.value.width'=>1,'values.category.text_size'=>9,
										'plot_area.color'=>'#ffffff,#c1c1c1','plot_area.margins.top'=>30,'plot_area.margins.left'=>50,'plot_area.margins.right'=>10,'plot_area.margins.bottom'=>10,
										'export_as_image.file'=>'index.php/charts/export','export_as_image.x'=>'!150','export_as_image.y'=>'1',
										'legend.margins'=>2,'legend.text_size'=>9,'legend.spacing'=>0,'legend.text_color_hover'=>'#333333','legend.key.size'=>12
									));			
		return $usersBar->getCode();	
	}
	
//END FUNCTIONS FOR HOME	
	
	
	function program()//ajax request to get the user program
	{

		if(isset($_POST['id']) && $this->mprograms->checkUP($_POST['id'],$this->my_session->userdata('uid')))
		{
			if(isset($_POST['etypeD']))
			{
				foreach ($this->items as $value)
				{
					$filters[$value] = trim($this->input->post($value));
				}
			}
			else 
			{
				foreach ($this->items as $value)
				{
					$filters[$value] = "";
				}
			}
			$upid = $this->input->post('id');//ito ay ID ng userprogram table
			//$_SESSION['userprogID'] = $upid;
			$this->my_session->set_userdata('userprogID',$upid);
			if(isset($_POST['dateID'])&& $_POST['dateID']!="")
			{
				$today = $this->mprograms->today($upid,$_POST['dateID']);// ang $today ay ID ng dates table
				//$_SESSION['dateID'] = $_POST['dateID'];
				$this->my_session->set_userdata('dateID',$_POST['dateID']);
			}
			else
			{
				$today = $this->mprograms->today($upid);// ang $today ay ID ng dates table
				//$_SESSION['dateID'] = $today;
				$this->my_session->set_userdata('dateID',$today);
			}
			$showalldays = isset($_POST['showalldays']) && $_POST['showalldays']!=""?$upid:"";
			//$_SESSION['showalldays'] = $showalldays;
			$this->my_session->set_userdata('showalldays',$showalldays);
			$total = count($this->mprograms->details($today,"","","",$filters,$showalldays));
			$config = array(
					'base_url'=>base_url().'index.php/main/ajaxprogram/'.$upid.'/'.$today,
					'total_rows'=>$total,
					'per_page'=>'10',
					'full_tag_open' => '<div id="pagination">',
					'full_tag_close' => '</div>',
					'uri_segment' =>'5'
			);	
			$this->ajaxpagination->initialize($config);
			$start = 0;	
			$data['results'] = $this->mprograms->details($today,$config['per_page'],$start,"",$filters,$showalldays);
			$data['msg'] = $total." record(s) found";
			$data['counter']=$start;
			$data['program'] = $this->mprograms->getProgramTitle($upid);
			$data['date'] = $this->mprograms->getDateContent($today);
			$data['uname'] = $this->my_session->userdata('uname');
			$data['upid'] = $upid;
			$data['dateid'] = $today;	
			$data['events'] = $this->type;
			$data['showalldays']= $showalldays;
			$this->load->view('vDayDetails',$data);
		}
		else
		{
			redirect(site_url());		
		}		
	}
	function ajaxprogram()
	{

		if(isset($_POST['upid']))
		{
			foreach ($this->items as $value)
			{
				$filters[$value] = trim($this->input->post($value));
			}
			$upid = $this->input->post('upid');//ito ay ID ng userprogram table
			$dateid = $this->input->post('dateid');
			$showalldays = isset($_POST['showalldays']) && $_POST['showalldays']!=""?$upid:"";
			//$_SESSION['showalldays'] = $showalldays;
			$this->my_session->set_userdata('showalldays',$showalldays);
			$total = count($this->mprograms->details($dateid,"","","",$filters,$showalldays));
			$config = array(
					'base_url'=>base_url().'index.php/main/ajaxprogram/'.$upid.'/'.$dateid,
					'total_rows'=>$total,
					'per_page'=>'10',
					'full_tag_open' => '<div id="pagination">',
					'full_tag_close' => '</div>',
					'uri_segment' =>'5'
			);	
			$this->ajaxpagination->initialize($config);
			$start = $this->uri->segment(5);
			if(!empty($start) && $this->uri->segment(5)<$total):$start = $this->uri->segment(5);else:$start=0;endif;
			$data['results'] = $this->mprograms->details($dateid,$config['per_page'],$start,"",$filters,$showalldays);
			$data['msg'] = $total." record(s) found";
			$data['counter']=$start;
			$data['program'] = $this->mprograms->getProgramTitle($upid);
			$data['date'] = $this->mprograms->getDateContent($dateid);
			$data['uname'] = $this->my_session->userdata('uname');	
			$data['upid'] = $upid;
			$data['dateid'] = $dateid;
			$data['showalldays']= $showalldays;	
			$this->load->view('ajaxdetailspage',$data);
		}
		else
		{
			redirect(site_url());		
		}		
	}
	
	function getPrevDays()//ajax request to get the previous days of that program
	{
		if(isset($_POST['id']))
		{
			$upID = $this->input->post('id');
			$total = count($this->mprograms->getDates($upID)->result_array());
			$config = array(
					'base_url'=>base_url().'index.php/main/ajaxdatepage/'.$upID,
					'total_rows'=>$total,
					'per_page'=>'9',
					'full_tag_open' => '<div id="pagination">',
					'full_tag_close' => '</div>',
					'display_pages' => FALSE,
					'prev_link' => 'Newer',
					'next_link' => 'Older',
					'uri_segment' =>'4'
			);	
			$this->ajaxpagination->initialize($config);
			$start = 0;	
			$data['upid'] = $upID;
			$data['dates'] = $this->mprograms->getDates($upID,$config['per_page'],$start);
			$data['counter']=$start;
			$this->load->view('prevDays',$data);				
		}
		else
		{
			redirect(site_url());		
		}				
	}
	function ajaxdatepage()
	{
		if($this->uri->segment(3)!="")
		{
			$upID = $this->uri->segment(3);
			$total = count($this->mprograms->getDates($upID)->result_array());
			$config = array(
					'base_url'=>base_url().'index.php/main/ajaxdatepage/'.$upID,
					'total_rows'=>$total,
					'per_page'=>'9',
					'full_tag_open' => '<div id="pagination">',
					'full_tag_close' => '</div>',
					'display_pages' => FALSE,
					'prev_link' => 'Newer',
					'next_link' => 'Older',
					'uri_segment' =>'4'
			);	
			$this->ajaxpagination->initialize($config);
			$start = $this->uri->segment(4);	
			if(!empty($start) && $this->uri->segment(4)<$total):$start = $this->uri->segment(4);else:$start=0;endif;
			$data['upid'] = $upID;
			$data['dates'] = $this->mprograms->getDates($upID,$config['per_page'],$start);
			$data['counter']=$start;
			$this->load->view('ajaxdatespage',$data);
		}
		else
		{
			redirect(site_url());		
		}		
	}

	//save new event added
	function addevent()
	{
		if (isset($_POST['ajax']))
		{
			$info = array('companyName','lastname','firstname','mi','position','telephone','fax','mobile');
			$details = array('dateID','eventType','remark','opportunityType','note','cPercent','refferal');
			$this->load->library('form_validation');
			$errorstr = "";

			$this->form_validation->set_rules('eventType','mode of communication','trim|required');
			$this->form_validation->set_rules('companyName','company name','trim');
			$this->form_validation->set_rules('lastname','surname','trim|required');
			$this->form_validation->set_rules('firstname','first name','trim|required');
			$this->form_validation->set_rules('remark','remark','trim|required');
			$this->form_validation->set_rules('email','email','trim|valid_email');
			$this->form_validation->set_rules('cPercent','chance','trim|numeric');
			if($this->form_validation->run()===FALSE)
			{
				$errorstr .= validation_errors();
			}
			
			if($errorstr=="")
			{
				foreach ($info as $value)
				{
					$datainfo[$value] = ucwords(strtolower($this->input->post($value)));
				}
				$datainfo['email'] = strtolower($this->input->post('email'));
				$infoID = $this->mprograms->addInfo($datainfo);
				if($infoID)
				{
					foreach ($details as $value)
					{
						$datadetails[$value] = $this->input->post($value);
					}
					$datadetails['infoID'] = $infoID;
					if($this->mprograms->addDetails($datadetails,$this->input->post('userprogid'), $datainfo))
					{
						echo "add";
					}
				}
				else
				{
					echo "there is an error on saving.";
				}						
			}	
			else 
			{
				echo $errorstr;
			}					
		}
		else //isset ajax
		{
			redirect(site_url());		
		}		
	}

	//save event changes
	function editevent()
	{
		if (isset($_POST['ajax']))
		{
			$info = array('companyName','lastname','firstname','mi','position','telephone','fax','mobile');
			$details = array('eventType','remark','opportunityType','note','cPercent','refferal');
			$this->load->library('form_validation');
			$errorstr = "";

			$this->form_validation->set_rules('eventType','mode of communication','trim|required');
			$this->form_validation->set_rules('companyName','company name','trim');
			$this->form_validation->set_rules('lastname','surname','trim|required');
			$this->form_validation->set_rules('firstname','first name','trim|required');
			$this->form_validation->set_rules('remark','remark','trim|required');
			$this->form_validation->set_rules('email','email','trim|valid_email');
			$this->form_validation->set_rules('cPercent','chance','trim|numeric');
			if($this->form_validation->run()===FALSE)
			{
				$errorstr .= validation_errors();
			}
			
			if($errorstr=="")
			{
				foreach ($info as $value)
				{
					$datainfo[$value] = ucwords(strtolower($this->input->post($value)));
				}
				$datainfo['email'] = strtolower($this->input->post('email'));
				$this->mprograms->editInfo($datainfo,$_POST['infoID']);
				foreach ($details as $value)
				{
					$datadetails[$value] = $this->input->post($value);
				}
				$this->mprograms->editDetails($datadetails,$_POST['dID']);
				echo "edit";														
			}
			else 
			{
				echo $errorstr;
			}
		}
		else //isset ajax
		{
			redirect(site_url());
		}
	}
	
	//delete event and info
	function deleteEvent()
	{
		if(isset($_POST['ajax']))
		{
			$did = $this->input->post('did');
			$infoid = $this->input->post('infoid');
			$action = $this->mprograms->deleteEvent($did,$infoid);
			trails($action);
		}
	}
		
	function getDetails()
	{
		if(isset($_POST['ajax']))
		{
			$did = $this->input->post('id');
			$data['result'] = $this->mprograms->getDetails($did);
			$this->load->view('eventdetails',$data);
		}
	}
	function getHistory()
	{
		if(isset($_POST['ajax']))
		{
			$did = $this->input->post('id');
			$forarchieve = $this->input->post('showall');
			$data['result'] = $this->mprograms->getHistory($did,$forarchieve);
			$this->load->view('eventhistory',$data);
		}
	}
	
	function getProgramSummary()
	{
		if(isset($_POST['ajax']))
		{
			$userprogramid = $this->input->post('id');
			$latest = $this->mprograms->userProgramSummary($userprogramid,1);
			$later = $this->mprograms->userProgramSummary($userprogramid);
			$data['total'] = count($this->mprograms->userProgramSummary($userprogramid));
			$data['followup'] = $this->countWhere($later, 'latest','0');
			$data['old'] = $this->countWhere($latest, 'old','1');
			$data['new'] = $this->countWhere($latest, 'old','0');
			$data['IC'] = $this->countWhere($later, 'eventType','Incoming Call');
			$data['OC'] = $this->countWhere($later, 'eventType','Outgoing Call');
			$data['IM'] = $this->countWhere($later, 'eventType','Incoming Mail');
			$data['OM'] = $this->countWhere($later, 'eventType','Outgoing Mail');
			$data['rejected'] = $this->countWhere($latest, 'remark' ,'Rejected');
			$data['opportunity'] = $this->countWhere($latest, 'remark','Opportunity');
			$data['won'] = $this->countWhere($latest, 'opportunityType','Won');
			$data['loss'] = $this->countWhere($latest, 'opportunityType','Loss');
			$data['note'] = $this->countWhere($latest, 'opportunityType','Pending');
			$this->load->view('dailySummary',$data);
		}
	}
	
	function getDailySummary()
	{
		if(isset($_POST['ajax']))
		{
			$dateid = $this->input->post('dateid');
			$latest = $this->mprograms->details($dateid,"","",1);
			$later = $this->mprograms->details($dateid);
			$data['total'] = count($this->mprograms->details($dateid));
			$data['followup'] = $this->countWhere($later, 'latest','0');
			$data['old'] = $this->countWhere($latest, 'old','1');
			$data['new'] = $this->countWhere($latest, 'old','0');
			$data['IC'] = $this->countWhere($later, 'eventType','Incoming Call');
			$data['OC'] = $this->countWhere($later, 'eventType','Outgoing Call');
			$data['IM'] = $this->countWhere($later, 'eventType','Incoming Mail');
			$data['OM'] = $this->countWhere($later, 'eventType','Outgoing Mail');
			$data['rejected'] = $this->countWhere($latest, 'remark' ,'Rejected');
			$data['opportunity'] = $this->countWhere($latest, 'remark','Opportunity');
			$data['won'] = $this->countWhere($latest, 'opportunityType','Won');
			$data['loss'] = $this->countWhere($latest, 'opportunityType','Loss');
			$data['note'] = $this->countWhere($latest, 'opportunityType','Pending');
			$this->load->view('dailySummary',$data);
		}
	}

	//get all old record for copy, param needed(userid,programtemplate id, and those not active program)
	function getoldrecords()
	{
		if(isset($_POST['ajax']))
		{
			if(isset($_SESSION['searchkey']))
			{
				$filter['searchkey'] = $_SESSION['searchkey'];
				$filter['searchval'] = $_SESSION['searchval'];
				$filter['searchprog'] = $_SESSION['searchprog'];
				$filter['searchstatus'] = $_SESSION['searchstatus'];
			}
			else 
			{
				$filter['searchkey'] = "";
				$filter['searchval'] = "";
				$filter['searchprog'] = "";
				$filter['searchstatus'] = "all";
			}
			$userid = $this->input->post('userid');
			$programtempid = $this->input->post('programtempid');

			$total = count($this->mprograms->getOldRecords($userid,$programtempid,$filter)->result_array());
			$config = array(
					'base_url'=>base_url().'index.php/main/ajaxoldrecords/'.$userid."/".$programtempid,
					'total_rows'=>$total,
					'per_page'=>'10',
					'full_tag_open' => '<div id="pagination">',
					'full_tag_close' => '</div>',
					'uri_segment' =>'5'
			);	
			$this->ajaxpagination->initialize($config);
			$start = 0;	
			$data['results'] = $this->mprograms->getOldRecords($userid,$programtempid,$filter,$config['per_page'],$start);
			$data['counter']=$start;
			$data['userid'] = $userid;
			$data['filter'] = $filter;
			$data['msg'] = $total." record(s) found";
			$data['programtempid'] = $programtempid;
			$data['programs']=$this->mprograms->getUserPrograms($userid);//second param is programtempid
			$this->load->view('copyOld',$data);				
		}
	}
	function ajaxoldrecords()
	{
		if(isset($_POST['ajax']))
		{
			$filter['searchkey'] = $this->input->post('searchkey');
			$filter['searchval'] = $this->input->post('searchval');
			$filter['searchprog'] = $this->input->post('searchprog');
			$filter['searchstatus'] = $this->input->post('searchstatus');
			$_SESSION['searchkey'] = $filter['searchkey'];
			$_SESSION['searchval'] = $filter['searchval'];
			$_SESSION['searchprog'] = $filter['searchprog'];
			$_SESSION['searchstatus'] = $filter['searchstatus'];
			
			$userid = $this->input->post('userid');
			$programtempid = $this->input->post('programtempid');						
		}
		else 
		{
			if(isset($_SESSION['searchkey']))
			{
				$filter['searchkey'] = $_SESSION['searchkey'];
				$filter['searchval'] = $_SESSION['searchval'];
				$filter['searchprog'] = $_SESSION['searchprog'];
				$filter['searchstatus'] = $_SESSION['searchstatus'];
			}
			else 
			{
				$filter['searchkey'] = "";
				$filter['searchval'] = "";
				$filter['searchprog'] = "";
				$filter['searchstatus'] = "all";
			}
			$userid = $this->uri->segment(3);
			$programtempid = $this->uri->segment(4);	
		}
		$total = count($this->mprograms->getOldRecords($userid,$programtempid,$filter)->result_array());
		$config = array(
				'base_url'=>base_url().'index.php/main/ajaxoldrecords/'.$userid."/".$programtempid,
				'total_rows'=>$total,
				'per_page'=>'10',
				'full_tag_open' => '<div id="pagination">',
				'full_tag_close' => '</div>',
				'uri_segment' =>'5'
		);		
		$this->ajaxpagination->initialize($config);
		$start = $this->uri->segment(5);	
		if(!empty($start) && $this->uri->segment(5)<$total):$start = $this->uri->segment(5);else:$start=0;endif;
		$data['results'] = $this->mprograms->getOldRecords($userid,$programtempid,$filter,$config['per_page'],$start);
		$data['counter']=$start;
		$data['userid'] = $userid;
		$data['msg'] = $total." record(s) found";
		$data['programtempid'] = $programtempid;		
		$this->load->view('ajaxCopyOld',$data);
		//echo $_SESSION['searchkey']."|".$_SESSION['searchval'];
	}
	
	//get the record to copy
	function getOldRecForEditor()
	{
		if(isset($_POST['ajax']))
		{
			$infoid = $this->input->post('id');
			$data['record'] = $this->mprograms->getOldRecForEditor($infoid);
			$this->load->view('copyOldEditor',$data);
		}
	}
	
	//save copied record to a new events
	function copyinfo()
	{
		if (isset($_POST['ajax']))
		{
			$info = array('companyName','lastname','firstname','mi','position','telephone','fax','mobile');
			$details = array('eventType','remark','opportunityType','note','infoID','dateID','cPercent','refferal');
			$this->load->library('form_validation');
			$errorstr = "";

			$this->form_validation->set_rules('eventType','mode of communication','trim|required');
			$this->form_validation->set_rules('companyName','company name','trim');
			$this->form_validation->set_rules('lastname','surname','trim|required');
			$this->form_validation->set_rules('firstname','first name','trim|required');
			$this->form_validation->set_rules('remark','remark','trim|required');
			$this->form_validation->set_rules('email','email','trim|valid_email');
			$this->form_validation->set_rules('cPercent','chance','trim|numeric');
			if($this->form_validation->run()===FALSE)
			{
				$errorstr .= validation_errors();
			}
			
			if($errorstr=="")
			{
				foreach ($info as $value)
				{
					$datainfo[$value] = ucwords(strtolower($this->input->post($value)));
				}
				$datainfo['email'] = strtolower($this->input->post('email'));
				$this->mprograms->editInfo($datainfo,$this->input->post('infoID'));
				foreach ($details as $value)
				{
					$datadetails[$value] = $this->input->post($value);
				}
				
				if($this->mprograms->checkDateID($this->input->post('dateID'),$this->input->post('userprogid')))
				{
					$datadetails['old'] = 0;
				}
				else 
				{
					$datadetails['old'] = 1;
				}
				
				if($this->mprograms->addDetails($datadetails,$this->input->post('newuserprogid'), $datainfo))
				{
					$from = $this->mprograms->getProgramTitle($this->input->post('userprogid'));
					$to = $this->mprograms->getProgramTitle($this->input->post('newuserprogid'));
					trails("Copy ".$datainfo['firstname']." ".$datainfo['lastname']." from ".$from->title." ".$from->batch." to ".$to->title." ".$to->batch);
					echo "add";
				}														
			}	
			else 
			{
				echo $errorstr;
			}					
		}
		else //isset ajax
		{
			redirect(site_url());		
		}		
	}
	
	//initialize email editor
	function emaileditor()
	{
		if(isset($_POST['ajax']))
		{
			$detailsid = $this->input->post('detailsid');
			$data['fromDashboard'] = $this->input->post('dashboard');
			if(!empty($detailsid))
			{
				$data['result'] = $this->mprograms->getDetails($detailsid);
			}
			$data[]="";
			$this->load->view('emailEditor',$data);
		}
	}
	
	//sending email call
	function sendemail()
	{
		if(isset($_POST['ajax']))
		{
			$did=$this->input->post('detailsid');
			if(!empty($did))
			{
				$result = $this->mprograms->getDetails($did);
				$data['did'] = $did;
				$data['reciever'] = $result['firstname']." ".$result['lastname'];
				$data['emailto'] = $result['email'];
			}
			else 
			{
				$emails = $this->input->post('emailbox');
				$this->load->library('form_validation');
				if($this->form_validation->valid_emails($emails)===FALSE)
				{
					echo "invalidemail";
					exit();
				}
				$data['emailto'] = explode(",", $emails);
			}
			$data['subject']=trim($this->input->post('subject'));
			$data['message']=$this->input->post('message',FALSE);
			//print_r($data);
			$this->sendIt($data);
		}
		else
		{
			redirect(site_url());
		}
	}

	private function sendIt($data)
	{
		$sent = FALSE;
		$this->load->model('muser');
		$efrom = $this->muser->userEmail($this->my_session->userdata('uid'));
		$subject = $data['subject'];
		$message = $data['message'];
		$this->load->library('email');
		// email blast
		if(is_array($data['emailto']))
		{
			foreach ($data['emailto'] as $value)
			{
				$this->email->clear();				
				//$info = $this->mprograms->getDetails($value);
				//$programlongname = $this->mmain->programname($info['program']);
				$programlongname = "";
				
				$this->email->set_newline("\r\n");
        		$this->email->from($efrom,$programlongname);
        		$this->email->to(trim($value));
        		$this->email->subject($subject);
        		$this->email->message($message);
				if($this->email->send())
		        {
		        	$sent = TRUE;
		        }
			}
			trails("Send an email blast to : ".count($data['emailto'])." email(s). Message subject : ".$subject);
			/*
			if($this->db->get('tb_notification_setting')->first_row()->emailBlast == "1")
			{
				notify("Business Sense Notification", "send an email blast to ".count($data['emailto'])." member(s). <br>Message subject: <b>".$subject."</b><br>Message:".$message);					
			}*/				
		}
		else
		{
			$fromtitle = $this->mprograms->getTitleViaDid($data['did']);
			$this->email->set_newline("\r\n");
	        $this->email->from($efrom,$fromtitle->title);
	        $this->email->to($data['emailto']);
	        $this->email->subject($subject);
	        $this->email->message($message);

			if($this->email->send())
		    {
		        $sent = TRUE;
		        
		        trails("Send an email to : ".$data['reciever'].". Message subject : ".$subject);
		    	/*
		        if($this->db->get('tb_notification_setting')->first_row()->sendEmail == "1")
				{
					notify("Business Sense Notification", "send an email to ".$data['reciever'].". <br>Message subject: <b>".$subject."</b><br>Message:".$message);					
				}
				*/							        
		    }
		}
        
        if($sent)
        {
        	echo "Email sent";
        }
        else
        {
        	echo "Unable to send email.";
        }
	}

	//display contacts
	function showcontacts()
	{
		if(isset($_POST['ajax']))
		{
			$userid = $this->my_session->userdata('uid');
			$data['programs']=$this->mprograms->getUserPrograms($userid);
			$data['contacts']=$this->mprograms->getUserContacts($userid);
			$this->load->view('vContacts',$data);
		}
	}
	//filter contacts
	function contactfilter()
	{
		if(isset($_POST['ajax']))
		{
			$userid = $this->my_session->userdata('uid');
			$filters['infoids'] = $this->input->post('infoid');
			$filters['searchkey'] = $this->input->post('searchkey');
			$filters['searchval'] = $this->input->post('searchval');
			$filters['conProgram'] = $this->input->post('conProgram');
			$filters['searchstatus'] = $this->input->post('constatus');
			$data['contacts'] = $this->mprograms->getUserContacts($userid,$filters);
			$this->load->view('vContactAjax',$data);
		}
	}
	
	
	//function use to get totals for summary
	private function countWhere($array,$key,$condition = 1)
	{
		$array = is_array($array)?$array:array($array);
		$i = 0;
		foreach ($array as $value)
		{
			if(isset($value[$key]))
			{
				if($value[$key] == $condition)
				{
					$i++;
				}
			}
		}
		return $i;
	}	

	public function uploadxls() {

		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'xlsx|xls';

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload('xmlfile')) {
			echo json_encode(array(
					'status'	=> false,
					'message'	=> $this->upload->display_errors('','')
				));
		} else {

			$records 	= array();
			$info 		= $this->upload->data();
			$xlsResponse= readXLSFile($info['full_path']);

			$this->load->helper('email');

			if (isset($xlsResponse['status']) && $xlsResponse['status']) {
				
				$has_error			= false;
				$required_fields	= array(0,1,9,10);

				foreach ($xlsResponse['data'] as $row) {
					$all_fields_empty	= true;
					$row_error 			= false;
					$fields	= array();
					foreach ($row as $k => $value) {

						if ($value != '') {
							$all_fields_empty = false;
						}

						$value = mb_convert_encoding($value, 'UTF-8', 'auto');


						$field = array(
								'value'		=> $value,
								'error'		=> false,
								'message'	=> ''
							);

						//required fields
						if (in_array($k, $required_fields)) {
							if (trim($value) == '') {
								$row_error = true;
								$field['error']		= true;
								$field['message']	= 'This field is required.';
							}
						}

						if ($k == 11) {
							if ($row[10] == 'Opportunity') {
								if (trim($value) == '') {
									$row_error = true;
									$field['error']		= true;
									$field['message']	= 'This field is required.';
								}
							}
						}

						//email format if not empty
						if ($k == 8) {
							if (trim($value) != '' && !valid_email($value)) {
								$row_error = true;
								$field['error']		= true;
								$field['message']	= 'Invalid email value.';
							}
						}

						//pending chance numeric if not empty
						if ($k == 12) {
							if (trim($value) != '' && (!is_numeric($value) || $value > 100)) {
								$row_error = true;
								$field['error']		= true;
								$field['message']	= 'Invalid chance percentage. Number only and max of 100';
							}
						}

						//require at least one contact
						if (in_array($k, array(5,7,8))) {
							if ($row[5] == '' && $row[7] == '' && $row[8] == '') {
								$row_error = true;
								$field['error']		= true;
								$field['message']	= 'Please add atleast one way to contact this person.';
							}
						}

						$fields[]	= $field;

					}

					//alteast one field has content
					if ($all_fields_empty === false) {
						$records[]	= $fields;
						//only have to update has_error if there no previous error
						if ($has_error === false) {
							$has_error = $row_error;
						}
					}

				}

				if ($has_error === false) {
					echo json_encode(array(
							'status'	=> true,
							'message'	=> 'Please verify this record and then click save button to confirm.',
							'data'		=> array(
									'records'	=> $records,
									'file'		=> $info['file_name']
								)
						));
				} else {
					echo json_encode(array(
							'status'	=> false,
							'message'	=> 'Error found on some of the fields. Please update the file and reupload.',
							'data'		=> array(
									'records'	=> $records,
									'file'		=> $info['file_name']
								)
						));
				}

			} else {
				echo json_encode(array(
						'status'	=> false,
						'message'	=> $xlsResponse['message'],
						'data'		=> array(
										'file' => $info['file_name']
									)
					));
			}
			
		}

	}

	public function savexls() {

		if (isset($_POST['file']) && isset($_POST['dateID']) && isset($_POST['userprogid'])) {
			
			$file = './uploads/' . $_POST['file'];
			if (file_exists($file)) {

				$xlsResponse= readXLSFile($file);

				if ($xlsResponse['status'] == true) {

					$row_with_errors	= array();

					foreach ($xlsResponse['data'] as $key=>$row) {

						foreach ($row as $i=>$v) {
							$row[$i] = mb_convert_encoding($v, 'UTF-8', 'auto');
						}

						$infoData	= array(
								'companyName'	=> ucwords(strtolower($row[3])),
								'lastname'		=> ucwords(strtolower($row[0])),
								'firstname'		=> ucwords(strtolower($row[1])),
								'mi' 			=> ucwords(strtolower($row[2])),
								'position'		=> ucwords(strtolower($row[4])),
								'telephone'		=> $row[5],
								'fax'			=> $row[6],
								'mobile'		=> $row[7],
								'email'			=> strtolower($row[8])
							);

						$infoID = $this->mprograms->addInfo($infoData);
						if ($infoID) {

							$detailData	= array(
									'infoID'	=> $infoID,
									'dateID'	=> $_POST['dateID'],
									'eventType'	=> $row[9],
									'remark'	=> $row[10],
									'opportunityType'	=> $row[11],
									'note'		=> $row[13],
									'cPercent'	=> $row[12],
									'refferal'	=> $row[14]
								);

							if (!$this->mprograms->addDetails($detailData, $_POST['userprogid'], $infoData)) {
								$row_with_errors[] = $key + 1;
							}

						} else {
							$row_with_errors[] = $key + 1;
						}

					}


					if (count($row_with_errors) == 0) {

						echo json_encode(array(
								'status'	=> true,
								'message'	=> 'All excel records has been saved successfully.'
							));

					} else {

						echo json_encode(array(
								'status'	=> false,
								'message'	=> 'Excel records has been saved, except row (' . implode(', ', $row_with_errors) . '). Please retry this rows.'
							));

					}

				} else {
					echo json_encode(array(
							'status'	=> false,
							'message'	=> 'Saving failed: Cannot read file ' . $_POST['file'] . '.'
						));
				}

			} else {
				echo json_encode(array(
						'status'	=> false,
						'message'	=> 'Saving failed: Cannot find file ' . $_POST['file'] . '.'
					));
			}

		} else {
			echo json_encode(array(
					'status'	=> false,
					'message'	=> 'Saving failed: missing parameters.'
				));
		}

	}

	public function deletexls($file = '') {

		if (isset($_POST['file']) && $_POST['file'] != '') {
			$file = './uploads/' . $_POST['file'];
		}

		if ($file !== '') {
			if (file_exists($file)) {
				unlink($file);
			}
		}

	}
}
?>