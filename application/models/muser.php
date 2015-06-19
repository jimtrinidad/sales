<?php 
class Muser extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function getIP()
	{
		$ip = array();
		$q = $this->db->query("SELECT ip FROM tb_allowed_ip")->result_array();
		foreach ($q as $v)
		{
			$ip[] = $v['ip'];
		}
		return $ip;
	}	
	
	function authenticateUser($username,$password,&$uid,&$uname,&$privilege)
	{
		$this->db->where('username',$username);
		$this->db->where('password',$password);
		$this->db->where('isActive','1');
		$q = $this->db->get('tb_users');
		
		if($q->num_rows()>0)
		{
			$row=$q->row();
			$uid = $row->id;
			
			//get privilege
			$this->db->where('id',$row->p_id);
			$p = $this->db->get('tb_privilege');
			$privilege = $p->row_array();
			
			$uname = $row->firstname." ".$row->lastname;
			
			$this->db->where('id',$uid);
			$this->db->set('lastLoggin',NOW);
			$this->db->update('tb_users');
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
/*
 * Functions for security
 */	
	function checkUser($username)
	{
		$sql = "SELECT username FROM tb_users WHERE username = '{$username}'";
		if($this->db->query($sql)->num_rows()>0)
		{
			return TRUE;
		}
		else 
		{
			return FALSE;
		}
	}
	function checkifDisabled($ip,$username)
	{
		$sql = "SELECT attempts, (CASE WHEN DATE_ADD(lastLogin, INTERVAL 30 MINUTE)>NOW() THEN 1 ELSE 0 END) as denied 
				FROM tb_login_attempts 
				WHERE ip = '{$ip}' 
				AND username = '{$username}'
				AND attempts = 3";
		if($this->db->query($sql)->num_rows()>0)
		{
			$data = $this->db->query($sql)->row_array();
			//if($username=='anonymous')
			{
				if($data['denied']==1)
				{
					$this->db->query("UPDATE tb_login_attempts SET lastLogin = '".date("Y-m-d H:i:s",strtotime(NOW))."' WHERE ip = '{$ip}' AND username = '{$username}' ");
					return TRUE;
				}
				else 
				{
					return FALSE;
				}
			}
			//else 
			{
				//return TRUE;
			}
		}
		else 
		{
			return FALSE;
		}		
	}
	function addLoginAttempt($ip,$username)
	{
		$sql = "SELECT attempts, (CASE WHEN DATE_ADD(lastLogin, INTERVAL 30 MINUTE)>NOW() THEN 1 ELSE 0 END) as denied 
				FROM tb_login_attempts 
				WHERE ip = '{$ip}' 
				AND username = '{$username}'";
		$data = $this->db->query($sql)->row_array();
		if(!$data)
		{
			$this->db->query("INSERT tb_login_attempts VALUES(null,'{$ip}','{$username}',1,'".date("Y-m-d H:i:s",strtotime(NOW))."')");
		}
		else
		{
			if($data['denied']==1)
			{
				$this->db->query("UPDATE tb_login_attempts SET attempts = attempts+1,lastLogin = '".date("Y-m-d H:i:s",strtotime(NOW))."' WHERE ip = '{$ip}' AND username = '{$username}' ");
			}
			else 
			{
				$this->db->query("UPDATE tb_login_attempts SET attempts = 1, lastLogin = '".date("Y-m-d H:i:s",strtotime(NOW))."' WHERE ip = '{$ip}' AND username = '{$username}' ");
			}
		}
	}
	function clearLoginAttempts($ip,$username)
	{
		$this->db->query("UPDATE tb_login_attempts SET attempts=0, lastLogin = '".date("Y-m-d H:i:s",strtotime(NOW))."' WHERE ip = '{$ip}' AND username = '{$username}' ");
	}
//END	
	

	function getUsers($limit,$offset)
	{
		$this->db->order_by('isActive','desc');
		$this->db->order_by('id');
		return $this->db->get('tb_users',$limit,$offset);
	}

	//check if email exist
	function checkEmail($email,$id="")
	{
		$this->db->where('id !=',$id);
		$q = $this->db->get_where('tb_users',array('email'=>$email));
		if($q->num_rows()>0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	//check if student number exist
	function checkUsername($user,$id="")
	{
		$this->db->where('id !=',$id);
		$q = $this->db->get_where('tb_users',array('username'=>$user));
		if($q->num_rows()>0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	//add privileges
	function addPrivilege($privilege)
	{
		$this->db->set($privilege);
		$this->db->insert('tb_privilege');
		return $this->db->insert_id();
	}

	//add user
	function addUser($data,$programs,$archives)
	{
		$programs = is_array($programs)?$programs:array();//check if the 2rd param is array. if not, make it array
		$archives = is_array($archives)?$archives:array();
		$this->db->set($data);
		$this->db->set('dateAdded',NOW);
		$this->db->insert('tb_users');
		$id = $this->db->insert_id();
		if(count($programs)>0)
		{
			foreach($programs as $program)
			{
				if($this->db->get_where('tb_programs',array('id'=>$program))->num_rows()>0)
				{
					$sql = "INSERT tb_user_program VALUES(null,'$program','$id','1')"; 
					$this->db->query($sql);	
					$upid = $this->db->insert_id();
					//$this->db->set('userID',$id)->set('userProgramID',$upid)->insert('tb_user_archive');// also add to the user archive
					$sql = "INSERT tb_user_archive VALUES(null,'$id','$upid','1')"; // also add to the user archive
					$this->db->query($sql);
				}
			}
		}
		
		if(count($archives)>0)
		{
			foreach($archives as $archive)
			{
				if($this->db->get_where('tb_user_program',array('id'=>$archive))->num_rows()>0)
				{
					$sql = "INSERT tb_user_archive VALUES(null,'$id','$archive','1')"; 
					$this->db->query($sql);	
				}
			}
		}		
	}

	//edit privileges
	function editPrivilege($privilege,$id)
	{
		$this->db->where('id',$id);
		$this->db->set($privilege);
		$this->db->update('tb_privilege');
	}
	
	//edit user
	function editUser($data,$id,$programs,$archives)
	{
		$str = "";
		$programs = is_array($programs)?$programs:array();//check if the 3rd param is array. if not, make it array
		$archives = is_array($archives)?$archives:array();
		$this->db->where('id',$id);
		$this->db->set($data);
		$this->db->update('tb_users');
		
		//start for archives
		$q = $this->db->where('userID',$id)->get('tb_user_archive');
		if($q->num_rows()>0)
		{
			foreach ($q->result_array() as $userarchive)
			{
				if(!in_array($userarchive['userProgramID'], $archives))//tingnan kung ang lumang userprogram id ay wala bagong set ng archive ng user
				{
					if($this->db->query("SELECT * FROM tb_user_archive WHERE isActive = 1 AND id = '{$userarchive['id']}'")->num_rows()>0)//apply only if active before the changes
					{
						//$programDetails = $this->getPrograms($userprogram['programID']);
						//$str .= "<br><span style='margin-left:20px;'>Pulled out from ".$programDetails->row()->title." ".$programDetails->row()->batch;
					}
					$sql = "UPDATE tb_user_archive SET isActive = '0' WHERE id = '{$userarchive['id']}'"; //kapag wala, i-deactivate lng ang record
					$this->db->query($sql);
				}
			}		
		}
		if(count($archives)>0)
		{
			foreach($archives as $archive)
			{
				$this->db->where(array('userID'=>$id,'userProgramID'=>$archive));
				$q = $this->db->get('tb_user_archive');
				if($q->num_rows()>0)//kapag existing ang userid=>programID combination, i-activate lang ung record 
				{
					$this->db->where(array('userID'=>$id,'userProgramID'=>$archive));
					$this->db->set('isActive','1');
					$this->db->update('tb_user_archive');
					if($this->db->affected_rows()>0)
					{
						//$programDetails = $this->getPrograms($archive);
						//$str .= "<br><span style='margin-left:20px;'>Assigned to ".$programDetails->row()->title." ".$programDetails->row()->batch;
					}
				}
				else //kapag wala pa, mag lagay...
				{
					if($this->db->get_where('tb_user_program',array('id'=>$archive))->num_rows()>0)
					{
						$sql = "INSERT tb_user_archive VALUES(null,'$id','$archive','1')"; // also add to the user archive
						$this->db->query($sql);
						if($this->db->affected_rows()>0)
						{
							//$programDetails = $this->getPrograms($program);
							//$str .= "<br><span style='margin-left:20px;'>Assigned to ".$programDetails->row()->title." ".$programDetails->row()->batch;
						}
					}
				}
			}
		}		
		
		//start user programs
		$this->db->where('userID',$id);
		$q = $this->db->get('tb_user_program');//kuhanin ang mga program ni user na my $id na id
		if($q->num_rows()>0)
		{
			foreach ($q->result_array() as $userprogram)
			{
				if(!in_array($userprogram['programID'], $programs))//tingnan kung ang lumang program id ay wala bagong set ng programs ng user
				{
					if($this->db->query("SELECT * FROM tb_user_program WHERE isActive = 1 AND id = '{$userprogram['id']}'")->num_rows()>0)//apply only if active before the changes
					{
						$programDetails = $this->getPrograms($userprogram['programID']);
						$str .= "<br><span style='margin-left:20px;'>Pulled out from ".$programDetails->row()->title." ".$programDetails->row()->batch;
					}
					$sql = "UPDATE tb_user_program SET isActive = '0' WHERE id = '{$userprogram['id']}'"; //kapag wala, i-deactivate lng ang record
					$this->db->query($sql);
				}
			}		
		}
		if(count($programs)>0)
		{
			foreach($programs as $program)
			{
				$this->db->where(array('userID'=>$id,'programID'=>$program));
				$q = $this->db->get('tb_user_program');
				if($q->num_rows()>0)//kapag existing ang userid=>programID combination, i-activate lang ung record 
				{
					$this->db->where(array('userID'=>$id,'programID'=>$program));
					$this->db->set('isActive','1');
					$this->db->update('tb_user_program');
					if($this->db->affected_rows()>0)
					{
						$programDetails = $this->getPrograms($program);
						$str .= "<br><span style='margin-left:20px;'>Assigned to ".$programDetails->row()->title." ".$programDetails->row()->batch;
					}
				}
				else //kapag wala pa, mag lagay...
				{
					if($this->db->get_where('tb_programs',array('id'=>$program))->num_rows()>0)
					{
						$sql = "INSERT tb_user_program VALUES(null,'$program','$id','1')";
						$this->db->query($sql);	
						$upid = $this->db->insert_id();
						//$this->db->set('userID',$id)->set('userProgramID',$upid)->insert('tb_user_archive');// also add to the user archive
						$sql = "INSERT tb_user_archive VALUES(null,'$id','$upid',1)"; // also add to the user archive
						$this->db->query($sql);
						if($this->db->affected_rows()>0)
						{
							$programDetails = $this->getPrograms($program);
							$str .= "<br><span style='margin-left:20px;'>Assigned to ".$programDetails->row()->title." ".$programDetails->row()->batch;
						}
					}
				}
			}
		}

		return $str;		
	}

	//change password
	function changepassword($data,$id)
	{
		$this->db->where('id',$id);
		$this->db->update('tb_users',$data);
	}
	//disable user
	function delete($id)
	{
		$q = $this->db->get_where('tb_users',array('id'=>$id));
			if($q->num_rows()>0)
			{
				$row = $q->row();
				$this->db->query("UPDATE tb_privilege SET isActive = '0' WHERE id = '{$row->p_id}'");
				
				$this->db->query("UPDATE tb_user_program SET isActive = '0' WHERE id = '{$id}'");
				
				$this->db->query("UPDATE tb_users SET isActive = '0' WHERE id = '{$id}'");
	
				$this->db->where('user_id',$id);
				$this->db->delete('tb_trails');					
			}					
	}
	
	//enable user
	function enableuser($id)
	{
		$q = $this->db->get_where('tb_users',array('id'=>$id));
			if($q->num_rows()>0)
			{
				$row = $q->row();
				$this->db->query("UPDATE tb_privilege SET isActive = '1' WHERE id = '{$row->p_id}'");
				
				$this->db->query("UPDATE tb_user_program SET isActive = '1' WHERE id = '{$id}'");
				
				$this->db->query("UPDATE tb_users SET isActive = '1' WHERE id = '{$id}'");				
			}					
	}	
	
	//get record
	function getRecord($id)
	{
		$q = $this->db->get_where('tb_users',array('id'=>$id));
			if($q->num_rows()>0)
			{
				return $q->row_array();
			}
	}
	//get record privilege
	function getPrivilege($id)
	{
		$q = $this->db->get_where('tb_privilege',array('id'=>$id));
			if($q->num_rows()>0)
			{
				return $q->row_array();
			}
	}

	//get trails of user
	function trails($limit,$offset,$id)
	{
		$this->db->order_by('datetime','desc');
		$this->db->where('user_id',$id);
		return $this->db->get('tb_trails',$limit,$offset);
	}
	//count user trails
	function countTrails($id)
	{
		$this->db->where('user_id',$id);
		return $this->db->get('tb_trails')->num_rows();
	}
	//delete trail
	function deletetrail($id)
	{
		$this->db->where('id',$id);
		$this->db->delete('tb_trails');	
	}
	//delete all trail
	function deletealltrails($id)
	{
		$this->db->where('user_id',$id);
		$this->db->delete('tb_trails');	
	}	

	//get email og user
	function userEmail($id)
	{
		$this->db->where('id',$id);
		$q = $this->db->get('tb_users');
		if($q->num_rows())
		{
			return $q->row()->email;
		}
	}
	//get programs for user assigning
	function getPrograms($id = NULL)
	{
		$sql = "SELECT p.id id,title,logo,batch,details,dateStart,dateEnd,p.dateCreated dateCreated
				FROM tb_programs p
				JOIN tb_programtemplate pt ON p.programTempID = pt.id
				WHERE p.id IS NOT NULL ";
		
		if(is_null($id))
		{
			$sql .=" AND p.isActive = '1' ";
		}
		else
		{
			$sql .= " AND p.id = {$id}";
		}
		$sql .= " ORDER BY p.isActive DESC,pt.title,batch+0 ";
		return $this->db->query($sql);
	}

	function userProgram($id)
	{
		$sql = "SELECT p.id pid FROM tb_user_program up
				JOIN tb_programs p ON up.programID = p.id
				JOIN tb_programtemplate pt ON pt.id = p.programTempID
				WHERE up.userID = '$id'
				AND up.isActive = '1'
				AND p.isActive = '1'";
		$q = $this->db->query($sql);
		if($q->num_rows()>0)
		{
			return $q->result_array();
		}
		else
		{
			return FALSE;
		}
	}	

	//function to check if the user id exist
	function checkUserID($id)
	{
		$this->db->where('id',$id);
		$q = $this->db->get('tb_users');
		return $q->num_rows()==1?TRUE:FALSE;	
	}
	
	function addNote($data)
	{
		$this->db->set('postDate',NOW);
		$this->db->set($data);
		$this->db->insert('tb_notes');
		return $this->db->insert_id();
	}
	function saveNotePos($data,$noteid)
	{
		$this->db->where('id',$noteid);
		$this->db->set($data);
		$this->db->update('tb_notes');
	}
	function getUserNotes($id)
	{
		$this->db->where('userWall',$id);
		return $this->db->get('tb_notes')->result_array();
	}
	
	function getAllUserPrograms() // function to get records for assigning archives
	{
		$sql = "SELECT up.id userProgramID,u.id userID,title,batch,logo,firstname,lastname
				FROM tb_user_program up
				JOIN tb_programs p ON up.programID = p.id
				JOIN tb_programtemplate pt ON pt.id = p.programTempID
				JOIN tb_users u ON u.id = up.userID
				";
		$sql .= " ORDER BY title,batch+0 DESC,firstname ";
		return $this->db->query($sql)->result_array();
	}
	function userArchive($id)
	{
		$sql = "SELECT userProgramID
				FROM tb_user_archive ua
				WHERE ua.userID = '$id'
				AND ua.isActive = '1'";
		return $this->db->query($sql)->result_array();

	}
}
?>