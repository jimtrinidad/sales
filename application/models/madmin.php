<?php 
class Madmin extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function get_program_by_template($template_id)
	{
		$sql = "SELECT *
				FROM tb_programs p
				WHERE programTempID = '{$template_id}'
				ORDER BY p.isActive DESC,batch + 0 DESC";	

		return $this->db->query($sql);
	}
	
	function get_program_template()
	{
		return $this->db->where('status',1)->order_by('title')->get('tb_programtemplate');
	}
	
	function get_latest_batch($program_id)
	{
		$sql = "SELECT *
				FROM tb_programs
				WHERE programTempID = '{$program_id}'
				ORDER BY batch + 0 DESC
				LIMIT 1";
		
		return $this->db->query($sql);
	}
	
	function get_next_batch($last_batch)
	{
		$sql = "SELECT *
				FROM tb_program_schedule ps
				JOIN tb_programtemplate pt ON ps.program_setting_id = pt.id
				WHERE program_setting_id = '{$last_batch['programTempID']}'
				AND batch = '".($last_batch['batch'] + 1)."'
				LIMIT 1";
		
		return $this->db->query($sql);
	}

	function add_next_program_batch($data)
	{
		$this->db->set('dateCreated',NOW);
		$this->db->set($data);
		$this->db->insert('tb_programs');
	}
	
	function update_program_batch_status($program_id,$status)
	{
		$this->db->where('id',$program_id)
			->set('isActive',$status)
			->update('tb_programs');
	}
	
	function get_program_batch($id)
	{
		return $this->db->where('id',$id)->get('tb_programs');
	}		
	
	function update_program_batch($program_id,$data)
	{
		$this->db->where('id',$program_id)
				->set($data)
				->update('tb_programs');
	}
	
	
	

	function editProgram($data,$id)
	{
		$this->db->where('id',$id);
		$this->db->update('tb_programs',$data);
	}
	function getSingleProgram($id)
	{
		$this->db->where('id',$id);
		return $this->db->get('tb_programs')->row();
	}	
	
	function addProgramTemp($data)
	{
		$this->db->set('dateCreated',NOW);
		$this->db->set($data);
		$this->db->insert('tb_programtemplate');
	}
	function editProgramTemp($data,$id)
	{
		$this->db->where('id',$id);
		$this->db->update('tb_programtemplate',$data);
	}
	function getSingleProgramTemp($id)
	{
		$this->db->where('id',$id);
		return $this->db->get('tb_programtemplate')->row();
	}

	function getWonPerWeek($programid,$weekno,$i="",$last="")//$i is the counter.. if $i is zero, add previously added to the first week,$last is lastweek nomber, if $i = $last, include those added after the last week 
	{
		$sql = "SELECT dt.id did,eventType,time,date,companyName,lastname,firstname,mi,position,telephone,mobile,fax,email
				FROM tb_details dt
				JOIN tb_information i ON dt.infoID = i.id
				JOIN tb_dates da ON dt.dateID = da.id
				JOIN tb_user_program up ON da.userProgramID = up.id
				JOIN tb_programs p ON up.programID = p.id
				JOIN tb_programtemplate pt ON pt.id = p.programTempID
				WHERE dt.id IS NOT NULL 
				AND dt.latest = 1 
				AND opportunityType = 'Won'
				AND p.id = {$programid} ";
		if($i==0)
		{
			$sql .= "AND DATE_FORMAT(dt.time,'%Y-%u') <= '{$weekno}' ";
		}
		elseif(($i+1)==$last)
		{
			$sql .= "AND DATE_FORMAT(dt.time,'%Y-%u') >= '{$weekno}' ";
		}
		else
		{
			$sql .= "AND DATE_FORMAT(dt.time,'%Y-%u') = '{$weekno}' ";
		}
		//echo $sql."<br>";
		return $this->db->query($sql)->num_rows();
	}
}

?>