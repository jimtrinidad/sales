<?php
class Cronjobs extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function sendemail()
	{
		$this->load->library('email');
		$this->email->set_newline("\r\n");
        $this->email->from("jimtrinidad002@gmail.com","Business Sense");
        $this->email->to("jim_azz002@yahoo.com");
        $this->email->subject("Crozxczxczcn Test");        		
        $this->email->message(NOW);	
        $this->email->send();		
	}
	
	function post_weekly_top()
	{
		if($this->uri->segment(3) == 'weeklykey')
		{
			$userdata = $this->getUserRanking('weektodate');
			$lastweek = $this->getUserRanking('lastweek');
			
			$top[0] = $userdata['users'][0];
			array_shift($userdata['users']);
			foreach($userdata['users'] as $v){
				if($v['totalPoints'] == $top[0]['totalPoints']){
					array_push($top, $v);
				}
			}
			
			$content = $this->db->where('key','weekly_post_top_content')->get('tb_settings')->row()->value;
			$data['title'] = $this->db->where('key','weekly_post_top_title')->get('tb_settings')->row()->value;
			if(count($top)  > 1 ){
				$name = "";
				$end = end($top);
				foreach ($top as $k=>$v){
					if($end['id'] == $v['id']){
						$name .= " and ";
						$name .= $v['name'];
					}else{						
						$name .= $v['name'];
						$name .= ", ";
					}
				}
				$date = date('M j',$userdata['start'])." - ".date('M j',$userdata['end']);
				
				$data['content'] = str_replace(array('{name}','{date}','{points}'), array($name,$date,$top[0]['totalPoints']), $content);
				$data['postBy'] = 0;	
			}else{
				
				if($top[0]['id'] == $lastweek['users'][0]['id'])
				{
					$content = $this->db->where('key','weekly_post_consecutive')->get('tb_settings')->row()->value;
					$date = date('M j',$lastweek['start'])." - ".date('M j',$lastweek['end']).' and '.date('M j',$userdata['start'])." - ".date('M j',$userdata['end']);
					$data['content'] = str_replace(array('{name}','{date}','{points}'), array($top[0]['name'],$date,$top[0]['totalPoints']), $content);		
				}
				else 
				{
					$date = date('M j',$userdata['start'])." - ".date('M j',$userdata['end']);
					$data['content'] = str_replace(array('{name}','{date}','{points}'), array($top[0]['name'],$date,$top[0]['totalPoints']), $content);											
				}
				
				$data['postBy'] = 0;
			}
			
			if(isset($data['postBy']))
			{
				$this->load->model('msettings');
				$this->msettings->addAnnouncement($data);
			}	
			
			//echo "<pre>";print_r($data);
			//echo "<pre>";print_r($userdata['users']);
		}
		else{
			show_404();
			exit();
		}
	}

	private function getUserRanking($type)
	{
		$this->load->model('mreports');	
		
		$users = $this->mreports->getUsers();
		$userdata = array();
		$userlist = array();
		
		$currentDate = date("Y-W-n-d",strtotime(NOW)); // Year YYYY week 1-53 month 1-12 days 1-31
		$dC = explode('-', $currentDate);
		foreach ($users as $user)
		{
			switch($type)
			{
				case 'weektodate':
					$start = strtotime($dC[0].'W'.sprintf('%02d',$dC[1]).'1');
					$end = strtotime(NOW);
					break;
				case 'lastweek':
					$start = strtotime($dC[0].'W'.sprintf('%02d',$dC[1]-1).'1');
					$end = strtotime($dC[0].'W'.sprintf('%02d',$dC[1]-1).'5');
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
				$closeDeal = $this->mreports->getWonPerUser($program['pid'],$user['id'],$filters);
				$program['closeDealCount'] = count($closeDeal);
				$program['points'] = round($program['closeDealCount']*$program['pointReference'],1);
				$totalPoints += $program['points']; 
				//array_push($programdata, $program);
			}
			//$userdata['programs'] = $programdata;
			$userdata['totalPoints'] = $totalPoints;
			array_push($userlist, $userdata);
		}
		//$userlist = array_sort($userlist, 'totalPoints',SORT_DESC);
		
		foreach($userlist as $k=>$v)
		{
			$totals[$k] = $v['totalPoints'];
		}		
		
		array_multisort($totals, SORT_DESC, $userlist);		
		
		return array('users'=>$userlist,'start'=>$start,'end'=>$end);
	}

	function success_program()
	{
		if($this->uri->segment(3) == 'programkey')
		{
			$program = $this->db->where('dateEnd',date('Y-m-d',strtotime(NOW)))->get('tb_programs')->result_array();
			$str = '';			
			foreach($program as $p)
			{
				$sql = "SELECT SUM( wins ) AS wins, target, title, batch, GROUP_CONCAT( name SEPARATOR ' and ' ) AS name
						FROM (
						
							SELECT COUNT( * ) AS wins, target, title, batch, CONCAT( firstname, ' ', lastname ) AS name
							FROM tb_details dt
							JOIN tb_dates da ON dt.dateID = da.id
							JOIN tb_user_program up ON da.userProgramID = up.id
							JOIN tb_users u ON up.userID = u.id
							JOIN tb_programs p ON up.programID = p.id
							JOIN tb_programtemplate pt ON pt.id = p.programTempID
							WHERE dt.id IS NOT NULL
							AND dt.latest = 1
							AND opportunityType = 'Won'
							AND p.id = {$p['id']}
							GROUP BY up.id
							
						) AS myview ";
				
				$p_totals = $this->db->query($sql)->row();
				$percent = round( ($p_totals->wins / ($p_totals->target !=0 ? $p_totals->target : 1 ) ) * 100, 1 );			
				//echo $p_totals->wins.' - '.$p_totals->target.' - '.$percent."<br>";
				if( $percent >= 80 ) // 80 % to pos
				{
					$content = $this->db->where('key','success_program_content')->get('tb_settings')->row()->value;
					$str .= str_replace(array('{name}','{wins}','{program}','{batch}'), array($p_totals->name,$p_totals->wins,$p_totals->title,$p_totals->batch), $content);
				}
			}
			
			if( ! empty($str) )
			{
				$data['title'] = $this->db->where('key','success_program_title')->get('tb_settings')->row()->value;
				$data['content'] = $str;
				$data['postBy'] = 0;
				$this->load->model('msettings');
				//$this->msettings->addAnnouncement($data);
			}	
		}
		else{
			show_404();
			exit();
		}
	}
	
}

?>