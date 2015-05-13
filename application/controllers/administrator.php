<?php 
class Administrator extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if(!checksession('logged_in'))
		{
			$this->my_session->set_userdata('returnUrl',current_url());
			redirect(site_url('user'));
		}
		if(!userPrivilege('program') AND !userPrivilege('programStatus'))
		{
			redirect(site_url());
		}
		$this->load->model('madmin');
		$this->load->library('form_validation');
	}
	
	function index()
	{
		$data['uname'] = my_session_value('uname');
		$data['title'] = "Programs";
		$data['content'] = "vProgram";
		$programs = $this->madmin->get_program_template()->result_array();
		$data['programs'] = array();
		foreach($programs as $program)
		{
			$program['batches'] = $this->madmin->get_program_by_template($program['id'])->result_array();
			array_push($data['programs'],$program);
		}
		$this->load->view('template',$data);	
	}
	
	function new_batch()
	{
		if(IS_AJAX)
		{
			$latest_batch = $this->madmin->get_latest_batch($this->input->post('program_id'));
			
			if($latest_batch->num_rows() > 0)
			{
				$latest_batch = $latest_batch->row_array();
			}
			else
			{
				$latest_batch = array(
					'programTempID' => $this->input->post('program_id'),
					'batch' => 0
				);
			}
			
			$return['status'] = "";
			
			//get the data from the schedules table
			$new_batch_data = $this->madmin->get_next_batch($latest_batch);
			if($new_batch_data->num_rows() > 0)
			{
				$return['message'] = $this->load->view('vProgramNextBatchEditor',$new_batch_data->row_array(),TRUE);
			}
			else 
			{
				$return['status'] = 'error';
				$return['message'] = 'Next batch schedule for this program is not yet available';
			}
			
			echo json_encode($return);
		}
		else show_404();
	}
	
	function edit_batch()
	{
		if(IS_AJAX)
		{
			$data = $this->madmin->get_program_batch($this->input->post('program_id'))->row_array();
			$this->load->view('vProgramNextBatchEditor',$data);
		}
		else show_404();
	}
	
	function save_program()
	{
		if(IS_AJAX)
		{
			$return['status'] = "";
			
			$this->form_validation->set_error_delimiters('','<br>');
			$this->form_validation->set_rules('batch','program batch','trim|required');
			$this->form_validation->set_rules('target','program target','trim|required|numeric');
			$this->form_validation->set_rules('dateStart','starting date','trim|required');
			$this->form_validation->set_rules('dateEnd','ending date','trim|required');
			
			if($this->form_validation->run() === TRUE)
			{
				$data = $_POST;				
				if(isset($_POST['id']))
				{
					$this->madmin->update_program_batch($this->input->post('id'),$data);
				}
				else 
				{
					$data['isActive'] = isset($_POST['isActive']) ? 1 : 0;
					$this->madmin->add_next_program_batch($data);
				}
			}
			else 
			{
				$return['status'] = 'error';
				$return['message'] = validation_errors();
			}
			
			echo json_encode($return);
		}
		else show_404();
	}
	
	function update_program_batch_status()
	{
		if(IS_AJAX)
		{
			$this->madmin->update_program_batch_status($this->input->post('program_id'),$this->input->post('status'));			
		}else redirect(site_url());		
	}
	
	
	function showstatus()
	{
		if(isset($_POST['ajax']))
		{
			$programid = $this->input->post('progid');
			$data['programinfo'] = $this->madmin->getSingleProgram($programid);
			$end = strtotime($data['programinfo']->dateEnd);
			$start = strtotime($data['programinfo']->dateStart);
			$totalweeks = array();
			$weekvalues = array();
			$c = round(($end-$start)/86400);
			$d = explode("-", date("Y-n-j",$start));
			for($i = 0;$i<=$c;$i++)
			{
				if(date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=6 && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=7)
				{
					$w = date("Y-W",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]));
					if(!in_array($w, $totalweeks))
					{
						$totalweeks[] = $w;
					}
				}
			}
			//print_r($totalweeks);
			$targetPerWeek = round($data['programinfo']->target/count($totalweeks),1);
			$accumTargetPerWeek = 0;
			$accumWonPerWeek = 0;
			$data['middle'] = round(count($totalweeks)/2);
			for($i = 0;$i<count($totalweeks);$i++)
			{
				$wonPerWeek = $this->madmin->getWonPerWeek($programid,$totalweeks[$i],$i,count($totalweeks));
				$weekvalues[] = array(
									'weekNo' => $totalweeks[$i],
									'tPerWeek'=>$targetPerWeek,
									'wonPerWeek'=>$wonPerWeek,
									'accumTargetPerWeek'=>round($accumTargetPerWeek+=$targetPerWeek),
									'accumWonPerWeek'=>$accumWonPerWeek+=$wonPerWeek,
									'accumPercent'=>round(($accumWonPerWeek/($accumTargetPerWeek!=0?round($accumTargetPerWeek):1))*100,2),
									'weeklyPercent'=>round(($wonPerWeek/($targetPerWeek!=0?$targetPerWeek:1))*100,2));
			}
			$data['weeks'] = $weekvalues;
			$data['title'] = $this->madmin->getSingleProgramTemp($data['programinfo']->programTempID)->title." ".$data['programinfo']->batch.' Status';
			$data['totals'] = array('target'=>$data['programinfo']->target,'totalwon'=>$accumWonPerWeek);		
			$this->load->view('programStatus',$data);
		}
	}	
	
	
	/*
	function __construct()
	{
		parent::__construct();
		if(!checksession('logged_in'))
		{
			$this->my_session->set_userdata('returnUrl',current_url());
			redirect(site_url('user'));
		}
		if(!$this->checkProgramPrivilege() && !$this->checkProgramStatus())
		{
			redirect(site_url());
			exit();			
		}
		unset($_SESSION['user']);
		unset($_SESSION['status']);//session sa dashboard
		unset($_SESSION['dateval']);//session sa dashboard
		$this->load->model('madmin');
		$this->load->library('form_validation');
		$this->load->helper('trails');
	}
	
	private $fields = array('programTempID'=>'program','batch'=>'batch','details'=>'details','dateStart'=>'start date',
							'dateEnd'=>'end date','target'=>'target','isActive'=>'status','title'=>'program title','pointReference'=>'point reference','logo'=>'logo');
	
	private function checkProgramPrivilege()
	{
		if(userPrivilege('program')==1)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}		
	}
	private function checkProgramStatus()
	{
		if(userPrivilege('programStatus')==1)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}		
	}
	
	function index()
	{
		//$data['uname'] = $_SESSION['uname'];
		//$data['title'] = "Administrator";
		//$data['content'] = "vadmin";	
		//$this->load->view('template',$data);	
		$this->programs();	
	}
	function programs()
	{
		$data['uname'] = my_session_value('uname');
		$data['title'] = "Programs";
		$data['content'] = "vprograms";	
		$total = $this->checkProgramPrivilege()?$this->db->count_all('tb_programs'):$this->db->where(array('isActive'=>1))->get('tb_programs')->num_rows();
		$config = array(
				'base_url'=>base_url().'index.php/administrator/programs',
				'per_page'=>'15',
				'total_rows'=>$total,
				'full_tag_open' => '<div id="pagination">',
				'full_tag_close' => '</div>',
		);
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		$start = $this->uri->segment(3);
		if(!empty($start) && $this->uri->segment(3)<$total):$start = $this->uri->segment(3);else:$start=0;endif;
		$data['programs'] = $this->madmin->getPrograms($config['per_page'],$start,$this->checkProgramPrivilege());
		$data['ptemplate'] = $this->madmin->getProgramTemp();
		$data['progPrivilege'] = $this->checkProgramPrivilege();
		$data['progStatus'] = $this->checkProgramStatus();
		$this->load->view('template',$data);			
	}
	
	function addprogram()
	{
		if(isset($_POST['ajax']) && $this->checkProgramPrivilege())
		{
			$data['ptemps'] = $this->db->get('tb_programtemplate')->result_array();
			$this->load->view('programEditor',$data);
		}
	}
	function editprogram()
	{
		if(isset($_POST['ajax']) && $this->checkProgramPrivilege())
		{
			$data['progID'] = $this->input->post('id');
			$data['ptemps'] = $this->db->get('tb_programtemplate')->result_array();
			$data['result'] = $this->madmin->getSingleProgram($data['progID']);
			$this->load->view('programEditor',$data);
		}
	}	
	
	function saveProgram()
	{
		if(isset($_POST['ajax']) && $this->checkProgramPrivilege())
		{			
			$this->form_validation->set_rules('batch','program batch','trim|required');
			$this->form_validation->set_rules('target','program target','trim|required|numeric');
			$this->form_validation->set_rules('dateStart','starting date','trim|required');
			$this->form_validation->set_rules('dateEnd','ending date','trim|required');
			
			if($this->form_validation->run()===TRUE)
			{
				$data = array(
					'programTempID' => $this->input->post('programTempID'),
					'batch' => $this->input->post('batch'),
					'target' => $this->input->post('target'),
					'details' => $this->input->post('details'),
					'isActive' => $this->input->post('isActive'),
					'dateStart' => date("Y-m-d",strtotime($this->input->post('dateStart'))),
					'dateEnd' => date("Y-m-d",strtotime($this->input->post('dateEnd'))),
					);
				if(strtotime($data['dateStart']) <= strtotime($data['dateEnd']))
				{
					$photostr = $this->input->post('logo');
					if(strpos($photostr, 'temp'))
					{
						$data['logo'] = $this->savephoto($data['title'], $photostr);
					}
											
					if(!empty($_POST['progID']))
					{					
						$program = $this->madmin->getSingleProgramTemp($data['programTempID']);
						$old = get_object_vars($this->madmin->getSingleProgram($_POST['progID']));
						$old['programTempID'] = $this->madmin->getSingleProgramTemp($old['programTempID'])->title;
						$new = $data;
						$new['programTempID'] = $this->madmin->getSingleProgramTemp($new['programTempID'])->title;							
						$changes = array();
						$str = "";
						foreach ($new as $key=>$value)
						{
							if(isset($old[$key]))
							{	
								if($new[$key]!=$old[$key] && $old[$key]!="")//get only the field with changes and old field is not blank
								{
									if($key == "isActive"):$value = $value == 1?"active":"inactive";$old['isActive'] = $old['isActive']== 1?"active":"inactive";endif;
									$str .= "<br><span style='margin-left:20px;'>Change ".$this->fields[$key]." from `".$old[$key]."` to `".$value."`";
								}
							}
						}
						$this->madmin->editProgram($data,$_POST['progID']);		
						if($str != ""):trails("Update program : ".$program->title." ".$data['batch'].$str);endif;
					}
					else
					{					
						$this->madmin->addProgram($data);
						$program = $this->madmin->getSingleProgramTemp($data['programTempID']);
						trails("Add program : ".$program->title." ".$data['batch']);
					}
				}else echo "The start date field cannot be higher than the end date";
			}
			else 
			{
				echo validation_errors();
			}
		}
	}
	
	//save photo
	private function savephoto($name,$photostr)
	{
		$ext = substr($photostr, strrpos($photostr, '.') + 1);
		$str = substr($photostr,strpos($photostr, 'assets/'),strlen($photostr)-strpos($photostr, 'assets/'));
		
		$photo = $name."-logo.".$ext;
		copy($photostr,'assets/photos/logo/'.$photo);
		unlink($str);
		return $photo;				
	}
	
	function addprogramtemplate()
	{
		if(isset($_POST['ajax']) && $this->checkProgramPrivilege())
		{
			$this->load->view('programTemplateEditor');
		}
	}
	function editprogramtemplate()
	{
		if(isset($_POST['ajax']) && $this->checkProgramPrivilege())
		{
			$data['progID'] = $this->input->post('refid');
			$data['result'] = $this->madmin->getSingleProgramTemp($data['progID']);
			$this->load->view('programTemplateEditor',$data);
		}
	}	
	
	function saveprogramtemplate()
	{
		if(isset($_POST['ajax']) && $this->checkProgramPrivilege())
		{
			$this->form_validation->set_rules('title','program title','trim|required');
			$this->form_validation->set_rules('pointReference','point reference','trim|required|numeric');
			
			if($this->form_validation->run()===TRUE)
			{
				$data = array(
					'title' => $this->input->post('title'),
					'pointReference' => $this->input->post('pointReference')
					);
				$photostr = $this->input->post('logo');
				if(strpos($photostr, 'temp'))
				{
					$data['logo'] = $this->savephoto($data['title'], $photostr);
				}
										
				if(!empty($_POST['progID']))
				{
					$program = $this->madmin->getSingleProgramTemp($_POST['progID']);
					$old = get_object_vars($this->madmin->getSingleProgramTemp($_POST['progID']));
					$new = $data;					
					$changes = array();
					$str = "";
					foreach ($new as $key=>$value)
					{
						if(isset($old[$key]))
						{	
							if($new[$key]!=$old[$key] && $old[$key]!="")//get only the field with changes and old field is not blank
							{
								$str .= "<br><span style='margin-left:20px;'>Change ".$this->fields[$key]." from `".$old[$key]."` to `".$value."`";
							}
						}
					}					
					$this->madmin->editProgramTemp($data,$_POST['progID']);
					if($str!=""):trails("Update program template : ".$program->title.$str);endif;
				}
				else
				{					
					$this->madmin->addProgramTemp($data);
					trails("Add program template : ".$data['title']);
				}				
			}
			else 
			{
				echo validation_errors();
			}
		}
	}
	
	function showstatus()
	{
		if(isset($_POST['ajax']))
		{
			$programid = $this->input->post('progid');
			$data['programinfo'] = $this->madmin->getSingleProgram($programid);
			$end = strtotime($data['programinfo']->dateEnd);
			$start = strtotime($data['programinfo']->dateStart);
			$totalweeks = array();
			$weekvalues = array();
			$c = round(($end-$start)/86400);
			$d = explode("-", date("Y-n-j",$start));
			for($i = 0;$i<=$c;$i++)
			{
				if(date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=6 && date("N",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]))!=7)
				{
					$w = date("Y-W",mktime(1,0,0,$d[1],$d[2]+$i,$d[0]));
					if(!in_array($w, $totalweeks))
					{
						$totalweeks[] = $w;
					}
				}
			}
			//print_r($totalweeks);
			$targetPerWeek = round($data['programinfo']->target/count($totalweeks),1);
			$accumTargetPerWeek = 0;
			$accumWonPerWeek = 0;
			$data['middle'] = round(count($totalweeks)/2);
			for($i = 0;$i<count($totalweeks);$i++)
			{
				$wonPerWeek = $this->madmin->getWonPerWeek($programid,$totalweeks[$i],$i,count($totalweeks));
				$weekvalues[] = array(
									'weekNo' => $totalweeks[$i],
									'tPerWeek'=>$targetPerWeek,
									'wonPerWeek'=>$wonPerWeek,
									'accumTargetPerWeek'=>round($accumTargetPerWeek+=$targetPerWeek),
									'accumWonPerWeek'=>$accumWonPerWeek+=$wonPerWeek,
									'accumPercent'=>round(($accumWonPerWeek/($accumTargetPerWeek!=0?round($accumTargetPerWeek):1))*100,2),
									'weeklyPercent'=>round(($wonPerWeek/($targetPerWeek!=0?$targetPerWeek:1))*100,2));
			}
			$data['weeks'] = $weekvalues;
			$data['title'] = $this->madmin->getSingleProgramTemp($data['programinfo']->programTempID)->title." ".$data['programinfo']->batch;
			$data['totals'] = array('target'=>$data['programinfo']->target,'totalwon'=>$accumWonPerWeek);		
			$this->load->view('programStatus',$data);
		}
	}
	*/
}

?>