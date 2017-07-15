<?php
class Schedule extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if (isset($_GET['setsched']) && stripos($_SERVER['REQUEST_URI'], 'create_schedule') !== false) {
			//bypass auth on creating schedule. //for localaccess when seting new active batch 
		} else {

			if(!checksession('logged_in'))
			{
				$this->my_session->set_userdata('returnUrl',current_url());
				redirect(site_url('user'));
			}
			
			if(!userPrivilege('isAdmin')){
				if( !userPrivilege('schedule') ) redirect(site_url());
			} 

		}
	}
	
	private $months = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
	private $days = array(1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday');
	//private $holidays = array('2011-12-25','2011-12-24','2012-12-24','2011-12-30','2011-12-31','2012-01-01','2012-12-25','2012-12-30','2012-12-29','2012-12-31','2012-02-25');
	
	
	function index()
	{
		$data['userid'] = $this->my_session->userdata('uid');
		$data['uname'] = $this->my_session->userdata('uname');
		$data['title'] = "Schedules";
		$data['content'] = "vSchedule";
		
		
		$this->create_schedule();
		
		//echo "<pre>";print_r($this->months);
		
		if(isset($_POST['year_selector'])){
			$selected_year = $_POST['year_selector'];
		}else{
			$selected_year = date('Y',strtotime(NOW));			
		}	
		$data['selected_year'] = $selected_year;	
		
		$months = array();
		foreach($this->months as $k=>$v)
		{
			$sql = "SELECT * 
					FROM tb_program_schedule sc 
					JOIN tb_programtemplate st ON sc.program_setting_id = st.id
					JOIN (
							SELECT schedule_id, MIN(session_date) AS session_date
							FROM tb_program_session
							GROUP BY schedule_id
						) AS ss ON sc.schedule_id = ss.schedule_id 
					WHERE st.status = 1
					AND DATE_FORMAT(ss.session_date,'%Y-%c') = '".$selected_year.'-'.$k."'";
			$sql .= " GROUP BY sc.schedule_id ";
			
			$month['month'] = $v.'-'.date('y',strtotime($selected_year.'-01-01'));
			$programs = $this->db->query($sql)->result_array();
			
			$schedule = array();
			foreach($programs as $program)
			{
				$str = '';			
				
				$sessions = $this->get_program_session_with_limit($program['schedule_id'])->result_array();
				
				foreach($sessions as $session)
				{ 
					$date = date("F j",strtotime($session['session_date']));
					$temp_str_arr = explode(' ', $date); 				
					
					$cssClass = "";
					$date_id = "";
					
					if($session['speaker_counter'] > 1 AND $session['counter'] > $session['limit'])
					{
						$cssClass =  "both_alert ";					
						$date_id = $session['session_date'];
					}
					elseif($session['counter'] > $session['limit'])
					{
						$cssClass =  "venue_alert ";					
						$date_id = $session['session_date'];
					}
					elseif($session['speaker_counter'] > 1)
					{
						$cssClass =  "speaker_alert ";					
						$date_id = $session['session_date'];
					}
					
					
					$link = '<a class="sessionDetails '.$cssClass.'" id="'.$session['session_id'].'" date_id ="'.$date_id.'">';
					$str .= strpos($str, $temp_str_arr[0]) === FALSE ? $link.$date.'</a>, ' : $link.$temp_str_arr[1].'</a>, ';
				}
				
				$program['sessions'] = substr($str, 0, -2);
				array_push($schedule,$program);
			}
			
			$month['programs'] = $schedule;
			
			array_push($months,$month);
		}
		
		$previous_type_schedule = $this->get_old_type_program_schedules($selected_year);
		
		$months = $this->merge_schedules($months, $previous_type_schedule,'programs');
		
		$chunk = array_chunk($months,ceil(count($months)/2),TRUE);
		$data['right'] = isset($chunk[0])?$chunk[0]:array();
		$data['left'] = isset($chunk[1])?$chunk[1]:array();
		$this->load->view('template',$data);
	}
	
	public function create_schedule()
	{
		
		$programs = $this->db->where('status', 1)->get('tb_programtemplate')->result_array();
		//echo "<pre>";print_r($programs);
		foreach($programs as $program)
		{
			
			if($program['next_date'] != '0000-00-00')
			{	

				$current_batch 	= $this->get_last_current_batch($program['id']);
				$latest_batch 	= $this->get_last_generated_batch($program['id']);
				// var_dump($latest_batch);
				// var_dump($latest_batch < ($current_batch + 1));
				// echo $program['id'] . ' - ' . $current_batch . ' - ' . $latest_batch . '<br>'; //continue;
				while ($latest_batch < ($current_batch + 2))
				{

					//ang start date(marketing start) ay ung first session ng previous batch, kapag hindi available, mag backwards ng compute ng time span
					$start_date = $this->get_start_date($program['id']);
					if( $start_date == FALSE )
					{
						$start_date = date('Y-m-d',strtotime($program['next_date']." -".$program['time_span']." week"));						
					}
					
					$end_date = $program['next_date'];
	
					if(date('N',strtotime($end_date)) >= $program['prefer_day'])
					{
						$end_date = date('Y-m-d',strtotime($end_date." +1 week"));
					}
					
					$end_date = date("Y-m-d",strtotime(date('Y',strtotime($end_date)) . 'W' . date('W',strtotime($end_date)) . $program['prefer_day']));
					
					$sessions = array();
					$target = $program['run_session'];
					for($i = 0;$i < $target;$i++)
					{
						$session = '';					
						$day = strtotime($end_date);
						$first_session = date("Y-m-d",strtotime(date('Y',$day) . 'W' . date('W',$day) . $program['prefer_day']));
						$session = date('Y-m-d',strtotime($first_session." +".($i * $program['session_interval'])."day"));
						
						if(is_holiday($session))
						{
							$target += 1;
						}
						else 
						{
							$sessions[] = $session;
						}
						
					}						
					
					$end_date = $sessions[0]; // ang magiging end date ay ung first day ng session
					
					$data = array(
						'program_setting_id' => $program['id'],
						'batch' => $latest_batch + 1,
						'start_date' => $start_date,
						'expected_end_date' => $end_date,
						'end_date' => $end_date
						
					);
					
					$this->db->set($data)->insert('tb_program_schedule');
					$schedule_id = $this->db->insert_id();

					foreach($sessions as $session)
					{
						//$this->db->set(array('schedule_id'=>$schedule_id,'session_date'=>$session))->insert('tb_program_session');
						$this->db->set('session_date',$session)
								->set('schedule_id',$schedule_id)
								->set('session_speaker',$program['default_speaker'])
								->set('session_venue',$program['default_venue'])
								->insert('tb_program_session');
					}
					
					$batch = $data['batch'];
					//$next_end_date = date('Y-m-d',strtotime($program['next_date']." +".$program['time_span']." week"));
					$next_end_date = $this->generate_new_end_date($program['id'],$program['next_date']);					
					if($this->update_program_settings($program['id'], array('next_batch'=>$batch,'next_date'=>$next_end_date)))
					{
						$program['next_batch'] = $batch;
						$program['next_date'] = $next_end_date;
					}

					$latest_batch 	= $this->get_last_generated_batch($program['id']);

				}
			}	
		
		}
	}	
	
	private function update_program_settings($id,$data)
	{
		$this->db->where('id',$id)->set($data)->update('tb_programtemplate');
		if($this->db->affected_rows()) 
		return TRUE;
	}
	
	private function get_start_date($id)
	{
		//$id = $this->uri->segment(3);
		$schedule = $this->db->where('program_setting_id',$id)->order_by('schedule_id','DESC')->limit(1)->get('tb_program_schedule');
		if($schedule->num_rows() > 0)
		{
			//echo '<pre>';print_r( $schedule->row_array());
			return $schedule->row()->end_date;
		}
		else return FALSE;
	}
	
	private function generate_new_end_date($id,$date_from)
	{
		$setting = $this->get_programs_setting($id);
		
		return date('Y-m-d',strtotime($date_from." +".$setting->time_span." week"));
	}


	private function get_last_current_batch($program_id) {
		//max batch + 0 because batch field datatype is varchar on tb_programs
		$sql = "SELECT programTempID,MAX(batch+0) AS batch
				FROM tb_programs o
				WHERE programTempID = {$program_id}
				LIMIT 1";

		$q = $this->db->query($sql);
		return (int) $q->row()->batch;

	}

	private function get_last_generated_batch($program_id) {
		$sql = "SELECT program_setting_id, MAX(batch) AS batch
				FROM tb_program_schedule
				WHERE program_setting_id = {$program_id}";

		$q = $this->db->query($sql);
		return (int) $q->row()->batch;
	}
	
	
	private function update_program_schedules($schedule_id,$data = array())
	{
		
		$schedule_data = $this->get_schedule_data($schedule_id);		
		if($schedule_data)
		{
			//una, kailangan i update ung current running program
			if(isset($data['end_date']))
			{
				if($this->update_schedule($schedule_id,array('end_date'=>$data['end_date'])))
				{
					//tapos kailangan ko makuha ung mga schedule na maaaddjust, ung mga schedule na susunod sa current
					$updated_schedule_data = $this->get_schedule_data($schedule_id);
					$next_start_date = $updated_schedule_data->end_date;	
					$schedules = $this->get_changable_schedules($schedule_data->program_setting_id,$updated_schedule_data->batch)->result_array();
					//print_r($schedules);
					foreach($schedules as $schedule)
					{
						$program_setting = $this->get_programs_setting($schedule['program_setting_id']);
						if($program_setting)
						{
							//$next_end_date = date('Y-m-d',strtotime($next_start_date." +".$program_setting->time_span." week"));
							$next_end_date = $this->generate_new_end_date($program_setting->id, $next_start_date);
							
								$sched_data = array(
									'start_date' => $next_start_date,
									'expected_end_date' => $next_end_date,
									'end_date' => $next_end_date						
								);
							
							$this->update_schedule($schedule['schedule_id'],$sched_data);
							$next_start_date = $next_end_date;
							
						}
					}
					
					//after the batch loop, update the next_date in the program_setting table
					$this->update_program_settings($schedule_data->program_setting_id, array('next_date'=>$this->generate_new_end_date($schedule_data->program_setting_id, $next_start_date)));
				}
			}
		}
		//echo "<pre>";print_r($schedules);
	}
	
	private function update_schedule($schedule_id,$data)
	{
		//update the sessions
		$schedule_data = $this->get_schedule_data($schedule_id);		
		$program_setting = $this->get_programs_setting($schedule_data->program_setting_id);

		if($program_setting)
		{
			$old_sessions_raw = $this->get_program_sessions($schedule_id)->result_array();
			$old_sessions = array();
			foreach($old_sessions_raw as $old)
			{
				$old_sessions[] = $old['session_date'];
			}
			
			$new_sessions = $this->generate_sessions($data['end_date'], $program_setting);
			
			//alisin ung mga old dates na wala sa bagong sessions date
			foreach ($old_sessions as $session)
			{
				if( ! in_array($session, $new_sessions))
				{
					$this->db->where('session_date',$session)->where('schedule_id',$schedule_id)->delete('tb_program_session');
				}
			}
			
			//i-add ung mga bagong date na wala sa old session date
			foreach ($new_sessions as $session)
			{
				if( ! in_array($session, $old_sessions))
				{
					$this->db->set('session_date',$session)
								->set('schedule_id',$schedule_id)
								->set('session_speaker',$program_setting->default_speaker)
								->set('session_venue',$program_setting->default_venue)
								->insert('tb_program_session');
				}
			}
			
			//print_r($old_sessions);
			//print_r($new_sessions);
		}
		
		$data['end_date'] = $new_sessions[0];
		
		// iupdate din ung schedule sa tb_programs
		$this->update_tb_program_schedules($schedule_id,$data);
		
		$this->db->where('schedule_id',$schedule_id)->set($data)->update('tb_program_schedule');
		
		//if($this->db->affected_rows()) 
		return TRUE;
	}
	
	// ung schedule sa tb_programs
	private function update_tb_program_schedules($schedule_id,$data)
	{
		$new_data = array();
		if(isset($data['start_date'])) $new_data['dateStart'] = $data['start_date'];
		if(isset($data['end_date'])) $new_data['dateEnd'] = $data['end_date'];
		
		$this->db->where('schedule_id',$schedule_id)->set($new_data)->update('tb_programs');
	}
	
	private function generate_sessions($session_start,$program_settings)
	{
		$sessions = array();
		$target = $program_settings->run_session;
		for($i = 0;$i < $target;$i++)
		{
			$session = '';					
			$day = strtotime($session_start);
			$first_session = date("Y-m-d",strtotime(date('Y',$day) . 'W' . date('W',$day) . $program_settings->prefer_day));
			
			if(strtotime($first_session) < $day){
				$first_session = date('Y-m-d',strtotime($first_session." +1 week"));
			} 
			
			$session = date('Y-m-d',strtotime($first_session." +".($i * $program_settings->session_interval)."day"));
			
			if(is_holiday($session))
			{
				$target += 1;
			}
			else 
			{
				$sessions[] = $session;
			}			
		}

		return $sessions;
	}
	
	function session_details()
	{
		if(IS_AJAX)
		{
			$session_data = $this->get_sessions($this->input->post('session_id'))->row();
			$schedule_data = $this->get_schedule_data($session_data->schedule_id);
			$program_data = $this->get_programs_setting($schedule_data->program_setting_id);
			$this->load->view('vProgramSessionDetails',array('program'=>$program_data,'schedule'=>$schedule_data,'session'=>$session_data));
		}
		else show_404();
	}
	
	function session_editor()
	{
		if(IS_AJAX)
		{
			$data = $this->get_sessions($this->input->post('session_id'))->row_array();
			$data['speakers'] = $this->get_speakers()->result_array();
			$data['venues'] = $this->get_venues()->result_array();
			$this->load->view('vProgramSessionEditor',$data);
		}
		else show_404();
	}
	
	function save_program_session()
	{
		if(IS_AJAX)
		{
			
			$this->load->library('form_validation','','validate');
			$this->validate->set_error_delimiters('','<br>');
			
			$return['status'] = "";
			$this->validate->set_rules('session_date','session date','trim|required|callback_session_date_exist');
			$this->validate->set_rules('session_speaker','session speaker','trim|required');
			$this->validate->set_rules('session_venue','session venue','trim|required');
			
			if($this->validate->run()===TRUE)
			{
				$this->db->where('session_id',$this->input->post('session_id'))->set($_POST)->update('tb_program_session');
				
				//dapat update ko din ung end_date nung schedule..baka nabago ung start date nung unang session.
				//kailangan ko kunin ung pinakaunang date nung mga sessions, tas un ang magiging bagong end_date
				$first_session = $this->db->where('schedule_id',$this->input->post('schedule_id'))->order_by('session_date')->limit(1)->get('tb_program_session')->row();
				
				//eto kahit anu piliin nyang date dun mismo mapupunta
				$this->db->where('schedule_id',$this->input->post('schedule_id'))->set('end_date',$first_session->session_date)->update('tb_program_schedule');
				
				//ito ay para maupdate din ung schedule sa tb_programs
				$this->update_tb_program_schedules($this->input->post('schedule_id'), array('end_date'=>$first_session->session_date));
				
				//eto naman, u uuppdate nya lahat nung mga schedule date nung program, pati ung ibang session date nung schedule..skip ung mga holidays
				//$this->update_program_schedules($this->input->post('schedule_id'),array('end_date'=>$first_session->session_date));
				
			}
			else 
			{
				$return['status'] = "error";
				$return['message'] = validation_errors();
			}
			
			echo json_encode($return);			
		}
		else show_404();
	}
	
	function session_date_exist($value)
	{
		if($this->check_session_date_if_exist($value,$this->input->post('session_id'),$this->input->post('schedule_id')))
		{
			$this->validate->set_message('session_date_exist', 'The program session date is already exists');
			return FALSE;
		}
		return TRUE;		
	}
	
	private function check_session_date_if_exist($date,$session_id,$schedule_id)
	{
		$sql = "SELECT * FROM tb_program_session
					WHERE session_date = '{$date}'
					AND session_date IN(SELECT session_date FROM tb_program_session WHERE session_id != '{$session_id}' AND schedule_id = '{$schedule_id}')";
		$q = $this->db->query($sql);
		return $q->num_rows()>0?TRUE:FALSE;		
	}
	
	function programs_list()
	{
		if(IS_AJAX)
		{
			$data['programs'] = $this->get_programs_setting('', TRUE)->result_array();
			$this->load->view('vProgramList',$data);
		}
		else show_404();
	}
	
	function program_details()
	{
		if(IS_AJAX)
		{
			$data['program'] = $this->get_programs_setting($this->input->post('id'));
			//echo '<pre>';print_r($data);
			$this->load->view('vProgramDetails',$data);
		}
		else show_404();
	}

	
	function add_program()
	{
		if(IS_AJAX)
		{
			$data['days'] = $this->days;
			$data['editorTitle'] = 'Add Program';
			$data['speakers'] = $this->get_speakers()->result_array();
			$data['venues'] = $this->get_venues()->result_array();
			$this->load->view('vNewProgramEditor',$data);
		}
		else show_404();
	}
	
	function program_editor()
	{
		if(IS_AJAX)
		{
			$data = $this->db->get_where('tb_programtemplate',array('id'=>$this->input->post('id')))->row_array();
			
			if($data['next_date'] == '0000-00-00')
			{
				$sql = "SELECT batch 
						FROM tb_programs
						WHERE programTempID = '{$this->input->post('id')}'
						ORDER BY batch + 0 DESC
						LIMIT 1";
				$q = $this->db->query($sql);
				if($q->num_rows() > 0 )
				{
					$data['next_batch'] = $q->row()->batch + 1;
				}
			}
			
			$data['days'] = $this->days;
			$data['editorTitle'] = !empty($data['name']) ? $data['name'] : $data['title'];
			$data['speakers'] = $this->get_speakers()->result_array();
			$data['venues'] = $this->get_venues()->result_array();
			$this->load->view('vNewProgramEditor',$data);
		}
		else show_404();
	}
	
	function save_program()
	{
		if(IS_AJAX)
		{
			
			$this->load->library('form_validation','','validate');
			$this->validate->set_error_delimiters('','<br>');
			
			$return['status'] = "";
			$this->validate->set_rules('name','program name','trim|ucwords|required');
			$this->validate->set_rules('title','acronym','trim|strtoupper|required');
			$this->validate->set_rules('pointReference','point reference','trim|numeric|required|greater_than[0]');
			$this->validate->set_rules('time_span','marketing span','trim|numeric|required|greater_than[0]');
			$this->validate->set_rules('yearly_run','run for year','trim|numeric|required|greater_than[0]');
			$this->validate->set_rules('run_session','program sessions','trim|numeric|required|greater_than[0]');
			$this->validate->set_rules('session_interval','sessions interval','trim|numeric|required|greater_than[0]');			
			
			if( isset($_POST['next_batch']))
			{
				$this->validate->set_rules('next_batch','next batch','trim|numeric|required|greater_than[0]');
				$this->validate->set_rules('next_date','session start','trim|required');
			}					                        
                        
			$this->validate->set_rules('default_speaker','default speaker','trim|required');
			$this->validate->set_rules('default_venue','default venue','trim|required');
                        
	
			$logo = $this->input->post('logo');
			if(strpos($logo, 'temp'))
			{
				$_POST['logo'] = savephoto($_POST['title'], $logo);
			}
			else unset($_POST['logo']);
			
			$_POST['until_date'] = '0000-00-00';
			
			if(!isset($_POST['id'])) {

				$_POST['date_added'] = date('Y-m-d');
				$_POST['generate_type']	= 'auto';

			}

			if($this->validate->run()===TRUE)
			{

				if(isset($_POST['id']))
				{
					$this->update_program_settings($_POST['id'], $_POST);
					
					//check ko muna kung meron ng schedule ng ganitong program
					if($this->get_program_schedules($_POST['id'])->num_rows() > 0)
					{
						// i-update ung schedules, simula sa current na nag ra-run.
						//kaya kailangan ko muna kunin ung schedule_id nung previous batch na nag rrun na ganung program
						$current_schedule = $this->get_current_program_running($_POST['id']);		

						/*
						
						//kunin ko ung mga susunod na program schedule
						$schedules = $this->get_changable_schedules($current_schedule->program_setting_id,$current_schedule->batch)->result_array();
						//i update ung kasunod, after nyan ay kukunin nung update_program_schedule ung mga susunod pa sa susunod
						$this->update_program_schedules($schedules[0]['schedule_id'],array('end_date'=>$this->generate_new_end_date($current_schedule->program_setting_id, $current_schedule->end_date)));						
						*/
						
						//kasi dapat kasamang maupdate ung current
						if($current_schedule)
						{
							$this->update_program_schedules($current_schedule->schedule_id,array('end_date'=>$current_schedule->end_date));	
						}
					}
					
				}
				else
				{
					$this->db->set($_POST)->insert('tb_programtemplate');
				}

				$this->create_schedule();
			}
			else 
			{
				$return['status'] = "error";
				$return['message'] = validation_errors();
			}
			
			echo json_encode($return);			
		}
		else show_404();
	}
        
        function program_disable()
	{
		//if(IS_AJAX)
		{
                    if(isset($_SERVER['HTTP_REFERER']) AND strpos($_SERVER['HTTP_REFERER'],'schedule/settings')){
                        $this->db->where('id',$this->uri->segment(3))->set('status',0)->update('tb_programtemplate');
                    }                    
                    redirect(site_url('schedule/settings'));
                }
        }
        function program_enable()
	{
		//if(IS_AJAX)
		{
                    if(isset($_SERVER['HTTP_REFERER']) AND strpos($_SERVER['HTTP_REFERER'],'schedule/settings')){
                        $this->db->where('id',$this->uri->segment(3))->set('status',1)->update('tb_programtemplate');
                        $this->create_schedule();
                    }                    
                    redirect(site_url('schedule/settings'));
                }
        }
	
	
	function schedule_details()
	{
		if(IS_AJAX)
		{
			$data = $this->db->where('schedule_id',$this->input->post('schedule_id'))->get('tb_program_schedule')->row_array();
			
			//$sessions = $this->db->where('schedule_id',$this->input->post('schedule_id'))->order_by('session_date')->get('tb_program_session');
			$sessions = $this->get_program_session_with_limit($this->input->post('schedule_id'));		
			$chunk = array_chunk($sessions->result_array(), ceil($sessions->num_rows()/2));
			$data['left'] = isset($chunk[0]) ? $chunk[0] : array();
			$data['right'] = isset($chunk[1]) ? $chunk[1] : array();
			
			
			$return['title'] = getProgramDetails($data['program_setting_id'])->name .' '.$data['batch'];
			$return['content'] = $this->load->view('vProgramScheduleDetails',$data,TRUE);
			
			echo json_encode($return);
		}
		else show_404();
	}
	
	function schedule_editor()
	{
		if(IS_AJAX)
		{
			$data = $this->db->where('schedule_id',$this->input->post('schedule_id'))->get('tb_program_schedule')->row_array();
			$this->load->view('vProgramScheduleEditor',$data);
		}
		else show_404();
	}
	
	// custom validation rules, check if transaction date is greater than the current date
	function valid_date($str = '')
	{
		if(!empty($str) && strtotime($str) < strtotime(date("Y-m-d",strtotime(NOW))))
		{
			$this->validate->set_message('valid_date','Program start date must be the same or higher than the current date');
			return FALSE;
		}
		return TRUE;
	}

	private function check_yearly_run($schedule_id,$date_from)
	{
		$schedule = $this->get_schedule_data($schedule_id);
		
		$sql = "SELECT *,DATE_FORMAT(end_date,'%Y') as year
				FROM tb_program_schedule 
				WHERE program_setting_id = '{$schedule->program_setting_id}'
				AND DATE_FORMAT(end_date,'%Y') = '".date('Y',strtotime($schedule->end_date))."'
				AND UNIX_TIMESTAMP(end_date) < '".strtotime($schedule->end_date)."'";
		
		$old_schedules_raw = $this->db->query($sql)->result_array();
		
		$old_schedules = array();
		foreach ($old_schedules_raw as $v)
		{
			$old_schedules[] = $v['end_date'];
		}
		
		$new_schedules = array();
		
		while(TRUE)
		{
			if(strtotime($date_from) <= strtotime(date('Y',strtotime($schedule->end_date)).'-12-31'))
			{
				$new_schedules[] = $date_from;
				$date_from = $this->generate_new_end_date($schedule->program_setting_id, $date_from);
			}
			else break;
		}
		
		$schedules = array_merge($old_schedules,$new_schedules);
		$program_setting = $this->get_programs_setting($schedule->program_setting_id);
		
		// kapag abot ung target run per year
		return count($schedules) >= $program_setting->yearly_run ? TRUE : FALSE;
	}
	
	function save_program_schedule()
	{
		if(IS_AJAX)
		{
			
			$this->load->library('form_validation','','validate');
			$this->validate->set_error_delimiters('','<br>');
			
			$return['status'] = "";
			$this->validate->set_rules('end_date','program start date','trim|required|callback_valid_date');
	
			if($this->validate->run()===TRUE)
			{
				
				if(isset($_POST['update_all']))
				{
					/// kapag pasok pa din sa target tuloy tuloy lang, if not, may option kung itutuloy o hindi
					if($this->check_yearly_run($this->input->post('schedule_id'), $this->input->post('end_date')))
					{
						unset($_POST['update_all']);
						
						//update all schedules of the next batch of the program
						$this->update_program_schedules($this->input->post('schedule_id'),$_POST);
					}
					else 
					{
						$return['status'] = "pending";
						$return['message'] = "You can no longer reach the target run per year with the new setting.<br>Do you want to continue?<br>";
						
						//pag nag continue sa $this->confirm_schedule_changes()
					}
				}
				else 
				{
					//single update
					$this->update_schedule($this->input->post('schedule_id'),$_POST);
				}
			}
			else 
			{
				$return['status'] = "error";
				$return['message'] = validation_errors();
			}
			
			echo json_encode($return);			
		}
		else show_404();		
	}
	
	function confirm_schedule_changes()
	{
		if(IS_AJAX)
		{
			$this->update_program_schedules($this->input->post('schedule_id'),$_POST);
		}
		else show_404();
	}
	
	function select_months()
	{
		if(IS_AJAX)
		{
			$chunk = array_chunk($this->months,ceil(count($this->months)/2),TRUE);
			$data['right'] = isset($chunk[0])?$chunk[0]:array();
			$data['left'] = isset($chunk[1])?$chunk[1]:array();
			$data['checked'] = $this->months;
			$data['selected_year'] = $this->input->post('selected_year');
			//echo "<pre>";print_r($data);
			$this->load->view('vPDFMonthSelector',$data);
		}
		else show_404();
	}
	
	function generate_pdf()
	{
		if(isset($_POST) AND (userPrivilege('isAdmin') OR userPrivilege('download_schedule_pdf')))
		{
			$this->load->library('mpdf');
			$pdfTitle = $_POST['selected_year'].' Program Schedules';
			$header = '<table width="100%" style="background: none;margin-left:-1px;border-bottom: 1px solid #aaa; vertical-align: bottom; font-family:
						serif; font-size: 7pt; color: #000088;"><tr>
						<td width="50%" align="left"><h1 style="color:#333">'.$pdfTitle.'</h1></td>
						<td width="50%" style="text-align: right;"><img src="assets/images/solidground-logo.png" width="150px" height="40px"/></td>
						</tr></table>';
			$footer = '<table width="100%" style="background: none;margin-left:-1px;border-top: 1px solid #aaa; vertical-align: bottom; font-family:
						serif; font-size: 8pt; color: #777;"><tr>
						<td align="left"><span style="font-size:8pt;">{PAGENO}</span></td>
						<td width="33%" align="right">Generated by '.getUserData(my_session_value('uid'))->name.'<br><span class="fadeTextSmall">{DATE F j, Y  g:i a}</span></td>
						</tr></table>';
			$this->mpdf->mpdf('c','A4','','',10,10,20,18,5,5);
			$this->mpdf->use_kwt = true;
			$this->mpdf->SetTitle($pdfTitle);
			$this->mpdf->SetHTMLHeader($header);
			$this->mpdf->SetHTMLFooter($footer);
			$this->mpdf->shrink_tables_to_fit=1;
			$this->mpdf->WriteHTML($this->get_pdf_content($_POST));
			$this->mpdf->Output($pdfTitle.".pdf",'D');
		}
		else show_404();		
	}
	
	private function get_pdf_content($data)
	{
		$months = array();
		foreach($this->months as $k=>$v)
		{
			if(in_array($k, $data['months']))
			{
				$sql = "SELECT * 
						FROM tb_program_schedule sc 
						JOIN tb_programtemplate st ON sc.program_setting_id = st.id
						JOIN (
								SELECT schedule_id, MIN(session_date) AS session_date
								FROM tb_program_session
								GROUP BY schedule_id
							) AS ss ON sc.schedule_id = ss.schedule_id 
						WHERE DATE_FORMAT(ss.session_date,'%Y-%c') = '".$data['selected_year'].'-'.$k."'";
				$sql .= " GROUP BY sc.schedule_id ";
				
				$month['month'] = $v.' '.date('Y',strtotime($data['selected_year'].'-01-01'));
				$programs = $this->db->query($sql)->result_array();
				
				$schedule = array();
				foreach($programs as $program)
				{
					$str = '';
					$sessions = $this->get_program_session_with_limit($program['schedule_id'])->result_array();
					foreach($sessions as $session)
					{
						$date = date("M j",strtotime($session['session_date']));
						$temp_str_arr = explode(' ', $date); 
	
						$cssClass = "";
						
						if($session['speaker_counter'] > 1 AND $session['counter'] > $session['limit'])
						{
							$cssClass =  "both_alert ";				
						}
						elseif($session['counter'] > $session['limit'])
						{
							$cssClass =  "venue_alert ";			
						}
						elseif($session['speaker_counter'] > 1)
						{
							$cssClass =  "speaker_alert ";			
						}
					
						$link = '<span class="'.$cssClass.'" id="'.$session['session_id'].'">';
						$str .= strpos($str, $temp_str_arr[0]) === FALSE ? $link.$date.'</span>, ' : $link.$temp_str_arr[1].'</span>, ';
					}
					
					$program['sessions'] = substr($str, 0, -2);
					array_push($schedule,$program);
				}
				
				$month['programs'] = $schedule;
				
				array_push($months,$month);
			}
		}
				
        $previous_type_schedule = $this->get_old_type_program_schedules($data['selected_year'],$data['months']);
		
		$months = $this->merge_schedules($months, $previous_type_schedule,'programs');
        
        $data['months'] = $months;
		
		if(count($months) > 4)
		{
			$chunk = array_chunk($months,ceil(count($months)/2),TRUE);
			$data['right'] = isset($chunk[0])?$chunk[0]:array();
			$data['left'] = isset($chunk[1])?$chunk[1]:array();			
		}
		
		return $this->load->view('vProgramSchedulePDF',$data,TRUE);
	}
	
	/*
	 * DATA GETHERING
	 */
	
	private function get_programs_setting($program_setting_id,$get_all = FALSE)
	{
		if($get_all)
		{
			return $this->db->where('status', 1)->order_by('name,title')->get('tb_programtemplate');
		}
		else 
		{
			$q = $this->db->where('id',$program_setting_id)->get('tb_programtemplate');
			
			return $q->num_rows() > 0 ? $q->row() : FALSE;	
		}	
	}
	
	
	private function get_schedule_data($schedule_id)
	{
		$q = $this->db->where('schedule_id',$schedule_id)->get('tb_program_schedule');
		
		return $q->num_rows() > 0 ? $q->row() : FALSE;		
	}
	
	private function get_changable_schedules($program_setting_id,$batch)
	{
		$sql = "SELECT * 
				FROM tb_program_schedule 
				WHERE program_setting_id = '{$program_setting_id}'
				AND batch > '{$batch}'
				ORDER BY schedule_id";
				//AND UNIX_TIMESTAMP(end_date) > UNIX_TIMESTAMP('".$end_date."')	
		return $this->db->query($sql);
	}
	
	private function get_program_sessions($schedule_id)
	{
		return $this->db->where('schedule_id',$schedule_id)->get('tb_program_session');
	}
	
	private function get_program_session_with_limit($schedule_id)
	{
		$sql = "SELECT session_id, schedule_id, session_date, session_venue, session_speaker, 
					(
						SELECT COUNT( * ) AS dayTotal
						FROM tb_program_session ses1
						JOIN tb_program_venue ven1 ON ven1.venue_id = ses1.session_venue
						WHERE ses1.session_date = ses.session_date
						AND ses1.session_venue = ses.session_venue
						GROUP BY session_date, session_venue
					) AS counter, ven.limit, venue_name, 
					(
						SELECT COUNT( * ) AS tempDayTotal
						FROM tb_program_session ses1
						JOIN tb_program_speaker sp1 ON sp1.speaker_id = ses1.session_speaker
						WHERE ses1.session_date = ses.session_date
						AND ses1.session_speaker = ses.session_speaker
						GROUP BY session_date, session_speaker
					) AS speaker_counter, CONCAT( firstname, ' ', lastname ) AS speaker_name
				FROM tb_program_session ses
				JOIN tb_program_venue ven ON ven.venue_id = ses.session_venue
				JOIN tb_program_speaker sp ON sp.speaker_id = ses.session_speaker
				WHERE schedule_id = '{$schedule_id}'
				ORDER BY session_date";
		
		return $this->db->query($sql);		
	}
	
	private function get_current_program_running($id)
	{
		//$id = $this->uri->segment(3);
		$current_timestamp = strtotime(NOW);
		$sql = "SELECT * FROM tb_program_schedule sc
				WHERE program_setting_id = '{$id}'
				AND UNIX_TIMESTAMP(start_date) <= {$current_timestamp}
				AND UNIX_TIMESTAMP(end_date) >= {$current_timestamp}
				ORDER BY schedule_id DESC
				LIMIT 1";
		
		$q = $this->db->query($sql);
		if($q->num_rows() > 0)
		{
			return $q->row();
		}
		else
		{
			$sql = "SELECT * FROM tb_program_schedule sc
				WHERE program_setting_id = '{$id}'
				ORDER BY schedule_id
				LIMIT 1";
			
			$q = $this->db->query($sql);
			if($q->num_rows() > 0)
			{
				if(strtotime($q->row()->start_date) > $current_timestamp)
				{
					return $q->row();
				}
			}
		}
		return FALSE;
		//echo "<pre>";print_r($q->result_array());
	}
	
	private function get_program_schedules($id)
	{
		return $this->db->where('program_setting_id',$id)->get('tb_program_schedule');
	}
	
	private function get_sessions($session_id = NULL)
	{
		$sql = "SELECT * FROM tb_program_session ";
		
		if( !is_null($session_id))
		{
			$sql .=" WHERE session_id = '{$session_id}' ";
		}
		
		return $this->db->query($sql);
	}	
	
	private function get_speakers($speaker_id = NULL)
	{
		$sql = "SELECT * FROM tb_program_speaker ";
		
		if( !is_null($speaker_id))
		{
			$sql .=" WHERE speaker_id = '{$speaker_id}' ";
		}
		else 
		{
			$sql .=" WHERE isActive = 1 ";
		}
		
		return $this->db->query($sql);
	}
	
	private function get_venues($venue_id = NULL)
	{
		$sql = "SELECT * FROM tb_program_venue ";
		
		if( !is_null($venue_id))
		{
			$sql .=" WHERE venue_id = '{$venue_id}' ";
		}
		else 
		{
			$sql .=" WHERE isActive = 1 ";
		}
		
		return $this->db->query($sql);
	}

	private function get_holidays($holiday_id = NULL)
	{
		//return $this->db->order_by('holiday_type','desc')->get('tb_holiday');
		$sql = "SELECT * FROM tb_holiday ";
		
		if( !is_null($holiday_id))
		{
			$sql .=" WHERE holiday_id = '{$holiday_id}' ";
		}
		
		$sql .= " ORDER BY holiday_type DESC, SUBSTRING( date, 4, 2 ) , SUBSTRING( date, 1, 2 )";
		return $this->db->query($sql);		
	}

	private function list_holidays()
	{
		$data = array();
		$rows = $this->get_holidays()->result_array();
		foreach($rows as $row)
		{
			$date_arr = explode('|',$row['date']);
			if($row['holiday_type'] == 'regular')
			{				
				$row['date'] = array('day'=>intval($date_arr[0]),'month'=>intval($date_arr[1]));
			}
			else 
			{
				$row['date'] = array('day'=>intval($date_arr[0]),'month'=>intval($date_arr[1]),'year'=>intval($date_arr[2]));
			}
			
			array_push($data, $row);
		}
		
		return $data;
	}
	
	function timediff()
	{
		$from = strtotime($this->uri->segment(3));
		$to = strtotime($this->uri->segment(4));
		
		$diff = $to - $from;
		
		echo round(($diff/604800),2);
		
	}	
	
	function test()
	{
					//$q = $this->get_programs_setting(1);
					//echo '<pre>';print_r(is_holiday('2012-04-20'));
			var_dump(is_holiday($this->uri->segment(3)));
	}
	
	/*
	 * SETTINGS
	 */
	
	function settings()
	{
		if(userPrivilege('isAdmin') || userPrivilege('program_misc_setting'))
		{
			$data['userid'] = $this->my_session->userdata('uid');
			$data['uname'] = $this->my_session->userdata('uname');
			$data['title'] = "Schedule Settings";
			$data['content'] = "vScheduleSettings";
			
			$data['venues'] = $this->get_venues()->result_array();
			$data['speakers'] = $this->get_speakers()->result_array();
			$data['programs'] = $this->db->order_by('name,title')->get('tb_programtemplate')->result_array();
			$data['holidays'] = $this->list_holidays();
			$data['months'] = $this->months;
	
			$this->load->view('template',$data);
		}
		else show_404();
	}
	
	function add_venue()
	{
		if(IS_AJAX)
		{
			$this->load->view('vProgramVenueEditor');
		}
		else show_404();
	}
	
	function edit_venue()
	{
		if(IS_AJAX)
		{
			$data = $this->get_venues($this->input->post('venue_id'))->row_array();
			$this->load->view('vProgramVenueEditor',$data);
		}
		else show_404();
	}
	
	function save_venue()
	{
		if(IS_AJAX)
		{
			$this->load->library('form_validation','','validate');
			$this->validate->set_error_delimiters('','<br>');
			
			$return['status'] = "";
			$this->validate->set_rules('venue_name','venue name','trim|ucwords|required');
			$this->validate->set_rules('venue_address','venue address','trim');
			$this->validate->set_rules('limit','venue limit','trim|numeric|required');
			
			if($this->validate->run()===TRUE)
			{
				if(isset($_POST['venue_id']))
				{
					$this->db->where('venue_id',$this->input->post('venue_id'))->set($_POST)->update('tb_program_venue');
				}
				else
				{
					$this->db->set($_POST)->insert('tb_program_venue');
				}
			}
			else 
			{
				$return['status'] = "error";
				$return['message'] = validation_errors();
			}
			
			echo json_encode($return);			
		}
		else show_404();
	}
	
	function delete_venue()
	{
		if(IS_AJAX)
		{
			$this->db->where('venue_id',$this->input->post('venue_id'))->set('isActive',0)->update('tb_program_venue');
		}
		else show_404();
	}
	
	function add_speaker()
	{
		if(IS_AJAX)
		{
			$this->load->view('vProgramSpeakerEditor');
		}
		else show_404();
	}
	
	function edit_speaker()
	{
		if(IS_AJAX)
		{
			$data = $this->get_speakers($this->input->post('speaker_id'))->row_array();
			$this->load->view('vProgramSpeakerEditor',$data);
		}
		else show_404();
	}
	
	function save_speaker()
	{
		if(IS_AJAX)
		{
			$this->load->library('form_validation','','validate');
			$this->validate->set_error_delimiters('','<br>');
			
			$return['status'] = "";
			$this->validate->set_rules('firstname','firstname','trim|ucwords|required');
			$this->validate->set_rules('lastname','lastname','trim|ucwords|required');
			$this->validate->set_rules('contact','contact','trim');
			
			if($this->validate->run()===TRUE)
			{
				if(isset($_POST['speaker_id']))
				{
					$this->db->where('speaker_id',$this->input->post('speaker_id'))->set($_POST)->update('tb_program_speaker');
				}
				else
				{
					$this->db->set($_POST)->insert('tb_program_speaker');
				}
			}
			else 
			{
				$return['status'] = "error";
				$return['message'] = validation_errors();
			}
			
			echo json_encode($return);			
		}
		else show_404();
	}

	function delete_speaker()
	{
		if(IS_AJAX)
		{
			$this->db->where('speaker_id',$this->input->post('speaker_id'))->set('isActive',0)->update('tb_program_speaker');
		}
		else show_404();
	}
	
	function add_holiday()
	{
		if(IS_AJAX)
		{
			$data['months'] = $this->months;
			$this->load->view('vHolidayEditor',$data);
		}
		else show_404();
	}
	
	function edit_holiday()
	{
		if(IS_AJAX)
		{
			$row = $this->get_holidays($this->input->post('holiday_id'))->row_array();
			$row['months'] = $this->months;
			$date_arr = explode('|',$row['date']);
			if($row['holiday_type'] == 'regular')
			{				
				$row['day'] = $date_arr[0];
				$row['month'] = $date_arr[1];
			}
			else 
			{
				$row['day'] = $date_arr[0];
				$row['month'] = $date_arr[1];
				$row['year'] = $date_arr[2];
			}
			$this->load->view('vHolidayEditor',$row);
		}
		else show_404();
	}
	
	function save_holiday()
	{
		if(IS_AJAX)
		{
			$this->load->library('form_validation','','validate');
			$this->validate->set_error_delimiters('','<br>');
			
			$return['status'] = "";			
			$this->validate->set_rules('month','month','trim|required');
			$this->validate->set_rules('day','day','trim|required');
			
			if($_POST['holiday_type'] == 'special')
			{
				$this->validate->set_rules('year','year','trim|numeric|required|min_length[4]');
			}
			
			if($this->validate->run()===TRUE)
			{
				
				$data['description'] = $_POST['description'];
				$data['holiday_type'] = $_POST['holiday_type'];
				
				if($_POST['holiday_type'] == 'regular')
				{
					$data['date'] = implode('|', array($_POST['day'],$_POST['month']));
				}
				else 
				{
					$data['date'] = implode('|', array($_POST['day'],$_POST['month'],$_POST['year']));
				}
				
				if(isset($_POST['holiday_id']))
				{
					$this->db->where('holiday_id',$_POST['holiday_id'])->set($data)->update('tb_holiday');
				}
				else
				{
					$this->db->set($data)->insert('tb_holiday');
				}
			}
			else 
			{
				$return['status'] = "error";
				$return['message'] = validation_errors();
			}
			
			echo json_encode($return);			
		}
		else show_404();
	}

	function delete_holiday()
	{
		if(IS_AJAX)
		{
			$this->db->where('holiday_id',$this->input->post('holiday_id'))->delete('tb_holiday');
		}
		else show_404();
	}	
	
	/*
	 * CALENDAR VIEW
	 */
	
	function calendar()
	{
		
		// The third segment will be used as timeid
		$timeid = $this->uri->segment(3);
		if($timeid==0)
			$time = strtotime(NOW);
		else
			$time = $timeid;
		
		$data = $this->_date($time);

		$data['userid'] = $this->my_session->userdata('uid');
		$data['uname'] = $this->my_session->userdata('uname');
		$data['title'] = "Calendar View";
		$data['content'] = "vProgramCalendar";
		$data['months'] = $this->months;
		
		$this->load->view('template',$data);		
	}
	
	function jump_time()
	{
		$time = mktime(0,0,0,$this->uri->segment(3),1,$this->uri->segment(4));
		redirect('schedule/calendar/'.$time);
	}
	
	function _date($time)
	{
		$data['events']=$this->getEvents($time);
		$data['time']= $time;
	
		$today = date("Y/n/j", strtotime(NOW));
		$data['today']= $today;
		
		$current_month = date("n", $time);
		$data['current_month'] = $current_month;
		
		$current_year = date("Y", $time);
		$data['current_year'] = $current_year;
		
		$current_month_text = date("F Y", $time);
		$data['current_month_text'] = $current_month_text;
		
		$total_days_of_current_month = date("t", $time);
		$data['total_days_of_current_month']= $total_days_of_current_month;
		
		$first_day_of_month = mktime(0,0,0,$current_month,1,$current_year);
		$data['first_day_of_month'] = $first_day_of_month;
		
		//geting Numeric representation of the day of the week for first day of the month. 0 (for Sunday) through 6 (for Saturday).
		$first_w_of_month = date("w", $first_day_of_month);
		$data['first_w_of_month'] = $first_w_of_month;
		
		//how many rows will be in the calendar to show the dates
		$total_rows = ceil(($total_days_of_current_month + $first_w_of_month)/7);
		$data['total_rows']= $total_rows;
		
		//trick to show empty cell in the first row if the month doesn't start from Sunday
		$day = -$first_w_of_month;
		$data['day']= $day;
		
		$next_month = mktime(0,0,0,$current_month+1,1,$current_year);
		$data['next_month']= $next_month;
		
		$next_month_text = date("F \'y", $next_month);
		$data['next_month_text']= $next_month_text;
		
		$previous_month = mktime(0,0,0,$current_month-1,1,$current_year);
		$data['previous_month']= $previous_month;
		
		$previous_month_text = date("F \'y", $previous_month);
		$data['previous_month_text']= $previous_month_text;
		
		$next_year = mktime(0,0,0,$current_month,1,$current_year+1);
		$data['next_year']= $next_year;
		
		$next_year_text = date("F \'y", $next_year);
		$data['next_year_text']= $next_year_text;
		
		$previous_year = mktime(0,0,0,$current_month,1,$current_year-1);
		$data['previous_year']=$previous_year;
		
		$previous_year_text = date("F \'y", $previous_year);
		$data['previous_year_text']= $previous_year_text;
		
		return $data;
  
	}

	private function getEvents($time)
	{
		
		$current_day = date("d", $time);
		$current_month = date("n", $time);
		$current_year = date("Y", $time);
		$current_month_text = date("F Y", $time);
		$total_days_of_current_month = date("t", $time);
		
		$events = array();
		
		$sql = "SELECT DATE_FORMAT(session_date,'%d') AS day,session_id,name,title,batch, (
					SELECT COUNT( * ) AS dayTotal
					FROM tb_program_session ses1
					JOIN tb_program_venue ven1 ON ven1.venue_id = ses1.session_venue
					WHERE ses1.session_date = ses.session_date
					AND ses1.session_venue = ses.session_venue
					) AS counter, ven.limit, venue_name, 
					(
						SELECT COUNT( * ) AS tempDayTotal
						FROM tb_program_session ses1
						JOIN tb_program_speaker sp1 ON sp1.speaker_id = ses1.session_speaker
						WHERE ses1.session_date = ses.session_date
						AND ses1.session_speaker = ses.session_speaker
						GROUP BY session_date, session_speaker
					) AS speaker_counter, CONCAT( firstname, ' ', lastname ) AS speaker_name
				FROM tb_program_session ses
				JOIN tb_program_schedule sch ON ses.schedule_id = sch.schedule_id
				JOIN tb_programtemplate pset ON pset.id = sch.program_setting_id
				JOIN tb_program_venue ven ON ven.venue_id = ses.session_venue
				JOIN tb_program_speaker sp ON sp.speaker_id = ses.session_speaker
				WHERE session_date BETWEEN  '$current_year/$current_month/01' 
				AND '$current_year/$current_month/$total_days_of_current_month'
				AND pset.status = 1 ";
		
		$filter = unserialize($this->input->cookie('calendar_filters'));
		
		if(isset($filter['session_speaker']) AND !empty($filter['session_speaker'])) $sql.= " AND ses.session_speaker = '{$filter['session_speaker']}' ";
		if(isset($filter['session_venue']) AND !empty($filter['session_venue'])) $sql.= " AND ses.session_venue = '{$filter['session_venue']}' ";
		
		$query = $this->db->query($sql);
		
		foreach ($query->result() as $row_event)
		{
			$events[intval($row_event->day)][] = $row_event;
		}
		$query->free_result(); 
		return $events;
	}

	function filter_calendar()
	{
		if(IS_AJAX)
		{
			$data['venues'] = $this->get_venues()->result_array();
			$data['speakers'] = $this->get_speakers()->result_array();
			$data['filter'] = unserialize($this->input->cookie('calendar_filters'));
			$data['segment'] = $this->input->post('segment');
			
			$this->load->view('vFilterCalendar',$data);
		}
		else show_404();
	}
	
	function save_filter()
	{
		if(isset($_POST))
		{
			$this->input->set_cookie('calendar_filters',serialize($_POST),0);
			redirect('schedule/calendar/'.$this->uri->segment(3));
		}
		else show_404();
	}
	
	/*
	 * Group By Program View
	 */
	
	function by_program()
	{
		$data['userid'] = $this->my_session->userdata('uid');
		$data['uname'] = $this->my_session->userdata('uname');
		$data['title'] = "Program Schedules";
		$data['content'] = "vScheduleByProgram";
		
		$program_data = array();
		$programs = $this->get_programs_setting('',TRUE)->result_array();
		
		if(isset($_POST['year_selector'])){
			$selected_year = $_POST['year_selector'];
		}else{
			$selected_year = date('Y',strtotime(NOW));			
		}	
		$data['selected_year'] = $selected_year;			
		
		foreach($programs as $program)
		{
			$sql = "SELECT * 
					FROM tb_program_schedule sc
					JOIN (
							SELECT schedule_id, MIN(session_date) AS session_date
							FROM tb_program_session
							GROUP BY schedule_id
						) AS ss ON sc.schedule_id = ss.schedule_id 
					WHERE DATE_FORMAT(ss.session_date,'%Y') = '".$selected_year."'";			
			$sql .=" AND program_setting_id = '{$program['id']}'
					 GROUP BY sc.schedule_id ";
			
			$schedules = $this->db->query($sql);
			
			if($schedules->num_rows() > 0)
			{
				$schedule = array();
				foreach($schedules->result_array() as $value)
				{
					$str = '';			
					
					$sessions = $this->get_program_session_with_limit($value['schedule_id'])->result_array();
					
					foreach($sessions as $session)
					{
						$date = date("F j",strtotime($session['session_date']));
						$temp_str_arr = explode(' ', $date); 
						
						$cssClass = "";
						$date_id = "";
						
						if($session['speaker_counter'] > 1 AND $session['counter'] > $session['limit'])
						{
							$cssClass =  "both_alert ";					
							$date_id = $session['session_date'];
						}
						elseif($session['counter'] > $session['limit'])
						{
							$cssClass =  "venue_alert ";					
							$date_id = $session['session_date'];
						}
						elseif($session['speaker_counter'] > 1)
						{
							$cssClass =  "speaker_alert ";					
							$date_id = $session['session_date'];
						}	
						
						$link = '<a class="sessionDetails '.$cssClass.'" id="'.$session['session_id'].'" date_id ="'.$date_id.'">';
						$str .= strpos($str, $temp_str_arr[0]) === FALSE ? $link.$date.'</a>, ' : $link.$temp_str_arr[1].'</a>, ';
					}
					
					$value['sessions'] = substr($str, 0, -2);
					array_push($schedule,$value);
				}
				
				$program['schedules'] = $schedule;
				
				array_push($program_data, $program);
			}
		}
		
		//$data['programs'] = $program_data;
		//echo '<pre>';print_r($program_data);exit();		
		$previous_type_schedule = $this->get_old_type_program_by_program($selected_year);
		
		$program_data = $this->merge_old_program_schedules($program_data, $previous_type_schedule);
		//echo '<pre>';print_r($program_data);exit();	
		
		$chunk = array_chunk($program_data,ceil(count($program_data)/2),TRUE);
		$data['right'] = isset($chunk[0])?$chunk[0]:array();
		$data['left'] = isset($chunk[1])?$chunk[1]:array();
		
		$this->load->view('template',$data);
	}
	
	private function get_old_type_program_schedules($selected_year,$selected_months = array(1,2,3,4,5,6,7,8,9,10,11,12))
	{
		$months = array();
		foreach($this->months as $k=>$v)
		{
            if(in_array($k, $selected_months))
            {
                $sql = "SELECT *,p.id program_id 
                        FROM tb_programs p
                        JOIN tb_programtemplate st ON p.programTempID = st.id
                        WHERE DATE_FORMAT(p.dateEnd,'%Y-%c') = '".$selected_year.'-'.$k."'
                        AND schedule_id = 0";

                $month['month'] = $v.'-'.date('y',strtotime($selected_year.'-01-01'));
                $programs = $this->db->query($sql)->result_array();

                $schedule = array();
                foreach($programs as $program)
                {
                    //$date = date("F j",strtotime($program['dateEnd'].' + 1 day'));
                    $date = date("F j",strtotime($program['dateEnd']));

                    $program['end_date'] = $program['dateEnd'];
                    $program['start_date'] = $program['dateStart'];
                    $program['sessions'] = $date;
                    array_push($schedule,$program);
                }

                $month['programs'] = $schedule;

                array_push($months,$month);
            }
		}
		//echo '<pre>';print_r($months);
		return $months;
	}

	
	private function merge_schedules($a,$b,$key)
	{	
		$new_array = array();
		foreach($a as $k=>$v)
		{
			$v2 = $b[$k];
			foreach($v2[$key] as $val)
			{
				array_push($v[$key], $val);
			}
			
			array_push($new_array,$v);
		}
		
		return $new_array;
	}
	
	
	function get_old_type_program_by_program($selected_year = 2012)
	{
		$program_settings = $this->get_programs_setting('',TRUE)->result_array();	
		$program_data = array();
		foreach($program_settings as $programs)
		{
			$sql = "SELECT *,p.id program_id 
					FROM tb_programs p
					WHERE DATE_FORMAT(p.dateEnd,'%Y') = '".$selected_year."' ";			
			$sql .="AND programTempID = '{$programs['id']}' 
					AND schedule_id = 0";
			
			$program = $this->db->query($sql)->result_array();
			$schedules = array();
			foreach($program as $schedule)
			{
				//$date = date("F j",strtotime($schedule['dateEnd'].' + 1 day'));
                $date = date("F j",strtotime($schedule['dateEnd']));

				$schedule['end_date'] = $schedule['dateEnd'];
				$schedule['start_date'] = $schedule['dateStart'];
				$schedule['sessions'] = $date;
				array_push($schedules,$schedule);
			}
			
			$programs['schedules'] = $schedules;
				
			array_push($program_data, $programs);
		}
		//echo '<pre>';print_r($program_data);
		return $program_data;
	}

	private function merge_old_program_schedules($a,$b)
	{
		$result_array = array();
		$new_array = array();
		foreach($b as $v)
		{
			foreach($a as $v2)
			{
				if($v['id'] == $v2['id'])
				{
					//array_push($v['schedules'],$v2['schedules']);
					foreach($v2['schedules'] as $other_sched)
					{
						array_push($v['schedules'],$other_sched);
					}
				}			
			}
			array_push($result_array,$v);
		}
		
		foreach($result_array as $val)
		{
			if(count($val['schedules']) > 0)
			{
				array_push($new_array,$val);
			}
		}
		return $new_array;
	}
	
	function old_schedule_editor()
	{
		if(IS_AJAX)
		{
			$data = $this->db->where('id',$this->input->post('program_id'))->get('tb_programs')->row_array();
			$this->load->view('vOldProgramScheduleEditor',$data);
		}
		else show_404();
	}
	
	function save_old_program_schedule()
	{
		if(IS_AJAX)
		{
			$this->load->library('form_validation','','validate');
			$this->validate->set_error_delimiters('','<br>');
			
			$return['status'] = "";
			$this->validate->set_rules('target','target','trim|numeric|required');
			
			if($this->validate->run()===TRUE)
			{
				$this->db->where('id',$this->input->post('id'))
					->set('target',$this->input->post('target'))
					->set('dateEnd',$this->input->post('dateEnd'))
					->update('tb_programs');
			}
			else 
			{
				$return['status'] = "error";
				$return['message'] = validation_errors();
			}
			
			echo json_encode($return);					
		}
	}
    
    //kaka add ko lang nito, at pang sakin lang to
    function delete_schedule()
    {
        if(IS_AJAX AND my_session_value('uid') == 1)
        {
            $this->db->where('schedule_id',$this->input->post('schedule_id'))->delete('tb_program_schedule');
            $this->db->where('schedule_id',$this->input->post('schedule_id'))->delete('tb_program_session');
        }
        else show_404 ();
    }
}