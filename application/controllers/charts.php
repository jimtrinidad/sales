<?php
class Charts extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if(!checksession('logged_in'))
		{
			$this->my_session->set_userdata('returnUrl',current_url());
			redirect(site_url('user'));
		}
		if(userPrivilege('reports')!=1)
		{
			redirect(site_url());
			exit();
		}
		$this->load->helper('trails');
		$this->load->model('mreports');	
	}
	
	private $type = array('Incoming Call'=>'Incoming Calls','Incoming Mail'=>'Incoming Mails','Outgoing Call'=>'Outgoing Calls','Outgoing Mail'=>'Outgoing Mails');
	private $skipday = array('2011-08-29','2011-08-30','2011-09-27','2011-10-31','2011-11-01','2011-11-07');
	
	
	function index()
	{
		$data['uid'] = $this->my_session->userdata('uid');
		$data['uname'] = $this->my_session->userdata('uname');
		$data['title'] = "Sales Report";
		$data['content'] = "vCharts";		
		$data['alldate'] = $this->alldate();
		$data['dailyDates'] = $this->dailyDates();
		$data['compareUsers'] = $this->compareUsers();
		$data['summary'] = $this->summary();
		
		$this->load->view('template',$data);
	}
	
	function expand()
	{
		if(isset($_POST['ajax']))
		{
			$data['users'] = $this->mreports->getUsers();
			$data['chartKey'] = $this->input->post('chart');
			switch ($data['chartKey'])
			{
				case 'alldate':$data['chart'] = $this->alldate(500,'100%',array('expand'=>'','user'=>'all'),'expandAlldate');break;
				case 'dailyDates':$data['chart'] = $this->dailyDates(500,'100%',array('expand'=>'','user'=>'all'),'expandDailyDates');break;
				case 'compareUsers':$data['chart'] = $this->compareUsers(500,'100%',array('expand'=>'','eventType'=>''),'expandCompareUsers');$data['eventType']=$this->type;break;
				case 'summary':$data['chart'] = $this->summary(500);break;
				default:$data['chart'] = 'Error occur on loading the chart';break;
			}
			
			$this->load->view('expandChart',$data);
		}else redirect(site_url());
	}
	
	function reloadChart()
	{
		if(isset($_POST['ajax']))
		{
			$filters['user'] = $this->input->post('user');
			$filters['expand'] = '';
			switch ($this->input->post('chart'))
			{
				case 'alldate':$chart = $this->alldate(500,'100%',$filters,'expandAlldate');break;
				case 'dailyDates':$chart = $this->dailyDates(500,'100%',$filters,'expandDailyDates');break;
				case 'compareUsers':$chart = $this->compareUsers(500,'100%',array('expand'=>'','eventType'=>$this->input->post('eventType')),'expandCompareUsers');break;
				case 'summary':$chart = $this->summary(500,'100%',$filters);break;
				default:$chart = '<div align="center" style="height:30px;">Error occur on loading the chart</div>';break;
			}
			echo $chart;
		}else redirect(site_url());
	}
	
	private function summary($height = '400',$width= '100%',$filters = array())
	{
		$this->load->library('amcharts/amlinechart');
		$summary = new AmLineChart();

		$summary->setID('summary');
		$filters['activeUser'] = "";//set activeuser so only get records of active users
		foreach ($this->type as $k=>$v)
		{
			$currentWon = $this->mreports->getTotals($k,"Won",false,$filters);
			$won[$k] = $currentWon;
			
			$currentPending = $this->mreports->getTotals($k,'Pending',false,$filters);		
			$pending[$k] = $currentPending;
			
			$currentLoss = $this->mreports->getTotals($k,"Loss",false,$filters);	
			$loss[$k] = $currentLoss;
			
			$currentRejected = $this->mreports->getTotals($k,"Rejected",false,$filters);
			$rejected[$k] = $currentRejected;
			
			$total[$k] = ($currentWon+$currentPending+$currentLoss+$currentRejected);		
			
			$summary->addSerie($k, $v);

		}
			
		$summary->addLabel("<b>Mode of Communication Effectiveness</b>", 0, 20,array('align'=>'center','text_size'=>'12'));
		$summary->setConfigAll(array("background.color"=>"#FFFFFF",
											'text_size'=>'11','text_color'=>'#333333',
											'width'=>$width,
											'height'=>$height,'scroller.color'=>'#567F89','grid.x.color'=>'#ff0000',
											'export_as_image.file'=>'index.php/charts/export','export_as_image.x'=>'!150','export_as_image.y'=>'1',
											'colors'=>'#ff6028,#00a608,#3f84be,#bda11d,#bd1d1d,#b418c7,#14413d,#27d073,#aed027',
											'legend.margins'=>2,'legend.text_size'=>10,'legend.spacing'=>2,'legend.text_color_hover'=>'#333333',
											'plot_area.margins.top'=>'50','balloon.on_off'=>'false',
											'grid.x.approx_count'=>'4','grid.y_right.dashed'=>'true','grid.y_right.color'=>'#ff00f0','grid.y_right.alpha'=>'30',
											'values.y_right.enabled'=>'true',
											'axes.x.width'=>0,'axes.y_left.width'=>0,'axes.y_right.width'=>0,'axes.y_right.color'=>'#ff00f0','axes.y_right.alpha'=>'60'
											));
		$summary->addLabel("<b></b>", 0, 20,array('align'=>'center','text_size'=>'11'));
		
		$summary->addGraph("total", "Total", $total,array('bullet'=>'round','bullet_size'=>5,'line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}','axis'=>'right'));
		$summary->addGraph("won", "Won", $won,array('hidden'=>'true','bullet'=>'round','bullet_size'=>5,'line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}'));
		$summary->addGraph("pending", "Pending", $pending,array('bullet'=>'round','bullet_size'=>5,'hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}','axis'=>'right'));
		$summary->addGraph("loss", "Loss", $loss,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}','bullet'=>'round','bullet_size'=>5,'hidden'=>'true'));
		$summary->addGraph("rejected", "Rejected", $rejected,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}','bullet'=>'round','bullet_size'=>5,'hidden'=>'true'));		
		return $summary->getCode();		
	}
	
	function dailyDates($height = '400',$width= '100%',$filters = array('user'=>'all'),$id = "dailyDates")
	{
		$filters['activeUser'] = "";//set activeuser so only get records of active users
		$this->load->library('amcharts/amlinechart');
		$dailyDates = new AmLineChart();
		$dailyDates->setID($id);
		
		$start = strtotime($this->mreports->getStartEndDate("Start",FALSE,$filters));
		$end = strtotime($this->mreports->getStartEndDate("End",FALSE,$filters));

		$days = array();
		$date = array();
		
		$c = round(($end-$start)/86400);
		$d = explode("-", date("Y-n-j",$start));
		for($i = 0;$i<=$c;$i++)
		{
			if(date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=6 && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=7 && !in_array(date("Y-m-d",mktime(1,0,0,$d[1],$d[2]+$i,$d[0])), $this->skipday))
			{
				$days[] = date("D, m/d/y",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]));
				$date[] = date("F j, Y",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]));
			}
		}
		
		
		foreach ($date as $k=>$v)
		{
			$currentWon = $this->mreports->monthly(strtotime($v),'Won',FALSE,$filters);			
			$won[$k] = $currentWon;
			
			$currentPending = $this->mreports->monthly(strtotime($v),'Pending',FALSE,$filters);			
			$pending[$k] = $currentPending;
			
			$currentLoss = $this->mreports->monthly(strtotime($v),'Loss',FALSE,$filters);			
			$loss[$k] = $currentLoss;
			
			$currentRejected = $this->mreports->monthly(strtotime($v),'Rejected',FALSE,$filters);
			$rejected[$k] = $currentRejected;
			
			$ic[$k] = $this->mreports->monthly(strtotime($v),'Incoming Call',FALSE,$filters);
			$im[$k] = $this->mreports->monthly(strtotime($v),'Incoming Mail',FALSE,$filters);			
			$oc[$k] = $this->mreports->monthly(strtotime($v),'Outgoing Call',FALSE,$filters);
			$om[$k] = $this->mreports->monthly(strtotime($v),'Outgoing Mail',FALSE,$filters);
			
			$pendGuideLine[$k] = isset($filters['user']) && $filters['user']=='all'?(25*count($this->mreports->getUsers())):25;
			$total[$k] = ($currentWon+$currentPending+$currentLoss+$currentRejected);		
			
			$dailyDates->addSerie($k, $days[$k]);
		}
		
		$dailyDates->setConfigAll(array("background.color"=>"#FFFFFF",'decimals_separator'=>'.',
										'text_size'=>'11','text_color'=>'#333333',
										'width'=>$width,
										'height'=>$height,'scroller.color'=>'#567F89',
										'export_as_image.file'=>'index.php/charts/export','export_as_image.x'=>'!150','export_as_image.y'=>'1',
										'colors'=>'#00a608,#3f84be,#bda11d,#bd1d1d,#b418c7,#14413d,#27d073,#aed027',
										'legend.margins'=>2,'legend.text_size'=>10,'legend.spacing'=>2,'legend.text_color_hover'=>'#333333',
										'plot_area.margins.top'=>'50','balloon.on_off'=>'false',
										'legend.margins'=>2,'legend.text_size'=>10,'legend.spacing'=>2,'legend.text_color_hover'=>'#333333','legend.key.size'=>12,'legend.x'=>'40','legend.width'=>'100%',
										'grid.x.approx_count'=>'4','grid.y_right.dashed'=>'true','grid.y_right.color'=>'#ff00f0','grid.y_right.alpha'=>'30',
										'values.y_right.enabled'=>'true',
										'axes.x.width'=>0,'axes.y_left.width'=>0,'axes.y_right.width'=>0,'axes.y_right.color'=>'#ff00f0','axes.y_right.alpha'=>'60'		
									));
		$dailyDates->addLabel("<b>Daily records of all activities</b>", 0, 20,array('align'=>'center','text_size'=>'12'));
		
		if(isset($filters['expand']))
		{
			$dailyDates->addLabel("<b>Totals:</b>", '20', '!30',array('align'=>'left','text_size'=>'10','text_color'=>'#ff0000'));

			$dailyDates->addGraph("won", "Won\n<b>".number_format(countArrayValue($won))."</b>", $won,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> Won'));
			$dailyDates->addGraph("pending", "Pending\n<b>".number_format(countArrayValue($pending))."</b>", $pending,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> Pending','axis'=>'right'));
			$dailyDates->addGraph("loss", "Loss\n<b>".number_format(countArrayValue($loss))."</b>", $loss,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> Loss','hidden'=>'true'));
			$dailyDates->addGraph("rejected", "Rejected\n<b>".number_format(countArrayValue($rejected))."</b>", $rejected,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> Rejected','hidden'=>'true'));
			$dailyDates->addGraph("ic", "Incoming Calls\n<b>".number_format(countArrayValue($ic))."</b>", $ic,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> Incoming Calls'));
			$dailyDates->addGraph("im", "Incoming Mails\n<b>".number_format(countArrayValue($im))."</b>", $im,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> Incoming Mails'));
			$dailyDates->addGraph("oc", "Outgoing Calls\n<b>".number_format(countArrayValue($oc))."</b>", $oc,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> Outgoing Calls'));
			$dailyDates->addGraph("om", "Outgoing Mails\n<b>".number_format(countArrayValue($om))."</b>", $om,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> Outgoing Mails'));
			$dailyDates->addGraph("{$id}total", "Total\n<b>".number_format(countArrayValue($total))."</b>", $total,array('hidden'=>'true','line_width'=>'2','axis'=>'right','color'=>'#ff6028','balloon_text'=>'<b>{value}</b> Total','balloon_color'=>'none'));	
		}
		else
		{
			$dailyDates->addGraph("won", "Won", $won,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}'));
			$dailyDates->addGraph("pending", "Pending", $pending,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}','axis'=>'right'));
			$dailyDates->addGraph("loss", "Loss", $loss,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}','hidden'=>'true'));
			$dailyDates->addGraph("rejected", "Rejected", $rejected,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}','hidden'=>'true'));
			$dailyDates->addGraph("ic", "Incoming Calls", $ic,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}'));
			$dailyDates->addGraph("im", "Incoming Mails", $im,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}'));
			$dailyDates->addGraph("oc", "Outgoing Calls", $oc,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}'));
			$dailyDates->addGraph("om", "Outgoing Mails", $om,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}'));
			$dailyDates->addGraph("{$id}total", "Total", $total,array('hidden'=>'true','line_width'=>'2','axis'=>'right','color'=>'#ff6028','balloon_text'=>'<b>{value}</b> {title}','balloon_color'=>'none'));							
		}
		
		$dailyDates->addGraph("pGuide", "Target", $pendGuideLine,array('hidden'=>'true','line_width'=>'0','selected'=>'false','visible_in_legend'=>'false','axis'=>'right','color'=>'#555555'));
		
		return $dailyDates->getCode();			
	}

	
	function alldate($height = '400',$width= '100%',$filters = array('user'=>'all'),$id = "alldate")
	{
		$filters['activeUser'] = "";//set activeuser so only get records of active users
		$this->load->library('amcharts/amlinechart');
		$alldate = new AmLineChart();
		$alldate->setID($id);
		
		$start = strtotime($this->mreports->getStartEndDate("Start",FALSE,$filters));
		$end = strtotime($this->mreports->getStartEndDate("End",FALSE,$filters));

		$days = array();
		$date = array();
		
		$c = round(($end-$start)/86400);
		$d = explode("-", date("Y-n-j",$start));
		for($i = 0;$i<=$c;$i++)
		{
			if(date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=6 && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=7 && !in_array(date("Y-m-d",mktime(1,0,0,$d[1],$d[2]+$i,$d[0])), $this->skipday))
			{
				$days[] = date("D, m/d/y",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]));
				$date[] = date("F j, Y",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]));
			}
		}
		
		$w=0;$p=0;$l=0;$r=0;$pGuide=0;$t=0;
		$icCtr =0;$imCtr=0;$ocCtr=0;$omCtr=0;
		foreach ($date as $k=>$v)
		{
			$currentWon = $this->mreports->monthly(strtotime($v),'Won',FALSE,$filters);			
			$won[$k] = $w+= $currentWon;
			
			$currentPending = $this->mreports->monthly(strtotime($v),'Pending',FALSE,$filters);			
			$pending[$k] = $p += $currentPending;
			
			$currentLoss = $this->mreports->monthly(strtotime($v),'Loss',FALSE,$filters);			
			$loss[$k] = $l += $currentLoss;
			
			$currentRejected = $this->mreports->monthly(strtotime($v),'Rejected',FALSE,$filters);
			$rejected[$k] = $r += $currentRejected;
			
			$currentIC = $this->mreports->monthly(strtotime($v),'Incoming Call',FALSE,$filters);
			$ic[$k] = $icCtr+=$currentIC;
			
			$currentIM = $this->mreports->monthly(strtotime($v),'Incoming Mail',FALSE,$filters);	
			$im[$k] = $imCtr += $currentIM;

			$currentOC = $this->mreports->monthly(strtotime($v),'Outgoing Call',FALSE,$filters);
			$oc[$k] = $ocCtr += $currentOC;
			
			$currentOM = $this->mreports->monthly(strtotime($v),'Outgoing Mail',FALSE,$filters);
			$om[$k] = $omCtr += $currentOM;
			
			$pGuide += isset($filters['user']) && $filters['user']=='all'?(25*count($this->mreports->getUsers())):25;
			$pendGuideLine[$k] = $pGuide;			
			$total[$k] = $t += ($currentWon+$currentPending+$currentLoss+$currentRejected);		
			$alldate->addSerie($k, $days[$k]);
		}
		
		$alldate->setConfigAll(array("background.color"=>"#FFFFFF",'decimals_separator'=>'.',
										'text_size'=>'11','text_color'=>'#333333',
										'width'=>$width,
										'height'=>$height,'scroller.color'=>'#567F89',
										'export_as_image.file'=>'index.php/charts/export','export_as_image.x'=>'!150','export_as_image.y'=>'1',
										'colors'=>'#00a608,#3f84be,#bda11d,#bd1d1d,#b418c7,#14413d,#27d073,#aed027',
										'legend.margins'=>2,'legend.text_size'=>10,'legend.spacing'=>2,'legend.text_color_hover'=>'#333333','legend.key.size'=>12,'legend.x'=>'40','legend.width'=>'100%',
										'plot_area.margins.top'=>'50','balloon.on_off'=>'false',
										'grid.x.approx_count'=>'4','grid.y_right.dashed'=>'true','grid.y_right.color'=>'#ff00f0','grid.y_right.alpha'=>'30',
										'values.y_right.enabled'=>'true',
										'axes.x.width'=>0,'axes.y_left.width'=>0,'axes.y_right.width'=>0,'axes.y_right.color'=>'#ff00f0','axes.y_right.alpha'=>'60'
									));
		$alldate->addLabel("<b>Accumulated records of all activities</b>", 0, 20,array('align'=>'center','text_size'=>'12'));		
		if(isset($filters['expand']))
		{
			$alldate->addLabel("<b>Totals:</b>", '20', '!30',array('align'=>'left','text_size'=>'10','text_color'=>'#ff0000'));

			$alldate->addGraph("won", "Won\n<b>".number_format($w)."</b>", $won,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> Won'));
			$alldate->addGraph("pending", "Pending\n<b>".number_format($p)."</b>", $pending,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> Pending','axis'=>'right'));
			$alldate->addGraph("loss", "Loss\n<b>".number_format($l)."</b>", $loss,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> Loss','hidden'=>'true'));
			$alldate->addGraph("rejected", "Rejected\n<b>".number_format($r)."</b>", $rejected,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> Rejected','hidden'=>'true'));
			$alldate->addGraph("ic", "Incoming Calls\n<b>".number_format($icCtr)."</b>", $ic,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> Incoming Calls'));
			$alldate->addGraph("im", "Incoming Mails\n<b>".number_format($imCtr)."</b>", $im,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> Incoming Mails'));
			$alldate->addGraph("oc", "Outgoing Calls\n<b>".number_format($ocCtr)."</b>", $oc,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> Outgoing Calls'));
			$alldate->addGraph("om", "Outgoing Mails\n<b>".number_format($omCtr)."</b>", $om,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> Outgoing Mails'));
			$alldate->addGraph("{$id}total", "Total\n<b>".number_format($t)."</b>", $total,array('hidden'=>'true','line_width'=>'2','axis'=>'right','color'=>'#ff6028','balloon_text'=>'<b>{value}</b> Total','balloon_color'=>'none'));	
		}
		else
		{
			$alldate->addGraph("won", "Won", $won,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}'));
			$alldate->addGraph("pending", "Pending", $pending,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}','axis'=>'right'));
			$alldate->addGraph("loss", "Loss", $loss,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}','hidden'=>'true'));
			$alldate->addGraph("rejected", "Rejected", $rejected,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}','hidden'=>'true'));
			$alldate->addGraph("ic", "Incoming Calls", $ic,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}'));
			$alldate->addGraph("im", "Incoming Mails", $im,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}'));
			$alldate->addGraph("oc", "Outgoing Calls", $oc,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}'));
			$alldate->addGraph("om", "Outgoing Mails", $om,array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}'));
			$alldate->addGraph("{$id}total", "Total", $total,array('hidden'=>'true','line_width'=>'2','axis'=>'right','color'=>'#ff6028','balloon_text'=>'<b>{value}</b> {title}','balloon_color'=>'none'));				
		}		
		$alldate->addGraph("pGuide", "Target", $pendGuideLine,array('hidden'=>'true','line_width'=>'0','selected'=>'true','visible_in_legend'=>'false','axis'=>'right','color'=>'#555555','balloon_text'=>'<b>{value}</b> {title}','balloon_color'=>'none'));
		
		return $alldate->getCode();			
	}	
	
	function compareUsers($height = '400',$width= '100%',$filters = array('eventType'=>''),$id = "compareUsers")
	{
		$filters['activeUser'] = "";//set activeuser so only get records of active users
		$this->load->library('amcharts/amlinechart');
		$compareUsers = new AmLineChart();
		$compareUsers->setID($id);
		
		$start = strtotime($this->mreports->getStartEndDate("Start",FALSE,$filters));
		$end = strtotime($this->mreports->getStartEndDate("End",FALSE,$filters));

		$days = array();
		$date = array();
		
		$c = round(($end-$start)/86400);
		$d = explode("-", date("Y-n-j",$start));
		for($i = 0;$i<=$c;$i++)
		{
			if(date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=6 && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=7 && !in_array(date("Y-m-d",mktime(1,0,0,$d[1],$d[2]+$i,$d[0])), $this->skipday))
			{
				$days[] = date("D, m/d/y",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]));
				$date[] = date("F j, Y",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]));
			}
		}
		
		$users = $this->mreports->getUsers();
		$userDetails = array();		
		foreach ($date as $k=>$v)
		{
			$dailyTot = 0;
			foreach($users as $user)
			{
				$filters['user'] = $user['id'];
				$tempPerUser = $this->mreports->monthly(strtotime($v),$filters['eventType'],FALSE,$filters);
				$userDetails[$user['id']][] = $tempPerUser;
				$dailyTot += $tempPerUser;
			}
			$total[$k] = $dailyTot;
			$totalPendGuideLine[$k] = 25*count($users);
			$pendGuideLine[$k] = 25;
			$compareUsers->addSerie($k, $days[$k]);
		}
		
		$compareUsers->setConfigAll(array("background.color"=>"#FFFFFF",'decimals_separator'=>'.',
										'text_size'=>'11','text_color'=>'#333333',
										'width'=>$width,
										'height'=>$height,'scroller.color'=>'#567F89',
										'export_as_image.file'=>'index.php/charts/export','export_as_image.x'=>'!150','export_as_image.y'=>'1',
										'colors'=>'#00a608,#3f84be,#bda11d,#bd1d1d,#b418c7,#14413d,#27d073,#aed027',
										'legend.margins'=>2,'legend.text_size'=>10,'legend.spacing'=>2,'legend.text_color_hover'=>'#333333','legend.key.size'=>12,'legend.x'=>'40','legend.width'=>'100%',
										'plot_area.margins.top'=>'50','balloon.on_off'=>'false',
										'grid.x.approx_count'=>'4','grid.y_right.dashed'=>'true','grid.y_right.color'=>'#ff00f0','grid.y_right.alpha'=>'30',
										'values.y_right.enabled'=>'true',
										'axes.x.width'=>0,'axes.y_left.width'=>0,'axes.y_right.width'=>0,'axes.y_right.color'=>'#ff00f0','axes.y_right.alpha'=>'60'
									));
		$compareUsers->addLabel("<b>Compare active users activities</b>", 0, 20,array('align'=>'center','text_size'=>'12'));		
		
		$compareUsers->addGraph("total","All Users", $total,array('line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}','axis'=>'right'));
		if($filters['eventType']=="Pending"){$compareUsers->addGraph("tpGuide", "Total Target", $totalPendGuideLine,array('line_width'=>'0','selected'=>'false','visible_in_legend'=>'false','color'=>'#ff00f0','axis'=>'right'));}
		else{$compareUsers->addGraph("", "", array(),array('hidden'=>'true','line_width'=>'0','selected'=>'false','visible_in_legend'=>'false','color'=>'#ff00f0','axis'=>'right'));}
		foreach($users as $user)
		{
			$compareUsers->addGraph($user['id'],substr($user['firstname'], 0,1).". ".$user['lastname'], $userDetails["{$user['id']}"],array('hidden'=>'true','line_width'=>'2','balloon_text'=>'<b>{value}</b> {title}'));
		}
		if($filters['eventType']=="Pending"){$compareUsers->addGraph("pGuide", "Target", $pendGuideLine,array('line_width'=>'0','selected'=>'false','visible_in_legend'=>'false','color'=>'#555555'));}
		//echo "<pre>";print_r($userDetails);
		return $compareUsers->getCode();			
	}
	
	function export()
	{
		// amcharts.com export to image utility
		// set image type (gif/png/jpeg)
		$imgtype = 'jpeg';
		
		// set image quality (from 0 to 100, not applicable to gif)
		$imgquality = 100;
		
		// get data from $_POST or $_GET ?
		$data = &$_POST;
		
		// get image dimensions
		$width  = (int) $data['width'];
		$height = (int) $data['height'];
		
		// create image object
		$img = imagecreatetruecolor($width, $height);
		
		// populate image with pixels
		for ($y = 0; $y < $height; $y++) {
		  // innitialize
		  $x = 0;
		  
		  // get row data
		  $row = explode(',', $data['r'.$y]);
		  
		  // place row pixels
		  $cnt = sizeof($row);
		  for ($r = 0; $r < $cnt; $r++) {
		    // get pixel(s) data
		    $pixel = explode(':', $row[$r]);
		    
		    // get color
		    $pixel[0] = str_pad($pixel[0], 6, '0', STR_PAD_LEFT);
		    $cr = hexdec(substr($pixel[0], 0, 2));
		    $cg = hexdec(substr($pixel[0], 2, 2));
		    $cb = hexdec(substr($pixel[0], 4, 2));
		    
		    // allocate color
		    $color = imagecolorallocate($img, $cr, $cg, $cb);
		    
		    // place repeating pixels
		    $repeat = isset($pixel[1]) ? (int) $pixel[1] : 1;
		    for ($c = 0; $c < $repeat; $c++) {
		      // place pixel
		      imagesetpixel($img, $x, $y, $color);
		      
		      // iterate column
		      $x++;
		    }
		  }
		}
		
		// set proper content type
		header('Content-type: image/'.$imgtype);
		header('Content-Disposition: attachment; filename="chart.'.$imgtype.'"');
		
		// stream image
		$function = 'image'.$imgtype;
		if ($imgtype == 'gif') {
		  $function($img);
		}
		else {
		  $function($img, null, $imgquality);
		}
		
		// destroy
		imagedestroy($img);		
	}

	function programs()
	{
		$data['uid'] = $this->my_session->userdata('uid');
		$data['uname'] = $this->my_session->userdata('uname');
		$data['title'] = "Programs Bar Graph";
		$data['content'] = "vProgramBar";	

		$this->load->model('madmin');
		
		$data['programs'] = $this->madmin->get_program_template()->result_array();
		
		$data['program_type'] = isset($_SESSION['program_type']) ? $_SESSION['program_type'] : 'active';
		$data['all_programs'] = $this->allprogram_bar_graph($data['program_type']);
		$this->load->view('template',$data);
	}
	
	function changeprogramtime()
	{
		$_SESSION['program_type'] = $this->uri->segment(3);
		redirect('charts/programs');
	}
	
	private function allprogram_bar_graph($program_type = 'active')
	{
		$this->load->library('amcharts/ambarchart');
		$programBar = new AmBarChart();
		
		$this->load->model('madmin');	
		
		$programs = $this->madmin->get_program_template()->result_array();
		
		foreach($programs as $program)
		{
			$program_won[$program['id']] = $this->mreports->getWeeklyWon('program_template',$program['id'],$program_type)->row()->wons;
			//$program_point[$program['id']] = $this->mreports->getWeeklyWon('program',$program['id'])->row()->points;
			$programBar->addSerie($program['id'], $program['title']);
			//echo "<pre>";print_r($program);
		}
		
		
		$programBar->addGraph('wons','Won ', $program_won,array('visible_in_legend'=>'false','width'=>'1','balloon_text'=>'<b>{value} wons</b>'));
		//$programBar->addGraph('points','Points ', $program_point,array('visible_in_legend'=>'false','width'=>'1','balloon_text'=>'<b>{value} points</b>'));
		
		$programBar->setConfigAll(array("background.color"=>"#ffffff,#c1c1c1",
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
									
		return $programBar->getCode();		
	}
	
	function program_batch_bar()
	{
		$this->load->library('amcharts/ambarchart');
		$programBar = new AmBarChart();
		
		$i = 4;
		$programs = $this->mreports->getPrograms("","both",$this->uri->segment(3));
		
		foreach($programs as $program)
		{
			$program_won[$program['pid']] = $this->mreports->getWeeklyWon('program',$program['pid'])->row()->wons;
			//$program_point[$program['id']] = $this->mreports->getWeeklyWon('program',$program['id'])->row()->points;
                        $label = 'Batch '.$program['batch'].'<br>'.date('m/d/y',strtotime($program['dateStart'])). '<br>'.date('m/d/y',strtotime($program['dateEnd']));
			$programBar->addSerie($program['pid'], $label);
			//echo "<pre>";print_r($program_won);
		}
		
		
		$programBar->addGraph('wons','Won ', $program_won,array('visible_in_legend'=>'false','width'=>'1','balloon_text'=>'<b>{value} wons</b>'));
		//$programBar->addGraph('points','Points ', $program_point,array('visible_in_legend'=>'false','width'=>'1','balloon_text'=>'<b>{value} points</b>'));
		
		$programBar->addLabel("<b>{$program['title']}</b>", 0, 0,array('align'=>'center','text_size'=>'12'));		
		
		$programBar->setConfigAll(array("background.color"=>"#ffffff,#c1c1c1",
										'text_size'=>'10','text_color'=>'#333333','decimals_separator'=>'.',
										'width'=>'100%',
										'height'=>'364',
										'colors'=>'#5C9CCC,#ff5400',
										'column.width'=>80,'column.sequenced_grow'=>true,'column.grow_time'=>3,'column.hover_brightness'=>-20,'column.spacing'=>0,'column.corner_radius_top'=>5,
										'balloon.alpha'=>90,
										'axes.category.width'=>1,'axes.value.width'=>1,'values.category.text_size'=>9,
										'plot_area.color'=>'#ffffff,#c1c1c1','plot_area.margins.top'=>50,'plot_area.margins.left'=>45,'plot_area.margins.right'=>10,'plot_area.margins.bottom'=>0,
										'export_as_image.file'=>'index.php/charts/export','export_as_image.x'=>'!150','export_as_image.y'=>'1',
										'legend.margins'=>2,'legend.text_size'=>9,'legend.spacing'=>0,'legend.text_color_hover'=>'#333333','legend.key.size'=>12
									));		
									
		echo $programBar->getCode();
	}
}


?>