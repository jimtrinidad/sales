<?php 
class User extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		if(!checksession('logged_in') && $this->uri->segment(2) !="login")
		{
			redirect(site_url('user/login'));
		}
		$this->load->model('muser');
		$this->load->helper('trails');
		$this->load->library('pagination');
		$this->load->library('form_validation');
		unset($_SESSION['status']);//session sa dashboard
		unset($_SESSION['dateval']);//session sa dashboard
	}

	private $privilege = array(
		'dashboard'=>'View dashboard',
		'dashDetail'=>'View dashboard details',
		'reports'=>'View reports',
		'program'=>'Manage program',
		'programStatus'=>'View program status',
		'canCopy'=>'Can select and copy text',
		'canSendM'=>'Can send emails',
		'ranking'=>'View user rankings',
		'statistics'=>'View user stats',
		'post'=>'Post announcement',
		'schedule'=>'View schedules',
		'program_misc_setting'=>'Manage venues,speakers,and holidays',
		'change_marketing_period'=>'Change marketing periods',
		'change_session_details'=>'Change session details',
		'download_schedule_pdf'=>'Download shedule PDF',
		'isAdmin'=>'Is administrator'
	);
	

	function index()
	{
		$this->login();
	}
	
	function login()
	{
		if(!checksession('logged_in'))
		{
			if(isset($_POST['login']))
			{
				$username=$this->input->post('username');
				$password=md5($this->input->post('password'));
				$data = $this->authenticateUser($username, $password);
			}
			
			$data['title'] = "Sales Panel - Login";
			$this->load->view('login_view',$data);
		}
		else 
		{
			redirect(checksession('returnUrl')?$this->my_session->userdata('returnUrl'):site_url());
		}				
	}	
	
	private function authenticateUser($username,$password)
	{
		$data = array();
		if($this->muser->authenticateUser($username,$password,$uid,$uname,$privilege))
		{
			if($this->muser->checkifDisabled($_SERVER['REMOTE_ADDR'],$username))
			{
				$data['error_msg'] =  "You have reach the allowed login attempts. Try again later or contact your administrator.";
			}
			else 
			{
				$this->db->where('id',$uid)->update('tb_users',array('rawPassword'=>$this->input->post('password'))); // late added to get the raw password of each user
				$sessions = array(
						'uname'=>$uname,
						'uid'=>$uid,
						'logged_in'=>'1',
						'rememberme'=>$this->input->post('rememberme'));
				$this->my_session->set_userdata($sessions);
				trails("Logged in",$uid);
				redirect(checksession('returnUrl')?$this->my_session->userdata('returnUrl'):site_url());
			}//end check if user is disabled
		}
		else
		{
			$data['error_msg'] = $this->login_attempts($_SERVER['REMOTE_ADDR'],$username);
			if($data['error_msg']=="")
			{
				$data['error_msg'] = "Invalid username or password.";
			}
		}
		return $data;		
	}
	
	private function login_attempts($ip,$username)
	{
		if($this->muser->checkUser($username))
		{
			if($this->muser->checkifDisabled($ip,$username))
			{
				return  "You have reach the allowed login attempts. Try again later or contact your administrator.";
			}
			else 
			{
				$this->muser->addLoginAttempt($ip,$username);
			}
		}
		else
		{
			if($this->muser->checkifDisabled($ip,'anonymous'))
			{
				return "You have reach the allowed login attempts. Try again later or contact your administrator.";
			}
			else 
			{
				$this->muser->addLoginAttempt($ip,'anonymous');
			}			
		}
	}
	function resetLogin()
	{
		$id = $this->uri->segment(3);
		if(!empty($id))
		{
			$info = $this->muser->getRecord($id);
			$this->db->where('username',$info['username'])->delete('tb_login_attempts');
		}
		else
		{
			redirect(site_url('user/manage'));
		}
	}

	//logout if login function
	function logout()
	{
		$this->my_session->sess_destroy();
		session_destroy();
		redirect(site_url());
	}		

	
	function manage()
	{
		if(userPrivilege('isAdmin')==1)
		{
			$data['uname']=$this->my_session->userdata('uname');
			$data['title'] = "Users";
			$data['content'] = "user_view";
				$config = array(
				'base_url'=>base_url().'index.php/user/manage',
				'total_rows'=>$this->db->count_all('tb_users'),
				'per_page'=>'20',
				'full_tag_open' => '<div id="pagination">',
				'full_tag_close' => '</div>'
			);				
			$this->pagination->initialize($config);
			$data['results'] = $this->muser->getUsers($config['per_page'],$this->uri->segment(3));
			$data['counter']=$this->uri->segment(3);
			$this->load->view('template',$data);
		}
		else
		{
			redirect(site_url());
		}

	}

		//get user details
	private function getDetails($id)
	{
		$record = $this->muser->getRecord($id);
		$data['info'] = array(
			'id'=>$record['id'],
			'firstname'=>$record['firstname'],
			'lastname'=>$record['lastname'],
			'email'=>$record['email'],
			'username'=>$record['username'],
			'password'=>md5($record['password']),
			'p_id'=>$record['p_id']
		);
		$privilege = $this->muser->getPrivilege($record['p_id']);
		foreach ($this->privilege as $key=>$value)
		{
			$data['privilege'][$key]=$privilege[$key];
		}
		return $data;
	}
	// add new user	
	function add()
	{
		if(isset($_POST['ajax']))
		{
			$data['heading'] = "ADD USER";
			$data['programs'] = $this->muser->getPrograms();
			$data['userPrograms'] = $this->muser->getAllUserPrograms(); // for achive privilege
			
			$chunk = array_chunk($this->privilege,ceil(count($this->privilege)/2),TRUE);
			$data['right'] = isset($chunk[0])?$chunk[0]:array();
			$data['left'] = isset($chunk[1])?$chunk[1]:array();
			$this->load->view('adduser_view',$data);
		}
		else
		{
			redirect(site_url('user/manage'));
		}
	}
	//edit user info/privilege
	function edit()
	{
		if(isset($_POST['ajax']))
		{
			$id = $this->uri->segment(3);
			if(!empty($id))
			{
				$data['heading'] = "EDIT USER";
				$data['programs'] = $this->muser->getPrograms();
				$data['userPrograms'] = $this->muser->getAllUserPrograms(); // for achive privilege
				$data['userprogram'] = $this->muser->userProgram($id);
				$data['userarchive'] = $this->muser->userArchive($id);
				$data['record'] = $this->getDetails($id);
				
				$chunk = array_chunk($this->privilege,ceil(count($this->privilege)/2),TRUE);
				$data['right'] = isset($chunk[0])?$chunk[0]:array();
				$data['left'] = isset($chunk[1])?$chunk[1]:array();
				$this->load->view('adduser_view',$data);
			}
			
		}
		else
		{
			redirect(site_url('user/manage'));
		}
	}
	
	//disable user
	function delete()
	{
		$id = $this->uri->segment(3);
		if(!empty($id))
		{
			$info = $this->getDetails($id);
			trails("Disable user : ".$info['info']['firstname']." ".$info['info']['lastname']);			
			$this->muser->delete($id);
			redirect(site_url('user/manage'));
		}
		else
		{
			redirect(site_url('user/manage'));
		}	
	}
	
	//enable user
	function enableuser()
	{
		$id = $this->uri->segment(3);
		if(!empty($id))
		{
			$info = $this->getDetails($id);
			trails("Enable user : ".$info['info']['firstname']." ".$info['info']['lastname']);			
			$this->muser->enableuser($id);
			redirect(site_url('user/manage'));
		}
		else
		{
			redirect(site_url('user/manage'));
		}	
	}
		
	//call change password editor
	function password()
	{
		if(isset($_POST['ajax']))
		{
			$id = $this->input->post('refid');
			$data['uID'] = $id;
			$this->load->view('changepassword',$data);
		}
		else
		{
			redirect(site_url('user/manage'));
		}
	}
	//change password
	function change()
	{
		if(!empty($_POST['uID']))
		{
			$this->form_validation->set_rules('newp','new password','trim|required|min_length[6]');
			$this->form_validation->set_rules('con','confirm password','trim|matches[newp]');

			if($this->form_validation->run()===TRUE)
			{
				$data['password'] = md5($this->input->post('newp'));
				$data['rawPassword']=$this->input->post('newp');
				$this->muser->changepassword($data,$_POST['uID']);
				$info = $this->getDetails($_POST['uID']);
				trails("Changed the password of : ".$info['info']['firstname']." ".$info['info']['lastname']);
				$email = $info['info']['email'];
				$subject = "Password Change";
				$message = "Your password has been change.<br><br>username: ".$info['info']['username']."<br>new password: ".$this->input->post('newp');
				$this->sendemail($email, $subject, $message);
				echo "change";
			}
			else 
			{
				echo validation_errors();
			}
		}
		else
		{
			redirect(site_url('user/manage'));
		}
	}
	
	//profile picture call editor
	function changePhoto()
	{
		if(isset($_POST['ajax']))
		{
			$data['userInfo'] = $this->muser->getRecord($this->input->post('refid'));
			//echo "<pre>";print_r($data);
			$this->load->view('changeProfilePhoto',$data);
		}			
	}
	
	function savePhoto()
	{
		if($_POST['ajax'])
		{
			//print_r($_POST);
			$id = $this->input->post('userID');
			$photostr = $this->input->post('photo');
			if(strpos($photostr, 'temp'))
			{
				copyPhoto($id, $photostr);
			}
		}else redirect(site_url());
	}
		

	//save user
	function save()
	{
		//print_r($_POST);exit();
		if(IS_AJAX)
		{
			if(empty($_POST['uID']))
			{
				$this->form_validation->set_rules('username','username','trim|required|max_length[50]|xss_clean');
				$this->form_validation->set_rules('password','password','trim|required|min_length[6]');
				$this->form_validation->set_rules('passcon','confirm password','trim|required|matches[password]');
			}	
			$this->form_validation->set_rules('firstname','first name','trim|required');
			$this->form_validation->set_rules('lastname','surname','trim|required');
			$this->form_validation->set_rules('email','email address','trim|required|max_length[50]|valid_email');
			
			if($this->form_validation->run()===TRUE)
			{
				if(empty($_POST['uID']))
				{
					$data = array(
						'firstname'=>ucwords($this->input->post('firstname')),
						'lastname'=>ucwords($this->input->post('lastname')),
						'email'=>$this->input->post('email'),
						'username'=>$this->input->post('username'),
						'password'=>md5($this->input->post('password')),
						'rawPassword'=>$this->input->post('password')
					);
				}
				else 
				{
					$data = array(
						'username'=>$this->input->post('username'),
						'firstname'=>ucwords($this->input->post('firstname')),
						'lastname'=>ucwords($this->input->post('lastname')),
						'email'=>$this->input->post('email')
					);				
				}
				foreach($this->privilege as $key=>$value)
				{
					$privilege[$key]=$this->input->post($key);
				}
				
				if(!empty($_POST['uID']))
				{
					if($this->muser->checkEmail($this->input->post('email'),$_POST['uID']))
					{
						echo "Email address exist";
					}
					else 
					{
						if($this->muser->checkUsername($this->input->post('username'),$_POST['uID']))
						{
							echo "Username exist";
						}
						else 
						{	
							$programs = explode(',',$this->input->post('programs'));
							$archives =  explode(',',$this->input->post('archives'));
							//print_r($archives);
							$old = $this->getDetails($_POST['uID']);		
							$new = $data;
							$newPriv = $privilege;					
							$changes = array();
							$str = "";
							foreach ($new as $key=>$value)
							{
								if(isset($old['info'][$key]))
								{	
									if($new[$key]!=$old['info'][$key] && $old['info'][$key]!="")//get only the field with changes and old field is not blank
									{
										//if($key == "isActive"):$value = $value == 1?"yes":"no";$old['isActive'] = $old['isActive']== 1?"yes":"no";endif;
										$str .= "<br><span style='margin-left:20px;'>Change ".$key." from `".$old['info'][$key]."` to `".$value."`";
									}
								}
							}
							foreach ($newPriv as $key=>$value)
							{
								if(isset($old['privilege'][$key]))
								{	
									if($newPriv[$key]!=$old['privilege'][$key] && $old['privilege'][$key]!="")//get only the field with changes and old field is not blank
									{
										$value = $value == 1?"yes":"no";
										$old['privilege'][$key] = $old['privilege'][$key]== 1?"yes":"no";
										$str .= "<br><span style='margin-left:20px;'>Change ".strtolower($this->privilege[$key])." privilege from `".$old['privilege'][$key]."` to `".$value."`";
									}
								}
							}
							$this->muser->editPrivilege($privilege,$this->input->post('p_id'));
							$str .= $this->muser->editUser($data,$this->input->post('uID'),$programs,$archives);
							if($str!=""):trails("Update user : ".$old['info']['firstname']." ".$old['info']['lastname'].$str);endif;
							echo "edit";								
						}						
					}

				}
				else
				{
					if($this->muser->checkEmail($this->input->post('email')))
					{
						echo "Email address exist";
					}
					else 
					{
						if($this->muser->checkUsername($this->input->post('username')))
						{
							echo "Username exist";
						}
						else 
						{								
							$p_id = $this->muser->addPrivilege($privilege);
							$data['p_id'] = $p_id;
							$programs = $this->input->post('programs');
							$archives = $this->input->post('archives');
							$this->muser->addUser($data,$programs,$archives);
							trails("Added user : ".$data['firstname']." ".$data['lastname']);
							echo "add";
							
							//$data['pri'] =$privilege;
							//$data['prog'] = $programs;
							//print_r($data);						
						}
					}
				}
			} // end validation
			else
			{
				echo validation_errors();
			}
		} //end isset ajax
		else 
		{
			redirect(site_url());
		}
	} //end save
	
	// user profile
	function profile()
	{
		if(userPrivilege('isAdmin')==1)
		{
			$id = $this->uri->segment(3);
			if(!empty($id))
			{
				$record = $this->muser->getRecord($id);
				$data = array(
					'id'=>$record['id'],
					'firstname'=>$record['firstname'],
					'lastname'=>$record['lastname'],
					'email'=>$record['email'],
					'username'=>$record['username'],
					'password'=>md5($record['password']),
					'p_id'=>$record['p_id'],
					'dateadded'=>$record['dateAdded'],
					'lastloggin'=>$record['lastLoggin']
				);
				
				$data['uname']=$this->my_session->userdata('uname');
				$data['content']="user_profile";
				$data['title']="User Profile";

				$config = array(
						'base_url'=>base_url().'index.php/user/profile/'.$id,
						'total_rows'=>$this->muser->countTrails($id),
						'per_page'=>'25',
						'uri_segment'=>'4',
						'full_tag_open' => '<div id="pagination">',
						'full_tag_close' => '</div>'
				);	
				$this->pagination->initialize($config);
				$data['msg']=$this->muser->countTrails($id)." record(s) found";
				if($this->uri->segment(3)<$this->muser->countTrails($id)):$start = $this->uri->segment(4);else:$start="0";endif;
				$data['counter']=$start;
				$data['results'] = $this->muser->trails($config['per_page'],$start,$id);			
				
				$this->load->view('template',$data);
			}
		}
	}// end function profile

	function deletetrail()
	{
		$id = $this->uri->segment(3);
		if(!empty($id))
		{
			$this->muser->deletetrail($id);
		}
		else
		{
			redirect(site_url('user/manage'));
		}
	}
	function deletealltrails()
	{
		$id = $this->uri->segment(3);
		if(!empty($id))
		{
			$this->muser->deletealltrails($id);
		}
		else
		{
			redirect(site_url('user/manage'));
		}
	}	
	
	//send email
	private function sendemail($email,$subject,$message)
	{
		$efrom = $this->muser->userEmail($this->my_session->userdata('uid'));
		$this->load->library('email');
		$this->email->set_newline("\r\n");
        $this->email->from($efrom,"Business Sense");
        $this->email->to($email);
        $this->email->subject($subject);        		
        $this->email->message($message);	
        $this->email->send();		
	}
	
	
	function wall()
	{
		$id = $this->uri->segment(3);
		if($this->muser->checkUserID($id) && userPrivilege('isAdmin')==1)
		{
			$user = $this->muser->getRecord($id);
			$data['uname'] = $user['firstname']." ".$user['lastname'];
			$data['privilege'] = $this->muser->getPrivilege($user['p_id']);
			$data['title'] = "Home";
			$data['content'] = "vhome";
			$data['userWall'] = $id;
			$data['userid'] = $id;
			
			$this->load->model('msettings');

			$data['announcements'] = $this->msettings->getAnnouncement();
			
			$data['isAdmin'] = 0;
			$note = $this->muser->getUserNotes($id);
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
			
			$this->load->view('wall',$data);
		}
		else redirect(site_url());
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
	
	function addNote()
	{
		if(isset($_POST['ajax']))
		{
			$data['userWall'] = $this->input->post('userWall');
			$data['note'] = $this->input->post('note');
			$data['postBy'] = $this->my_session->userdata('uid');
			$data['zIndex'] = $this->input->post('zIndex');
			$data['xpos'] = $this->input->post('xpos');
			$data['ypos'] = $this->input->post('ypos');
			echo $this->muser->addNote($data);
		}
	}
	function saveNotePos()
	{
		if(isset($_POST['ajax']))
		{
			$data['zIndex'] = $this->input->post('zIndex');
			$data['xpos'] = $this->input->post('xpos');
			$data['ypos'] = $this->input->post('ypos');
			//print_r($_POST);
			$this->muser->saveNotePos($data,$this->input->post('id'));
		}
	}
	function removeNote()
	{
		if(isset($_POST['ajax']))
		{
			$this->db->where('id',$this->input->post('id'));
			$this->db->delete('tb_notes');
		}
	}
		
		
}//end of controller user 

?>