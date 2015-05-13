<?php
class Support extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
                if(!checksession('logged_in') OR !in_array(my_session_value('uid'), array(1,19)) )
		{
			$this->my_session->set_userdata('returnUrl',current_url());
			redirect(site_url('user'));
		}
                $this->load->model('madmin');
	}
        
        function index(){   
            $programs = $this->madmin->get_program_template()->result_array();
            $this->load->view('support', array('programs'=>$programs));
        }
        
        function get_program_batch(){
            if(isset($_POST['program_id'])){
                $sql = "SELECT *
				FROM tb_program_schedule
				WHERE program_setting_id = '{$_POST['program_id']}'
				ORDER BY batch + 0";
		
		$results = $this->db->query($sql)->result_array();
                echo json_encode($results);
            }
        }
        
        function save_start_date(){
            if(isset($_POST['schedule_id'])){
                $this->db->where('schedule_id',$_POST['schedule_id'])->set('start_date', $_POST['start_date'])->update('tb_program_schedule');
                $this->db->where('schedule_id',$_POST['schedule_id'])->set('dateStart', $_POST['start_date'])->update('tb_programs');
            }
        }
	
}

?>