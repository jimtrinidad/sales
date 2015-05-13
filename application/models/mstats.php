<?php
class Mstats extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function getUsers($id = "",$isActive = TRUE)
	{
		$sql = "SELECT u.id id,u.id userID,CONCAT(firstname,' ',lastname) name,firstname,lastname,u.isActive active,dateAdded
				FROM tb_users u
				JOIN tb_privilege p ON u.p_id = p.id
				JOIN tb_user_program up ON up.userID = u.id
				WHERE isAdmin = 0 
				AND u.id NOT IN(30) ";
			
		
			if($isActive){$sql .= "AND u.isActive = '1' ";}
			elseif(is_null($isActive)){}
			else{$sql .= "AND u.isActive = '0' ";}
			
			if($id != "")
			{
				$sql.= " AND u.id = {$id} ";
				$sql.= " AND up.isActive = 1 ";
				$q = $this->db->query($sql);
				if($q->num_rows() > 0 )
				{
					return $q->row_array();
				}else return FALSE;
			}
			else 
			{
				$sql .=" GROUP BY u.id ";
				$sql .=" ORDER BY firstname ";
				return $this->db->query($sql)->result_array();				
			}
	}
	
	function getUserProgram($id,$start = '')
	{
		$sql = "";
		$sql .="SELECT up.id userProgramID,p.id programID,programTempID,target,dateStart,dateEnd,CONCAT(title,' ',batch)program
				FROM tb_user_program up
				JOIN tb_programs p ON up.programID = p.id
				JOIN tb_programtemplate pt ON p.programTempID = pt.id
				WHERE up.userID = '$id'";
	/*	if($start!='')
		{
			$sql .=" AND (DATE_FORMAT(dateEnd,'%Y-%m-%d')>= '".date("Y-m-d",$start)."' OR DATE_FORMAT(dateStart,'%Y-%m-%d')>= '".date("Y-m-d",$start)."') ";
		}*/
		$sql .=" ORDER BY p.isActive DESC,title,batch";
		//echo $sql;
		return $this->db->query($sql)->result_array();
	}
	
	function getUserActiveProgram($id)
	{
		$sql = "";
		$sql .="SELECT up.id userProgramID,p.id programID,programTempID,target,dateStart,dateEnd,CONCAT(title,' ',batch)program
				FROM tb_user_program up
				JOIN tb_programs p ON up.programID = p.id
				JOIN tb_programtemplate pt ON p.programTempID = pt.id
				WHERE up.userID = '$id'
				AND up.isActive = 1
				AND p.isActive = 1
				";

		$sql .=" ORDER BY p.isActive DESC,title,batch";
		//echo $sql;
		return $this->db->query($sql);
	}	
	
	function getUserProgramRecords($userProgramID="",$class="",$filter="",$userID="")
	{
		$sql = "SELECT dt.id did,eventType,time,date,
						CASE remark
						WHEN 'Rejected' THEN 'Rejected'
						ELSE opportunityType
						END as status
				FROM tb_details dt
				JOIN tb_dates da ON dt.dateID = da.id
				JOIN tb_user_program up ON da.userProgramID = up.id
				JOIN tb_programs p ON up.programID = p.id
				WHERE dt.latest = 1 ";
		
		if($userID!="") {
			$sql .= "AND up.userID = '{$userID}' ";
		}
		
		if($userProgramID!="") $sql .= "AND up.id = '{$userProgramID}' ";
		
		if($class!="")
		{
			if(in_array($class, array('Won','Loss','Pending')))
			{
				$sql .=" AND opportunityType = '{$class}' ";
			}
			else if($class=="Rejected")
			{
				$sql .=" AND remark = 'Rejected' ";
			}
			else
			{
				$sql .=" AND eventType = '{$class}' ";
			}
		}
		if(is_array($filter))
		{
			$sql .=" AND DATE_FORMAT(dt.time,'%Y-%m-%d') >= '".date("Y-m-d",$filter['start'])."' ";
			$sql .=" AND DATE_FORMAT(dt.time,'%Y-%m-%d') <= '".date("Y-m-d",$filter['end'])."' ";
		}
		//echo $sql;	
		return $this->db->query($sql);
	}
}


?>