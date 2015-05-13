<?php 
class Settings extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if(!checksession('logged_in'))
		{
			$this->my_session->set_userdata('returnUrl',current_url());
			redirect(site_url('user'));
		}
		if (userPrivilege('isAdmin')!=1) {
			redirect(site_url('user'));exit();
		}		
		$this->load->model('msettings');
	}
	
	function index()
	{
		$data['uid'] = $this->my_session->userdata('uid');
		$data['uname'] = $this->my_session->userdata('uname');
		$data['title'] = "Settings";
		$data['content'] = "vsettings";
		$data['allowedip'] = $this->msettings->getIP()->result_array();
		$this->load->view('template',$data);
	}
	
	function saveIP()
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$this->load->library('form_validation','','validate');
			$return['status'] = "";
			$this->validate->set_rules('ip','IP address','trim|valid_ip|required');
			
			if($this->validate->run() === TRUE)
			{
				$this->msettings->addIP($_POST);
			}
			else
			{
				$return['status'] = "error";
				$return['message'] = validation_errors();
			}
			
			echo json_encode($return);
		}
	}
	
	function deleteIP()
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$this->msettings->deleteIP($this->input->post('ipID'));
		}
	}
	
	function editRobot()
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$this->load->view('vRobotProfileEditor');
		}		
	}
	
	function saveRobot()
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$photostr = $this->input->post('photo');
			if(strpos($photostr, 'temp'))
			{
				$orig = $photostr;
				$ext = substr($photostr, strrpos($photostr, '.') + 1);
				$photostr = substr($photostr,strpos($photostr, 'assets/'),strlen($photostr)-strpos($photostr, 'assets/'));
				$photo = "robotPhoto.".$ext;
				copy($orig,'assets/images/userphoto/'.$photo);
				$files = glob("assets/images/userphoto/temp/*.".$ext); 
				
				foreach($files as $file){
					unlink($file); 
				}
				$this->db->where('key','robot_photo')->set('value',$photo)->update('tb_settings');
			}
			$this->db->where('key','robot_name')->set('value',$this->input->post('robotName'))->update('tb_settings');
		}			
	}
	
	function savePostTemplate()
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$this->load->library('form_validation','','validate');
			$return['status'] = "";
			$this->validate->set_rules('postTitle','title','trim|required');
			$this->validate->set_rules('postMessage','content','trim|required');
			$this->validate->set_rules('consecPostMessage','content','trim|required');
			
			if($this->validate->run() === TRUE)
			{
				$this->db->where('key','weekly_post_top_title')->set('value',$this->input->post('postTitle'))->update('tb_settings');
				$this->db->where('key','weekly_post_top_content')->set('value',$this->input->post('postMessage'))->update('tb_settings');
				$this->db->where('key','weekly_post_consecutive')->set('value',$this->input->post('consecPostMessage'))->update('tb_settings');
			}
			else
			{
				$return['status'] = "error";
				$return['message'] = validation_errors();
			}
			
			echo json_encode($return);
		}
	}
	
	function programSuccessSaveTemplate()
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$this->load->library('form_validation','','validate');
			$return['status'] = "";
			$this->validate->set_rules('postTitle','title','trim|required');
			$this->validate->set_rules('postMessage','content','trim|required');
			
			if($this->validate->run() === TRUE)
			{
				$this->db->where('key','success_program_title')->set('value',$this->input->post('postTitle'))->update('tb_settings');
				$this->db->where('key','success_program_content')->set('value',$this->input->post('postMessage'))->update('tb_settings');
			}
			else
			{
				$return['status'] = "error";
				$return['message'] = validation_errors();
			}
			
			echo json_encode($return);
		}		
	}
}

?>