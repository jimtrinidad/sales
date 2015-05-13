<?php 
class Mdash extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	//get active programs
	function getPrograms()
	{
		$sql = "SELECT p.id id,title,logo,batch,details,target,dateStart,dateEnd,p.dateCreated dateCreated
				FROM tb_programs p
				JOIN tb_programtemplate pt ON p.programTempID = pt.id
				WHERE p.isActive = '1'";
		$sql .= " ORDER BY p.isActive DESC,pt.title,batch+0 ";
		return $this->db->query($sql)->result_array();
	}
	
	
	//dashboard record(won/loss/dates/etc...)
	function getRecords($filters = NULL)
	{
		$sql = "SELECT dt.id did,eventType,time,date,title,batch,logo ,companyName,i.lastname lastname,i.firstname firstname,mi,position,telephone,mobile,fax,i.email email,CONCAT(u.firstname,' ',u.lastname) user,
						CASE remark
						WHEN 'Rejected' THEN 'Rejected'
						ELSE opportunityType
						END as status
				FROM tb_details dt
				JOIN tb_information i ON dt.infoID = i.id
				JOIN tb_dates da ON dt.dateID = da.id
				JOIN tb_user_program up ON da.userProgramID = up.id
				JOIN tb_users u ON up.userID = u.id
				JOIN tb_programs p ON up.programID = p.id
				JOIN tb_programtemplate pt ON pt.id = p.programTempID
				WHERE dt.id IS NOT NULL AND dt.latest = 1 ";

			if(isset($filters['lastweeks']))
			{
				$sql .=" AND DATE_FORMAT(time,'%Y-%m-%d') < '".date("Y-m-d",$filters['lastweeks'])."' ";
			}
			elseif(isset($filters['weekfrom']))
			{
				//$sql .=" AND DATE_FORMAT(time,'%Y-%m-%d') >= '".date("Y-m-d",$filters['weekfrom'])."' ";//para idisplay nya ung overall data, including previous weeks
				//$sql .=" AND DATE_FORMAT(time,'%Y-%m-%d') <= '".date("Y-m-d",strtotime(date("Y-m-d",$filters['weekfrom'])."+ 4 days"))."' ";
				$sql .=" AND DATE_FORMAT(time,'%Y-%u') <= DATE_FORMAT('".date("Y-m-d",$filters['weekfrom'])."','%Y-%u') ";
			}
			elseif(isset($filters['day']))
			{
				$sql .=" AND DATE_FORMAT(time,'%Y-%m-%d') = '".date("Y-m-d",$filters['day'])."' ";
			}
			
			
			if(isset($filters['program']))
			{			
				$sql .=" AND p.id = '{$filters['program']}' ";
			}
			if(isset($filters['status']) && $filters['status']!="all" && $filters['status'] !="Rejected")
			{
				$sql .=" AND opportunityType = '{$filters['status']}' ";
			}
			elseif(isset($filters['status']) && $filters['status'] =="Rejected")
			{
				$sql .=" AND (opportunityType = '{$filters['status']}' OR remark = 'Rejected' ) ";
			}
			
			if(isset($filters['status']) && $filters['status']=="Pending")
			{
				$sql .= " ORDER BY cPercent+0 DESC,firstname ";
			}
			else 
			{
				$sql .=" ORDER BY firstname ";
			}
                    //echo $sql."<br>";
		return $this->db->query($sql)->result_array();
	}

	//get list of user contacts
	function getDashContacts($filters = "")
	{
		$sql = "";
		$sql .="SELECT i.id infoid,CONCAT(firstname,' ',lastname)name,email
				FROM tb_details d 
				JOIN tb_information i ON d.infoID = i.id 
				JOIN tb_dates dt ON dt.id = d.dateID
				JOIN tb_user_program up ON up.id = dt.userProgramID
				JOIN tb_programs p ON p.id = up.programID
				WHERE email NOT LIKE '' 
				AND d.latest = 1 ";
		
		if($filters!="")
		{
			if(isset($filters['lastweeks']))
			{
				$sql .=" AND DATE_FORMAT(time,'%Y-%m-%d') < '".date("Y-m-d",$filters['lastweeks'])."' ";
			}
			elseif(isset($filters['weekfrom']))
			{
				//$sql .=" AND DATE_FORMAT(time,'%Y-%m-%d') >= '".date("Y-m-d",$filters['weekfrom'])."' ";
				$sql .=" AND DATE_FORMAT(time,'%Y-%m-%d') <= '".date("Y-m-d",strtotime(date("Y-m-d",$filters['weekfrom'])."+ 4 days"))."' ";
			}
			elseif(isset($filters['day']))
			{
				$sql .=" AND DATE_FORMAT(time,'%Y-%m-%d') = '".date("Y-m-d",$filters['day'])."' ";
			}
			
			if(isset($filters['program']))
			{			
				$sql .=" AND p.id = '{$filters['program']}' ";
			}
			if(isset($filters['status']) && $filters['status']!="all" && $filters['status'] !="Rejected")
			{
				$sql .=" AND opportunityType = '{$filters['status']}' ";
			}
			elseif(isset($filters['status']) && $filters['status'] =="Rejected")
			{
				$sql .=" AND (opportunityType = '{$filters['status']}' OR remark = 'Rejected' ) ";
			}
			
			if(isset($filters['searchval']) && $filters['searchval']!="")
			{
				if($filters['searchkey']=='name')
				{
					$sql .=" AND (firstname LIKE '%{$filters['searchval']}%' OR lastname LIKE '%{$filters['searchval']}%') ";
				}
				else
				{
					$sql .=" AND {$filters['searchkey']} LIKE '%{$filters['searchval']}%' ";
				}
			}
			if(isset($filters['infoids']) && is_array($filters['infoids']))
			{
				$sql .=" AND i.id NOT IN ('".implode("','", $filters['infoids'])."') ";
			}
		}

		$sql .=" GROUP BY i.id ";
		
		if(isset($filters['status']) && $filters['status']=="Pending")
		{
			$sql .= " ORDER BY cPercent+0 DESC,firstname ";
		}
		else 
		{
			$sql .=" ORDER BY firstname ";
		}
		
		return $this->db->query($sql)->result_array();
	}	
	
	function getProgramWon($programid)
	{
		$sql = "SELECT dt.id did
				FROM tb_details dt
				JOIN tb_dates da ON dt.dateID = da.id
				JOIN tb_user_program up ON da.userProgramID = up.id
				JOIN tb_programs p ON up.programID = p.id
				WHERE dt.id IS NOT NULL 
				AND dt.latest = 1 
				AND opportunityType = 'Won'
				AND p.id = {$programid} ";
		
		return $this->db->query($sql);
	}
	
	function getProgramUsers($programID)
	{
		$sql = "SELECT u.id userID,firstname,lastname,photo
				FROM tb_user_program up
				JOIN tb_users u ON u.id = up.userID
				WHERE up.isActive = 1
				AND u.isActive = 1
				AND up.programID = '{$programID}'
				";
		return $this->db->query($sql);
	}
}
?>