<?php 
class Msettings extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
		//get allowed ip
	function getIP()
	{
		return $this->db->order_by('id','DESC')->get('tb_allowed_ip');
	}
	
	function addIP($data)
	{
		$this->db->set($data)->insert('tb_allowed_ip');
	}
	
	function deleteIP($ipID)
	{
		$this->db->where('id',$ipID)->delete('tb_allowed_ip');
	}	

	function old_getAnnouncement($limit,$offset)
	{
		$this->db->order_by('id','DESC');
		$q = $this->db->get('tb_announcement',$limit,$offset);
		if($q->num_rows()>0)
		{
			return $q->row();
		}
		else return FALSE;
	}	
	
	function getAnnouncement()
	{
		return $this->db->order_by('id','DESC')->limit(50)->get('tb_announcement');
	}
	
	function getAnnouncementByID($id)
	{
		$this->db->where('id',$id);
		$q = $this->db->get('tb_announcement');
		if($q->num_rows()>0)
		{
			return $q->row();
		}
		else return FALSE;
	}	
	
	function getUser($id)
	{
		$sql = "SELECT id,CONCAT(firstname,' ',lastname) name 
				FROM tb_users 
				WHERE  id = {$id} ";
		$q = $this->db->query($sql);
				if($q->num_rows()>0)
				{
					return $q->row()->name;
				}
				else return "Anonymous";
	}
	
	function addAnnouncement($data)
	{
		$this->db->set('postDate',NOW);
		$this->db->set($data);
		$this->db->insert('tb_announcement');
	}
	function editAnnouncement($data,$id)
	{
		$this->db->where('id',$id);
		$this->db->set($data);
		$this->db->update('tb_announcement');
	}
}

?>