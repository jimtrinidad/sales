<?php 
ini_set('memory_limit', '-1');
class Reports extends CI_Controller
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
		unset($_SESSION['status']);//session sa dashboard
		unset($_SESSION['dateval']);//session sa dashboard		
		$this->load->model('mreports');		
		$this->load->library('pagination');
		$this->load->library('ofc2');
	}
	
	private $items = array('programType','user','program','etype','remark','statusR','date','latest','qsearchkey','qsearchval','orderby','ordertype');
	private $type = array('Incoming Call'=>'<br>Incoming Calls','Incoming Mail'=>'<br>Incoming Mails','Outgoing Call'=>'<br>Outgoing Calls','Outgoing Mail'=>'<br>Outgoing Mails');	
	private $month = array('1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July','8'=>'August',
								'9'=>'September','10'=>'October','11'=>'November','12'=>'December');

	function index()
	{
		$data['uid'] = $this->my_session->userdata('uid');
		$data['uname'] = $this->my_session->userdata('uname');
		$data['title'] = "Sales Report";
		$data['content'] = "vreports";
		$data['activeusers'] = $this->mreports->getUsers("",TRUE);
		$data['inactiveusers'] = $this->mreports->getUsers("",FALSE);
		$data['events'] = $this->type;
		$data['month'] = $this->month;
		
		if(isset($_SESSION['user']))
		{
			foreach ($this->items as $value)
			{
				$filters[$value] = $_SESSION[$value];
			}
		}
		else 
		{
			foreach ($this->items as $value)
			{
				$filters[$value] = "";
				
			}
			$filters['program'] = 'all';
			$filters['programType'] = 'active';		
		}
		$data['programs'] = $this->getProgram($this->mreports->getPrograms("",$filters['programType']),$filters['programType']);
		//$data['programsGraph'] = $this->getProgram($this->mreports->getPrograms());
		$total = count($this->mreports->searchResult("","",$filters)->result_array()); //count all the result without limit
		$config = array(
				'base_url'=>base_url().'index.php/reports/index',
				'total_rows'=>$total,
				'per_page'=>'30',
				'full_tag_open' => '<div id="pagination">',
				'full_tag_close' => '</div>'
		);	
		$this->pagination->initialize($config);
		$start = $this->uri->segment(3);
		if(!empty($start) && $this->uri->segment(3)<$total):$start = $this->uri->segment(3);else:$start="0";endif;
		$data['results'] = $this->mreports->searchResult($config['per_page'],$start,$filters);		
		$data['counter']=$start;
		$data['msg']=$total." record(s) found";
		$data['filters'] = $filters;
		
		$data['chart_height'] = '350';
		$data['chart_width'] = '100%';
		$data['data_url'] = site_url('reports/graph/all/all');
		
		//echo "<pre>";print_r($data['results']->result_array());
		$this->load->view('template',$data);
		
	}

	function filter()
	{
		if(isset($_POST['ajax']))
		{
			$data['content'] = "vreports";
			$data['users'] = $this->mreports->getUsers();
			$data['programs'] = $this->mreports->getPrograms();
			
			foreach ($this->items as $value)
			{
				$filters[$value] = trim($this->input->post($value));
				$_SESSION[$value] = $filters[$value];
			}
			$total = count($this->mreports->searchResult("","",$filters)->result_array()); //count all the result without limit
			$config = array(
					'base_url'=>base_url().'index.php/reports/index',
					'total_rows'=>$total,
					'per_page'=>'30',
					'full_tag_open' => '<div id="pagination">',
					'full_tag_close' => '</div>'
			);	
			$this->pagination->initialize($config);
			$start = $this->uri->segment(3);
			if(!empty($start) && $this->uri->segment(3)<$total):$start = $this->uri->segment(3);else:$start="0";endif;
			$data['results'] = $this->mreports->searchResult($config['per_page'],$start,$filters);		
			$data['counter']=$start;
			$data['msg']=$total." record(s) found";
			$this->load->view('vReportAjax',$data);
		}
	}
	private function getProgram($programs = array(),$programStatus = "active")
	{
		$str = "";
		$str .= "<option value='all' ";
		$str .= isset($_SESSION['program']) && $_SESSION['program']=='all'?"selected='selected'":"";
		$str .= ">All</option> ";
		
		if($programStatus == "both" || $programStatus == "active")
		{
			$str .= "<optgroup label='Active programs'>";
			foreach ($programs as $program)
			{
				if($program['pActive'])
				{					
					$str .= "<option value='{$program['pid']}' ";
					$str .= isset($_SESSION['program']) && $_SESSION['program']==$program['pid']?"selected='selected'":"";
					$str .= ">".$program['title'].' '.$program['batch']."</option>";					
				}
			}
			$str .= "</optgroup>";
		}
		if($programStatus == "both" || $programStatus == "inactive")
		{
			$str .= "<optgroup label='Inactive programs'>";
			foreach ($programs as $program)
			{
				if($program['pActive']==0)
				{					
					$str .= "<option value='{$program['pid']}' ";
					$str .= isset($_SESSION['program']) && $_SESSION['program']==$program['pid']?"selected='selected'":"";
					$str .= ">".$program['title'].' '.$program['batch']."</option>";					
				}
			}
			$str .= "</optgroup>";
		}

		return $str;
	}
	function getProgramAjax()
	{
		if (isset($_POST['ajax']))
		{
			$programs = $this->mreports->getPrograms("",$_POST['programType']);
			echo $this->getProgram($programs,$_POST['programType']);
		}
	}
	
	function getDetails()
	{
		if(isset($_POST['ajax']))
		{
			$did = $this->input->post('id');
			$data['result'] = $this->mreports->getDetails($did);
			$this->load->view('reportDetails',$data);
		}
	}

	
	function excel()
	{
		if(isset($_SESSION['user']))
		{
			foreach ($this->items as $value)
			{
				$filters[$value] = $_SESSION[$value];
			}
		}
		else 
		{
			foreach ($this->items as $value)
			{
				$filters[$value] = "";
			}			
		}
		
		
		$resultArray = $this->mreports->searchResult("","",$filters)->result_array();
		$data['counttotal'] = count($resultArray);
		if(count($resultArray)>0)
		{
			$data['countall'] = count($resultArray);
		}else 
		{
			$data['countall']=1;
		}
		$data['ic'] = $this->countWhere($resultArray, 'eventType','Incoming Call');
		$data['im'] = $this->countWhere($resultArray, 'eventType','Incoming Mail');
		$data['oc'] = $this->countWhere($resultArray, 'eventType','Outgoing Call');
		$data['om'] = $this->countWhere($resultArray, 'eventType','Outgoing Mail');
		$data['won'] = $this->countWhere($resultArray, 'opportunityType','Won');
		$data['pending'] = $this->countWhere($resultArray, 'opportunityType','Pending');
		$data['loss'] = $this->countWhere($resultArray, 'opportunityType','Loss');
		$data['rejected'] = $this->countWhere($resultArray, 'remark','Rejected');
	
			//dun na ko sa pag display ng mga summary,, ayun ung array sa line 164...
		$this->load->library('phpexcel');

		$a = array(array('Name','Program','Batch','Position','Company','Telephone','Mobile','Fax','Email','Mode of Communication','Date','Time','Result','Note','Refferal','Sales Representative'));
		$b = $this->mreports->searchResult("","",$filters,TRUE)->result_array();
		$c = array(array(''),array(),
							array('Summary'),
							array('Total Results:',$data['counttotal']),
							array('Total Incoming Calls',$data['ic']." (".round(($data['ic']/$data['countall'])*100,2)."%)",'',
										'Won',$data['won']." (".round(($data['won']/$data['countall'])*100,2)."%)"),
							array('Total Incoming Mails',$data['im']." (".round(($data['im']/$data['countall'])*100,2)."%)",'',
										'Pending',$data['pending']." (".round(($data['pending']/$data['countall'])*100,2)."%)"),
							array('Total Outgoing Calls',$data['oc']." (".round(($data['oc']/$data['countall'])*100,2)."%)",'',
										'Loss',$data['loss']." (".round(($data['loss']/$data['countall'])*100,2)."%)"),
							array('Total Outgoing Mails',$data['om']." (".round(($data['om']/$data['countall'])*100,2)."%)",'',
										'Rejected',$data['rejected']." (".round(($data['rejected']/$data['countall'])*100,2)."%)")
							);
		$a = array_merge($a,$b,$c);
		$time = date("m-d-y");
		$this->phpexcel->addArray($a);
		$colWidth = "<ss:Column ss:Width='110'/>
                		<ss:Column ss:Width='70'/>
                		<ss:Column ss:Width='30'/>
                		<ss:Column ss:Width='150'/>
                		<ss:Column ss:Width='150'/>
                		<ss:Column ss:Width='80'/>
                		<ss:Column ss:Width='80'/>
                		<ss:Column ss:Width='80'/>
                		<ss:Column ss:Width='120'/>
                		<ss:Column ss:Width='100'/>
                		<ss:Column ss:Width='80'/>
                		<ss:Column ss:Width='80'/>
                		<ss:Column ss:Width='80'/>
                		<ss:Column ss:Width='120'/>	
                		<ss:Column ss:Width='120'/>	 
                		<ss:Column ss:Width='100'/>	                 		
                ";
		$this->phpexcel->generateXML($time.'-Report',$colWidth);
		//$this->phpexcel->e();// echo string for debugging -gawa ko
	}//end excel	
	
	/*
	//graph selector
	function opengraph()
	{
		if(isset($_POST['ajax']))
		{
			$user = $this->input->post('user');
			$program = $this->input->post('program');
			$graph = $this->input->post('graphtype');
			$month = $this->input->post('month');
			$year = $this->input->post('year');
			$classification = $this->input->post('classification');
			$startdate = strtotime($this->input->post('startdate'));
			$enddate = strtotime($this->input->post('enddate'));
			
			if($graph=="graph")
			{
				$url =  site_url("reports/".$graph."/".$user."/".$program);	
			}
			else if($graph=="monthly")
			{
				$url = site_url("reports/".$graph."/".$month."/".$year."/".$classification."/".$program);
			}
			else if($graph=="perprogram")
			{
				$url = site_url("reports/".$graph."/".$classification."/".$program);
			}
			else if($graph=="rangedate")
			{
				$url = site_url("reports/perprogram/".$classification."/".$program."/".$startdate."/".$enddate);
			}

			$data = array(
				'chart_height'  => 400,
				'chart_width'   => '100%',
				'data_url'      => $url
			); 
			//echo $data['data_url'];     
	       	$this->load->view('vGraphAjax', $data);
		}
		else 
		{
			redirect(site_url('reports'));
		}	
	}	

	function graph()
	{
		$user = $this->uri->segment(3);
		$program = $this->uri->segment(4);
		
		if($user!="all")
		{
			$name = $this->mreports->getUsers($user)->name;
		}
		else 
		{
			$name = "all user";
		}
		if($program != "all")
		{
			$pname = $this->mreports->getPrograms($program);
			$programname = $pname->title." ".$pname->batch;
		}
		else 
		{
			$programname = "all programs";
		}
		$titlestr = "Statistics of {$name} on {$programname}";
			
		$max = 1;
		$i = 0;
		
		$won = array();
		$loss = array();
		$pending = array();
		$rejected = array();
		$event = array();
		
		$label = array();
		
		$tWon = 0;
		$tLoss = 0;
		$tPend = 0;
		$tRej = 0;
		
		foreach ($this->type as $k=>$v)
		{

			$won[] = $this->mreports->getTotals($k,"Won",$user,$program);
			$loss[] = $this->mreports->getTotals($k,"Loss",$user,$program);
			$pending[] = $this->mreports->getTotals($k,'Pending',$user,$program);
			$rejected[] = $this->mreports->getTotals($k,"Rejected",$user,$program);	
			$event[] = $this->mreports->getSummary('eventType',$k,$user,$program);

			$label[] = $v;
			
           	$max = ($max<$won[$i])?$won[$i]:$max;
           	$max = ($max<$loss[$i])?$loss[$i]:$max;	
			$max = ($max<$pending[$i])?$pending[$i]:$max;	
			$max = ($max<$rejected[$i])?$rejected[$i]:$max;	
			
			$max = ($max<$event[$i])?$event[$i]:$max;	

            $tWon += $won[$i];
            $tLoss += $loss[$i];
            $tPend += $pending[$i];
            $tRej += $rejected[$i];	
            $i++;
		}
		//$total = $tWon+$tLoss+$tPend+$tRej;
		$total = $this->mreports->getSummary("","",$user,$program);
		$new = $this->mreports->getSummary('old','0',$user,$program);
		$old = $this->mreports->getSummary('old','1',$user,$program);
		$ic = $this->mreports->getSummary('eventType','Incoming Call',$user,$program);
		$im = $this->mreports->getSummary('eventType','Incoming Mail',$user,$program);
		$oc = $this->mreports->getSummary('eventType','Outgoing Call',$user,$program);
		$om = $this->mreports->getSummary('eventType','Outgoing Mail',$user,$program);
		
		$step = round($max / 15);

		$tt = $total==0?1:$total;//divisor.. change to 1 if value is zero

		$eventbar = new bar_3d();
		$eventbar->colour('#567f89');
		$eventbar->set_on_show(new bar_on_show('grow-up', 2.5, 0));
		$eventbar->set_values( $event );
		$eventbar->set_tooltip( "Total: #val#" );
		$eventbar->set_alpha(.8);		
		
		$wonbar = new bar_3d();
		$wonbar->colour('#63be3f');
		$wonbar->set_on_show(new bar_on_show('grow-up', 2.5, 0));
		$wonbar->set_values( $won );
		$wonbar->set_tooltip( "#val# Won" );
		$wonbar->set_alpha(.8);
		
		$lossbar = new bar_3d();
		$lossbar->colour('#bda11d');
		$lossbar->set_on_show(new bar_on_show('grow-up', 2.5, 0));
		$lossbar->set_values( $loss );
		$lossbar->set_tooltip( "#val# Loss" );	
		$lossbar->set_alpha(.8);


		$notebar = new bar_3d();
		$notebar->colour('#3f84be');
		$notebar->set_on_show(new bar_on_show('grow-up', 2.5, 0));
		$notebar->set_values( $pending );
		$notebar->set_tooltip( "#val# Pending" );
		$notebar->set_alpha(.8);
		
		$rejbar = new bar_3d();
		$rejbar->colour('#bd1d1d');
		$rejbar->set_on_show(new bar_on_show('grow-up', 2.5, 0));
		$rejbar->set_values( $rejected );
		$rejbar->set_tooltip( "#val# Rejected" );	
		$rejbar->set_alpha(.8);			
		
		$x_labels = new x_axis_labels();
		$x_labels->set_labels( $label );
		$x_labels->set_colour("#567f89");
		
		$x_axis = new x_axis();
		$x_axis->set_labels( $x_labels ); 
		
		$y = new y_axis();
		$y->set_range( 0, $step+$max, $step );
		
		$title = new title($titlestr);
		$title->set_style("{font-size: 20px; font-weight: bold; color: #567f89;}");

		$t = new tooltip();
		$t->set_shadow( false );
		$t->set_stroke( 3 );
		$t->set_colour( "#567f89" );
		$t->set_background_colour( "#e8e8e8" );
		$t->set_body_style( "{font-size: 10px; font-weight: bold; color: #000000;}" );	
		
		$xlegend = new x_legend("Total Record: {$total} | New: {$new} (".round(($new/$tt)*100,2)."%) | Old: {$old} (".round(($old/$tt)*100,2)."%) \n Incoming Calls: {$ic} (".round(($ic/$tt)*100,2)."%) Incoming Mails: {$im} (".round(($im/$tt)*100,2)."%) Outgoing Calls: {$oc} (".round(($oc/$tt)*100,2)."%) Outgoing Mails: {$om} (".round(($om/$tt)*100,2)."%) \n Total Won: {$tWon} (".round(($tWon/$tt)*100,2)."%)  Total Pending: {$tPend} (".round(($tPend/$tt)*100,2)."%)  Total Loss: {$tLoss} (".round(($tLoss/$tt)*100,2)."%)  Total Rejected: {$tRej} (".round(($tRej/$tt)*100,2)."%)");
		$xlegend->set_style('{font-family:tahoma;font-weight:bold;font-size:12px; color: #567f89;}');
		
		$chart = new open_flash_chart();
		$chart->set_tooltip($t);
		$chart->set_title( $title );
		$chart->add_element( $eventbar );
		$chart->add_element( $wonbar );
		$chart->add_element( $notebar );		
		$chart->add_element( $lossbar );
		$chart->add_element( $rejbar );	
		$chart->set_bg_colour( '#e8e8e8' );
		$chart->set_x_axis( $x_axis );
		$chart->set_y_axis( $y );
		$chart->set_x_legend($xlegend);
		
		echo $chart->toPrettyString();
	}
	
	function monthly()
	{
		$month = $this->uri->segment(3);
		$year = $this->uri->segment(4);
		$classification = $this->uri->segment(5);
		$program = $this->uri->segment(6);
		
		if($program != "all")
		{
			$pname = $this->mreports->getPrograms($program);
			$programname = $pname->title." ".$pname->batch;
		}
		else 
		{
			$programname = "";
		}
		
		$days = array();
		$color = array('Won'=>'#63be3f','Pending'=>'#3f84be','Loss'=>'#bda11d','Rejected'=>'#bd1d1d','IC'=>'#b418c7','IM'=>'#14413d','OC'=>'#27d073','OM'=>'#aed027');
		$c = date("t",mktime(1,0,0,$month,1,$year));
		
		for($i = 1;$i<=$c;$i++)
		{
			if(date("N",mktime(1,0,0,$month,$i,$year))!=6 && date("N",mktime(1,0,0,$month,$i,$year))!=7)
			{
				$days[] = date("D, M j",mktime(1,0,0,$month,$i,$year));
				$date[] = date("F j, Y",mktime(1,0,0,$month,$i,$year));
			}
		}
		
		$titlestr = $this->month[$month]." ".$year." ".$programname." Statistics";
		$event = array('IM'=>'Incoming Mail','IC'=>'Incoming Call','OM'=>'Outgoing Mail','OC'=>'Outgoing Call');
		if(in_array($classification, array('IC','IM','OC','OM')))
		{
			$classification = $event[$classification];
		}
		
		
		$data = array();
		$total = 0;
		$new = 0;
		$old = 0;
		$i = 0;
		$max = 1;
		foreach ($date as $v)
		{
			$data[] = $this->mreports->monthly(strtotime($v),$classification,$program);
			$total += $data[$i];
			$max = ($max<$data[$i])?$data[$i]:$max;
			
			$new += $this->mreports->getSummary('old','0',"all",$program,strtotime($v),$classification);
			$old += $this->mreports->getSummary('old','1',"all",$program,strtotime($v),$classification);
			$i++;
		}
		$tt = $total==0?1:$total;//divisor.. change to 1 if value is zero
		$step = round($max / 15);

		$x = new bar_3d();
		$x->colour("{$color[$this->uri->segment(5)]}");
		$x->set_on_show(new bar_on_show('grow-up', 2.5, 0));
		$x->set_values( $data );
		$x->set_tooltip( "#val# {$classification}" );
		$x->set_alpha(.8);

		
		$x_labels = new x_axis_labels();
		$x_labels->set_labels( $days );
		$x_labels->rotate(320);
		$x_labels->set_colour("#567f89");	
		
		$x_axis = new x_axis();
		$x_axis->set_labels( $x_labels ); 
		
		$y = new y_axis();
		$y->set_range( 0, $step+$max, $step );
		
		$title = new title($titlestr);
		$title->set_style("{font-size: 20px; font-weight: bold; color: #333333;}");
		
		$label = new x_legend("Total Record: {$total} | New: {$new} (".round(($new/$tt)*100,2)."%) | Old: {$old} (".round(($old/$tt)*100,2)."%)");
		$label->set_style('{font-family:tahoma;font-weight:bold;font-size:12px; color: #567f89}');

		$t = new tooltip();
		$t->set_shadow( false );
		$t->set_stroke( 3 );
		$t->set_colour( "#567f89" );
		$t->set_background_colour( "#e8e8e8" );
		$t->set_body_style( "{font-size: 10px; font-weight: bold; color: #000000;}" );	
		
		$chart = new open_flash_chart();
		$chart->set_title( $title );
		$chart->set_tooltip($t);
		$chart->add_element( $x );		
		$chart->set_bg_colour( '#e8e8e8' );
		$chart->set_x_axis( $x_axis );
		$chart->set_y_axis( $y );
		$chart->set_x_legend($label);
		
		echo $chart->toPrettyString();		
	}

//graph per program - date range base on program
	function perprogram()
	{
		$classification = $this->uri->segment(3);
		$program = $this->uri->segment(4);
		$s = $this->uri->segment(5);
		$e = $this->uri->segment(6);
		
		if($program != "all")
		{
			$pname = $this->mreports->getPrograms($program);
			$programname = $pname->title." ".$pname->batch;
		}
		else 
		{
			$programname = "";
		}
		
		if(!empty($s) && !empty($e))
		{
			$start = $s;
			$end = $e;
		}
		else if($program != "all")
		{
			$start = strtotime($pname->dateStart);
			$end = strtotime($pname->dateEnd);
		}
		else 
		{
			
		}
		
		$days = array();
		$color = array('Won'=>'#63be3f','Pending'=>'#3f84be','Loss'=>'#bda11d','Rejected'=>'#bd1d1d','IC'=>'#b418c7','IM'=>'#14413d','OC'=>'#27d073','OM'=>'#aed027');
		
		$c = round(($end-$start)/86400);
		$d = explode("-", date("Y-n-j",$start));
		for($i = 0;$i<=$c;$i++)
		{
			if(date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=6 && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=7)
			{
				$days[] = date("D, M j",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]));
				$date[] = date("F j, Y",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]));
			}
		}
		
		$titlestr = $programname." Statistics from ".date("F j, Y",$start)." to ".date("F j, Y",$end);
		$event = array('IM'=>'Incoming Mail','IC'=>'Incoming Call','OM'=>'Outgoing Mail','OC'=>'Outgoing Call');
		if(in_array($classification, array('IC','IM','OC','OM')))
		{
			$classification = $event[$classification];
		}
		
		
		$data = array();
		$total = 0;
		$new = 0;
		$old = 0;
		$i = 0;
		$max = 1;
		foreach ($date as $v)
		{
			$data[] = $this->mreports->monthly(strtotime($v),$classification,$program);
			$total += $data[$i];
			$max = ($max<$data[$i])?$data[$i]:$max;
			
			$new += $this->mreports->getSummary('old','0',"all",$program,strtotime($v),$classification);
			$old += $this->mreports->getSummary('old','1',"all",$program,strtotime($v),$classification);
			$i++;
		}
		$tt = $total==0?1:$total;//divisor.. change to 1 if value is zero
		$step = round($max / 15);

		$x = new bar_3d();
		//$x = new bar();
		//$x = new line();
		$x->colour("{$color[$this->uri->segment(3)]}");
		$x->set_on_show(new bar_on_show('grow-up', 2.5, 0));
		$x->set_values( $data );
		$x->set_tooltip( "#val# {$classification}" );
		$x->set_alpha(.8);
		//$x->set_width(1);

		
		$x_labels = new x_axis_labels();
		$x_labels->set_labels( $days );
		$x_labels->rotate(320);
		$x_labels->set_colour("#567f89");	
		
		$x_axis = new x_axis();
		$x_axis->set_labels( $x_labels ); 
		
		$y = new y_axis();
		$y->set_range( 0, $step+$max, $step );
		
		$title = new title($titlestr);
		$title->set_style("{font-size: 20px; font-weight: bold; color: #333333;}");
		
		$label = new x_legend("Total Record: {$total} | New: {$new} (".round(($new/$tt)*100,2)."%) | Old: {$old} (".round(($old/$tt)*100,2)."%)");
		$label->set_style('{font-family:tahoma;font-weight:bold;font-size:12px; color: #567f89}');

		$t = new tooltip();
		$t->set_shadow( false );
		$t->set_stroke( 3 );
		$t->set_colour( "#567f89" );
		$t->set_background_colour( "#e8e8e8" );
		$t->set_body_style( "{font-size: 10px; font-weight: bold; color: #000000;}" );	
		
		$chart = new open_flash_chart();
		$chart->set_title( $title );
		$chart->set_tooltip($t);
		$chart->add_element( $x );		
		$chart->set_bg_colour( '#e8e8e8' );
		$chart->set_x_axis( $x_axis );
		$chart->set_y_axis( $y );
		$chart->set_x_legend($label);
		
		echo $chart->toPrettyString();			
	}	
	
	*/
	
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
	
	
	function summary()
	{
		$data['uid'] = $this->my_session->userdata('uid');
		$data['uname'] = $this->my_session->userdata('uname');
		$data['title'] = "Summary";
		$data['content'] = "vSummary";
		$data['activeusers'] = $this->mreports->getUsers("",TRUE);
		$data['inactiveusers'] = $this->mreports->getUsers("",FALSE);

		$filters = unserialize($this->input->cookie('summary_filters'));

		if(!is_array($filters))
		{
			foreach ($this->items as $value)
			{
				$filters[$value] = "";				
			}			
		}
		if($filters['qsearchkey'] == '') $filters['qsearchkey'] = 'name';
		//$data['programsGraph'] = $this->getProgram($this->mreports->getPrograms());
		$total = $this->mreports->getResultSummary("","",$filters)->num_rows(); //count all the result without limit
		$config = array(
				'base_url'=>base_url().'index.php/reports/summary',
				'total_rows'=>$total,
				'per_page'=>'30',
				'full_tag_open' => '<div id="pagination">',
				'full_tag_close' => '</div>'
		);	
		$this->pagination->initialize($config);
		$start = $this->uri->segment(3);
		if(!empty($start) && $this->uri->segment(3)<$total):$start = $this->uri->segment(3);else:$start="0";endif;
		$results = $this->mreports->getResultSummary($config['per_page'],$start,$filters)->result_array();

		foreach($results as $v)
		{
			$detailfilter = $filters;
			$detailfilter['qsearchval'] = strip_quotes($v[$filters['qsearchkey']]);
			//echo "<pre>";print_r($detailfilter);
			$v['details'] = $this->mreports->getResultSummary("","",$detailfilter,FALSE)->result_array();
			$data['results'][] = $v;
		}
		
		$data['counter']=$start;
		$data['msg']=$total." record(s) found";
		$data['filters'] = $filters;
		
		//echo "<pre>";print_r($data['results']);
		$this->load->view('template',$data);		
	}
	
	function filterSummary()
	{
		$this->input->set_cookie('summary_filters',serialize($_POST),0);
	}
        
        
        function deleted()
        {
            $data['uid'] = $this->my_session->userdata('uid');
            $data['uname'] = $this->my_session->userdata('uname');
            $data['title'] = "Deleted Records";
            $data['content'] = 'vDeletedList';
            $data['results'] = $this->mreports->getUsersDeletedRecords();
            //echo '<pre>';print_r($data['results']);exit;
            $this->load->view('template',$data);
        }


}

?>