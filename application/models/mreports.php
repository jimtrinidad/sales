<?php 
class Mreports extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function getUsersExcept($id = array())
	{
		$sql = "SELECT u.id id,CONCAT(firstname,' ',lastname) name,firstname,lastname,photo,u.isActive active ,dateAdded
				FROM tb_users u
				JOIN tb_privilege p ON u.p_id = p.id
				JOIN tb_user_program up ON up.userID = u.id
				WHERE isAdmin = 0 
				AND u.isActive = '1' ";
			
			if(count($id) >= 1)
			{
				$sql.= " AND u.id NOT IN (" . implode(", ", $id) . ") ";
			}

		$sql .=" GROUP BY u.id ";
		return $this->db->query($sql)->result_array();
	}
	
	function getUsers($id = "",$isActive = TRUE)
	{
		$sql = "SELECT u.id id,CONCAT(firstname,' ',lastname) name,firstname,lastname,photo,u.isActive active ,dateAdded
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
				return $this->db->query($sql)->row();
			}
			else 
			{
				$sql .=" GROUP BY u.id ";
				return $this->db->query($sql)->result_array();				
			}
	}
	
	function getPrograms($id = "",$status="", $program_template_id = "")
	{
		$sql = "SELECT p.id pid,title,logo,batch,dateStart,dateEnd,p.isActive pActive
				FROM tb_programs p
				JOIN tb_programtemplate pt ON pt.id = p.programTempID 
				WHERE p.id IS NOT NULL ";
		switch ($status)
		{
			case "active": $sql .= " AND p.isActive = '1' ";break;
			case "inactive": $sql .=" AND p.isActive = '0' ";break;
			case "both": $sql .= "";break;
			default: $sql .= " AND p.isActive = '1' ";break;
		}
		
		
		if($id != "")
		{
			$sql.= " AND p.id = {$id} ";
			return $this->db->query($sql)->row();
		}
		elseif($program_template_id != "")
		{
			$sql.= " AND pt.id = {$program_template_id} ";
			$sql .= " ORDER BY batch+0 ";
			return $this->db->query($sql)->result_array();	
		}
		else 
		{
			$sql .= " ORDER BY p.isActive DESC,pt.title,batch+0 ";
			return $this->db->query($sql)->result_array();				
		}
				

	}
	
	//get specific information of the event.. contact/company/ etc... 
	function getDetails($id)
	{
		$sql="";
		$sql .="SELECT d.id did,i.id infoid,eventType,time,remark,opportunityType,cPercent,refferal,note,companyName,i.lastname lastname,i.firstname firstname,mi,position,telephone,mobile,fax,i.email email 
				 ,u.lastname ulname,u.firstname ufname,CONCAT(title,' ',batch)program ";
		$sql .="FROM tb_details d
				JOIN tb_information i ON d.infoID = i.id
				JOIN tb_dates dt ON dt.id = d.dateID
				JOIN tb_user_program up ON up.id = dt.userProgramID 
				JOIN tb_programs p ON p.id = up.programID
				JOIN tb_programtemplate pt ON pt.id = p.programTempID
				JOIN tb_users u ON u.id = up.userID
				WHERE d.id = '$id' ";
		
		return $this->db->query($sql)->row_array();
	}	
	
	function searchResult($limit="",$offset="",$filters,$excel = FALSE)
	{
		$sql = "";

		if(!$excel)
		{
			$sql .="SELECT d.id did,eventType,time,remark,opportunityType,note,cPercent,refferal,pt.title program,batch,
					companyName,i.lastname lastname,i.firstname firstname,mi,position,telephone,mobile,fax,i.email email,CONCAT(u.firstname,' ',u.lastname) user  ";
		}
		else 
		{
			$sql .=" SELECT CONCAT(i.firstname,' ',i.lastname)name,pt.title program,batch,position,companyName,telephone,mobile,fax,i.email email,eventType,
						DATE_FORMAT(time,'%M %d, %Y')date,DATE_FORMAT(time,'%l:%i:%s %p')time,
						CASE remark
						WHEN 'Rejected' THEN 'Rejected'
						ELSE opportunityType
						END as result,note,refferal,CONCAT(u.firstname,' ',u.lastname) user ";
		}
		
		$sql .="FROM tb_details d 
				JOIN tb_information i ON d.infoID = i.id
				JOIN tb_dates dt ON dt.id = d.dateID
				JOIN tb_user_program up ON up.id = dt.userProgramID 
				JOIN tb_users u ON up.userID = u.id
				JOIN tb_programs p ON p.id = up.programID 
				JOIN tb_programtemplate pt ON p.programTempID = pt.id
				WHERE d.id IS NOT NULL ";
		
		switch ($filters['programType'])
		{
			case "active": $sql .=" AND p.isActive = '1' ";break;
			case "inactive": $sql .=" AND p.isActive = '0' ";break;
			case "both":break;
		}
		
		if($filters['user']!="")
		{
			$sql .=" AND up.userID = '{$filters['user']}' ";
		}
		if($filters['program']!="all")
		{
			$sql .=" AND p.id = '{$filters['program']}' ";
		}
		if($filters['etype']!="")
		{
			$sql .=" AND d.eventType = '{$filters['etype']}' ";
		}		
		if($filters['remark']!="")
		{
			$sql .=" AND d.remark = '{$filters['remark']}' ";
		}
		if($filters['statusR']!="")
		{
			$sql .=" AND d.opportunityType = '{$filters['statusR']}' ";
		}
		if($filters['date']!="")
		{
			$sql .=" AND DATE_FORMAT(d.time,'%Y-%m-%d') = '".date("Y-m-d",strtotime($filters['date']))."' ";
		}

		if($filters['qsearchval']!="")
		{
			if($filters['qsearchkey']=='name')
			{
				$sql .=" AND (i.firstname LIKE '%{$filters['qsearchval']}%' 
								OR i.lastname LIKE '%{$filters['qsearchval']}%'
								OR CONCAT( i.firstname, ' ', i.lastname ) LIKE '%{$filters['qsearchval']}%'
						) ";
			}
			else
			{
				$sql .=" AND i.{$filters['qsearchkey']} LIKE '%{$filters['qsearchval']}%' ";
			}
		}		

		if($filters['latest']!=1)
		{
			$sql .= " AND d.latest = '1' ";
		}
		
		if($filters['orderby']!="")
		{
			$sql.=" ORDER BY {$filters['orderby']} {$filters['ordertype']},i.lastname {$filters['ordertype']},i.firstname {$filters['ordertype']} ";
		}
		else 
		{
			$sql .=" ORDER BY d.time DESC ";
		}

		if($limit!="")
		{
			$sql.="LIMIT $offset,$limit";
		}
		//echo $sql."<br>";
		return $this->db->query($sql);
		
	}

	//for graphs
	function getTotals($type = '',$status,$active = TRUE,$filters = array())
	{
		$sql = "SELECT * FROM tb_details d
				JOIN tb_dates dt ON d.dateID = dt.id
				JOIN tb_user_program up ON up.id = dt.userProgramID
				JOIN tb_programs p ON up.programID = p.id
				JOIN tb_programtemplate pt ON p.programTempID = pt.id
				WHERE d.id IS NOT NULL ";
		
		if($active)
		{
			$sql .= " AND p.isActive = '1' ";
		}
		
		if($type!='')
		{
			$sql .= " AND d.eventType = '{$type}' ";
		}
		
		if($status!="Rejected")
		{
			$sql .=" AND opportunityType = '{$status}' ";
		}
		else 
		{
			$sql .= " AND remark = 'Rejected' ";
		}
		
		if(isset($filters['user']) && $filters['user']!="all")
		{
			$sql .=" AND up.userID = '{$filters['user']}' ";
		}
		
		$sql .= " AND d.latest = 1 ";

		//echo $sql."<br>";
		$q = $this->db->query($sql);
		if($q->num_rows()>0)
		{
			return $q->num_rows();	
		}
		else
		{
			return 0;
		}
	}
	/*
	//for graph summary
	function getSummary($key="",$value="",$user="all",$program="all",$date="",$class="")
	{
		$sql = "SELECT * FROM tb_details d
				JOIN tb_dates dt ON d.dateID = dt.id
				JOIN tb_user_program up ON up.id = dt.userProgramID
				JOIN tb_programs p ON up.programID = p.id
				JOIN tb_programtemplate pt ON p.programTempID = pt.id
				WHERE p.isActive = '1' ";
		
		if($key!="" && $value!="")
		{
			$sql .=" AND {$key} LIKE '{$value}' ";
		}
		
		if($user!="all")
		{
			$sql .=" AND up.userID = '{$user}' ";
		}
		if($program!="all")
		{
			$sql .=" AND up.programID = '{$program}' ";
		}
		
		if($date!="")
		{
			$sql .=" AND DATE_FORMAT(d.time,'%Y-%m-%d') = '".date("Y-m-d",$date)."' ";
		}
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

		$sql .= " AND d.latest = 1 ";

		//echo $sql."<br>";
		$q = $this->db->query($sql);
		if($q->num_rows()>0)
		{
			return $q->num_rows();	
		}
		else
		{
			return 0;
		}		
	}
	*/
	function getStartEndDate($what,$active = TRUE,$filters = array())
	{
		$sql = "SELECT DATE_FORMAT(time,'%Y-%m-%d')time FROM tb_details d
				JOIN tb_dates dt ON d.dateID = dt.id
				JOIN tb_user_program up ON up.id = dt.userProgramID
				JOIN tb_programs p ON up.programID = p.id
				JOIN tb_programtemplate pt ON p.programTempID = pt.id
				WHERE d.id IS NOT NULL ";
		
		if($active)
		{
			$sql .= " AND p.isActive = '1' ";
		}
		
		if(isset($filters['user']) && $filters['user']!="all")
		{
			$sql .=" AND up.userID = '{$filters['user']}' ";
		}			
		
		if($what == "Start")
			$sql .= " ORDER BY d.id ";
		elseif($what == "End")
			$sql .= " ORDER BY d.id DESC";
		
		$sql .=" LIMIT 0,1 ";
		
		return $this->db->query($sql)->row()->time;
		
	}
	
	function monthly($date,$class,$active = TRUE,$filters = array())
	{
		$sql = "SELECT * FROM tb_details d
				JOIN tb_dates dt ON d.dateID = dt.id
				JOIN tb_user_program up ON up.id = dt.userProgramID
				JOIN tb_users u ON u.id = up.userID
				JOIN tb_programs p ON up.programID = p.id
				JOIN tb_programtemplate pt ON p.programTempID = pt.id
				WHERE d.id IS NOT NULL ";
		
		if($active)
		{
			$sql .= " AND p.isActive = '1' ";
		}

		if(isset($filters['user']) && $filters['user']!="all")
		{
			$sql .=" AND up.userID = '{$filters['user']}' ";
		}		
		
		if($date!="")
		{
			$sql .=" AND DATE_FORMAT(d.time,'%Y-%m-%d') = '".date("Y-m-d",$date)."' ";
		}
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
		
		if(isset($filters['activeUser'])){$sql.=" AND u.isActive = 1 ";}
		
		$sql .= " AND d.latest = 1 ";

		//echo $sql."<br>";
		$q = $this->db->query($sql);
		if($q->num_rows()>0)
		{
			return $q->num_rows();	
		}
		else
		{
			return 0;
		}		
	}
	
	/*
	 * FOR USER RANKINGS
	 */
	//get specific program of user
	function getUserProgram($id)//return group by program template
	{
		$sql = "";
		$sql .="SELECT pt.id pid,title,batch,pointReference FROM tb_programtemplate pt
				JOIN tb_programs p ON pt.id = p.programTempID
				JOIN tb_user_program up ON p.id = up.programID 
				WHERE up.userID = '$id'
				GROUP BY pt.id 
				ORDER BY dateStart DESC";
		return $this->db->query($sql)->result_array();
	}
	
	//get specific program of user
	function getUserBonusPoints($id,$filters="")//return user bonus points
	{
		$sql = "";
		$sql .="SELECT ub.id bid,user_id,bonusType,points FROM tb_user_bonus ub
				JOIN tb_users u ON u.id = ub.user_id
				WHERE ub.user_id = {$id} ";
		
		if(is_array($filters))
		{
			$sql .=" AND DATE_FORMAT(ub.dateAdded,'%Y-%m-%d') >= '".date("Y-m-d",$filters['start'])."' ";
			$sql .=" AND DATE_FORMAT(ub.dateAdded,'%Y-%m-%d') <= '".date("Y-m-d",$filters['end'])."' ";
		}	
		
		return $q = $this->db->query($sql)->result_array();
		
	}

	function updateBonusPoints($data) //d2 ako huminto kahapon, i add kpag di pa existing.. edit pag meron na..
	{
		$sql = " SELECT * FROM tb_user_bonus WHERE user_id = {$data['user_id']} AND bonusType = '{$data['bonusType']}'";
		$q = $this->db->query($sql);
		if($q->num_rows()==1)
		{
			$this->db->where('id',$q->row()->id);
			$this->db->set('points',$data['points'],FALSE);
			$this->db->update('tb_user_bonus');
		}
		elseif($data['points']!=0) 
		{
			$this->db->insert('tb_user_bonus',$data);
		}
		
	}
	
	function getWonPerUser($programid,$userid,$filters = "")//filtered by programtemplate id and user id
	{
		$sql = "SELECT dt.id did,eventType,time,date,companyName,lastname,firstname,mi,position,telephone,mobile,fax,email,CONCAT(title,' ',batch) program,
						CASE remark
						WHEN 'Rejected' THEN 'Rejected'
						ELSE opportunityType
						END as status
				FROM tb_details dt
				JOIN tb_information i ON dt.infoID = i.id
				JOIN tb_dates da ON dt.dateID = da.id
				JOIN tb_user_program up ON da.userProgramID = up.id
				JOIN tb_programs p ON up.programID = p.id
				JOIN tb_programtemplate pt ON pt.id = p.programTempID
				WHERE dt.id IS NOT NULL 
				AND dt.latest = 1 
				AND opportunityType = 'Won'
				AND pt.id = {$programid}
				AND up.userID = {$userid} ";
		
		if(is_array($filters))
		{
			$sql .=" AND DATE_FORMAT(dt.time,'%Y-%m-%d') >= '".date("Y-m-d",$filters['start'])."' ";
			$sql .=" AND DATE_FORMAT(dt.time,'%Y-%m-%d') <= '".date("Y-m-d",$filters['end'])."' ";
		}
		
		//echo $sql."<br>";
		return $this->db->query($sql)->result_array();
	}

	function getPendingPerUser($userid,$time,$type)//filtered by programtemplate id and user id
	{
		$sql = "SELECT dt.id did,eventType,time,date,companyName,lastname,firstname,mi,position,telephone,mobile,fax,email,CONCAT(title,' ',batch) program,
						CASE remark
						WHEN 'Rejected' THEN 'Rejected'
						ELSE opportunityType
						END as status
				FROM tb_details dt
				JOIN tb_information i ON dt.infoID = i.id
				JOIN tb_dates da ON dt.dateID = da.id
				JOIN tb_user_program up ON da.userProgramID = up.id
				JOIN tb_programs p ON up.programID = p.id
				JOIN tb_programtemplate pt ON pt.id = p.programTempID
				WHERE dt.id IS NOT NULL 
				AND dt.latest = 1 
				AND opportunityType = 'Pending'
				AND up.userID = {$userid} ";
		
		switch($time)
		{
			case 'Weekly':
				switch($type)
				{
					case 'previous':
						$sql .=" AND DATE_FORMAT(dt.time,'%Y-%u') = DATE_FORMAT(DATE_SUB('".date("Y-m-d",strtotime(NOW))."',INTERVAL 1 WEEK),'%Y-%u') ";
						break;
					case 'current':
						$sql .=" AND DATE_FORMAT(dt.time,'%Y-%u') = DATE_FORMAT('".NOW."','%Y-%u') ";
						break;
				}
				
				break;
			case 'Monthly':
				switch($type)
				{
					case 'previous':
						$sql .=" AND DATE_FORMAT(dt.time,'%Y-%m') = DATE_FORMAT(DATE_SUB('".date("Y-m-d",strtotime(NOW))."',INTERVAL 1 MONTH),'%Y-%m') ";
						break;
					case 'current':
						$sql .=" AND DATE_FORMAT(dt.time,'%Y-%m') = DATE_FORMAT('".NOW."','%Y-%m') ";
						break;
				}
				break;
		}
		
		//echo $sql."<br>";
		return $this->db->query($sql);
	}

	function getLatestWonsPerUser($userid,$type,$dates = array())//filtered by programtemplate id and user id
	{
		$sql = "SELECT dt.id did,eventType,time,date,companyName,lastname,firstname,mi,position,telephone,mobile,fax,email,CONCAT(title,' ',batch) program,
						CASE remark
						WHEN 'Rejected' THEN 'Rejected'
						ELSE opportunityType
						END as status
				FROM tb_details dt
				JOIN tb_information i ON dt.infoID = i.id
				JOIN tb_dates da ON dt.dateID = da.id
				JOIN tb_user_program up ON da.userProgramID = up.id
				JOIN tb_programs p ON up.programID = p.id
				JOIN tb_programtemplate pt ON pt.id = p.programTempID
				WHERE dt.id IS NOT NULL 
				AND dt.latest = 1 
				AND opportunityType = 'Won'
				AND up.userID = {$userid} ";
		

			switch($type)
			{
				case 'today':
					$sql .=" AND DATE_FORMAT(dt.time,'%Y-%m-%d') = DATE_FORMAT('".date("Y-m-d",strtotime(NOW))."','%Y-%m-%d') ";
					break;
				case 'yesterday':
					$sql .=" AND DATE_FORMAT(dt.time,'%Y-%m-%d') = DATE_FORMAT(DATE_SUB('".date("Y-m-d",strtotime(NOW))."',INTERVAL 1 DAY),'%Y-%m-%d') ";
					break;
				case 'all':
					$sql .=" AND DATE_FORMAT(dt.time,'%Y-%m-%d') IN ('". implode("','", $dates) ."')";
					break;
			}
		
		//echo $sql."<br>";
		return $this->db->query($sql);
	}
	
	function getWeeklyWon($type,$prevCount = 0,$program_status = 'active', $start_date = NOW)//filtered by programtemplate id and user id
	{
		$condition = "";
		switch($type)
		{
			case 'current':
				$condition .=" AND DATE_FORMAT(dt.time,'%Y-%u') = DATE_FORMAT('".date("Y-m-d",strtotime(NOW))."','%Y-%u') ";
				break;
			case 'previous':
				$condition .=" AND DATE_FORMAT(dt.time,'%Y-%u') = DATE_FORMAT(DATE_SUB('".date("Y-m-d",strtotime($start_date))."',INTERVAL {$prevCount} WEEK),'%Y-%u') ";
				break;
			case 'program_template':
				$condition .=" AND pt.id = {$prevCount} "; // $prevCount as program template id
					switch ($program_status)
					{
						case "active": $condition .=" AND p.isActive = '1' ";break;
						case "inactive": $condition .=" AND p.isActive = '0' ";break;
						case "both":break;
					}
				break;
			case 'program':
				$condition .=" AND p.id = {$prevCount} "; // $prevCount as program id
				break;
		}
		
		$sql = "SELECT wons, points
					FROM (
						
						SELECT SUM( wons ) AS wons, SUM( points ) AS points
						FROM (
						
							SELECT CONCAT( title,  ' ', batch ) program, pointReference, COUNT( * ) AS wons, (COUNT( * ) * pointReference) AS points
							FROM tb_details dt
							JOIN tb_dates da ON dt.dateID = da.id
							JOIN tb_user_program up ON da.userProgramID = up.id
							JOIN tb_programs p ON up.programID = p.id
							JOIN tb_programtemplate pt ON pt.id = p.programTempID
							JOIN tb_users u ON u.id = up.userID
							WHERE dt.id IS NOT NULL 
							AND dt.latest =1
							AND opportunityType =  'Won'
							{$condition}
							GROUP BY up.id
						) AS TEMP
					) AS Unranked ";
		
		//echo $sql."<br>";
		return $this->db->query($sql);
	}
	
	function getGroupWon($time_period,$prevCount = 0,$group_type = 'WEEK', $format = '%Y-%u')
	{
		$condition = "";
		
		switch($time_period)
		{
			case 'current':
				$condition .=" AND DATE_FORMAT(dt.time,'{$format}') = DATE_FORMAT('".date("Y-m-d",strtotime(NOW))."','{$format}') ";
				break;
			case 'previous':
				$condition .=" AND DATE_FORMAT(dt.time,'{$format}') = DATE_FORMAT(DATE_SUB('".date("Y-m-d",strtotime(NOW))."',INTERVAL {$prevCount} {$group_type}),'{$format}') ";
				break;
		}
		
		$sql = "SELECT wons, points
					FROM (
						
						SELECT SUM( wons ) AS wons, SUM( points ) AS points
						FROM (
						
							SELECT CONCAT( title,  ' ', batch ) program, pointReference, COUNT( * ) AS wons, (COUNT( * ) * pointReference) AS points
							FROM tb_details dt
							JOIN tb_dates da ON dt.dateID = da.id
							JOIN tb_user_program up ON da.userProgramID = up.id
							JOIN tb_programs p ON up.programID = p.id
							JOIN tb_programtemplate pt ON pt.id = p.programTempID
							JOIN tb_users u ON u.id = up.userID
							WHERE dt.id IS NOT NULL 
							AND dt.latest =1
							AND opportunityType =  'Won'
							{$condition}
							GROUP BY up.id
						) AS TEMP
					) AS Unranked ";
		
		//echo $sql."<br>";
		return $this->db->query($sql);
	}
        
        function getSalesYear(){
            $sql = "SELECT YEAR(TIME) as year FROM tb_details
                    GROUP BY YEAR(TIME)";
            return $this->db->query($sql)->result_array();
        }
        
        function getGroupWonYTD($start,$end)
	{
		$condition = "";
		
		$condition .=" AND DATE_FORMAT(dt.time,'%Y-%m-%d') >= '".$start."' ";
		$condition .=" AND DATE_FORMAT(dt.time,'%Y-%m-%d') <= '".$end."' ";
		
		$sql = "SELECT wons, points
					FROM (
						
						SELECT SUM( wons ) AS wons, SUM( points ) AS points
						FROM (
						
							SELECT CONCAT( title,  ' ', batch ) program, pointReference, COUNT( * ) AS wons, (COUNT( * ) * pointReference) AS points
							FROM tb_details dt
							JOIN tb_dates da ON dt.dateID = da.id
							JOIN tb_user_program up ON da.userProgramID = up.id
							JOIN tb_programs p ON up.programID = p.id
							JOIN tb_programtemplate pt ON pt.id = p.programTempID
							JOIN tb_users u ON u.id = up.userID
							WHERE dt.id IS NOT NULL 
							AND dt.latest =1
							AND opportunityType =  'Won'
							{$condition}
							GROUP BY up.id
						) AS TEMP
					) AS Unranked ";
		
		//echo $sql."<br>";
		return $this->db->query($sql);
	}

	function won_ranking($userid = '',$filters = '',$carrer = FALSE)
	{
		$condition = "";
		if(is_array($filters))
		{
			$condition .=" AND DATE_FORMAT(dt.time,'%Y-%m-%d') >= '".date("Y-m-d",$filters['start'])."' ";
			$condition .=" AND DATE_FORMAT(dt.time,'%Y-%m-%d') <= '".date("Y-m-d",$filters['end'])."' ";
		}	
		$sql = "SELECT rank, id, firstname, points
				FROM (
					
					SELECT @rownum := @rownum +1 AS rank, id, firstname, points
					FROM (
					
						SELECT @rownum :=0)r, (
						
						SELECT id, firstname, SUM( points ) AS points
						FROM (
						
							SELECT CONCAT( title,  ' ', batch ) program, pointReference, COUNT( * ) AS wons, (COUNT( * ) * pointReference) AS points, u.firstname, u.id
							FROM tb_details dt
							JOIN tb_dates da ON dt.dateID = da.id
							JOIN tb_user_program up ON da.userProgramID = up.id
							JOIN tb_programs p ON up.programID = p.id
							JOIN tb_programtemplate pt ON pt.id = p.programTempID
							JOIN tb_users u ON u.id = up.userID
							WHERE dt.id IS NOT NULL 
							AND dt.latest =1
							AND opportunityType =  'Won'
							{$condition}
							GROUP BY up.id
						) AS TEMP
						
						GROUP BY id
						ORDER BY points DESC
					) AS Unranked
				) AS Ranked ";
							
		if($carrer)
		{
			$sql = "SELECT rank, id, firstname, points, bonus, totalPoints
					FROM (
						SELECT @rownum := @rownum +1 AS rank, id, firstname, points, bonus, totalPoints
						FROM (
						
							SELECT @rownum :=0)r, (
						
							SELECT TEMP2.id AS id, firstname, TEMP2.points,IFNULL(SUM(ub.points),0) AS bonus,(TEMP2.points + IFNULL(SUM(ub.points),0)) AS totalPoints
							FROM (
								
								SELECT id, firstname, SUM( points ) AS points
								FROM (
								
									SELECT CONCAT( title,  ' ', batch ) program, pointReference, COUNT( * ) AS wons, (COUNT( * ) * pointReference) AS points, u.firstname, u.id
									FROM tb_details dt
									JOIN tb_dates da ON dt.dateID = da.id
									JOIN tb_user_program up ON da.userProgramID = up.id
									JOIN tb_programs p ON up.programID = p.id
									JOIN tb_programtemplate pt ON pt.id = p.programTempID
									JOIN tb_users u ON u.id = up.userID
									WHERE dt.id IS NOT NULL 
									AND dt.latest =1
									AND opportunityType =  'Won'									
									GROUP BY up.id
								) AS TEMP								
								GROUP BY id						
							) AS TEMP2
							LEFT OUTER JOIN tb_user_bonus ub ON ub.user_id = TEMP2.id
							GROUP BY TEMP2.id 
							ORDER BY totalPoints DESC
						) AS Unranked 
					) AS Ranked ";
		}
							
		if($userid != '') $sql .= " WHERE id = '{$userid}' ";
		
		return $this->db->query($sql);
	}
	
	function pending_ranking($userid = '',$filters = '')
	{
		$condition = "";
		if(is_array($filters))
		{
			$condition .=" AND DATE_FORMAT(dt.time,'%Y-%m-%d') >= '".date("Y-m-d",$filters['start'])."' ";
			$condition .=" AND DATE_FORMAT(dt.time,'%Y-%m-%d') <= '".date("Y-m-d",$filters['end'])."' ";
		}	
		$sql = "SELECT rank, id, firstname, pendings
				FROM (
					
					SELECT @rownum := @rownum +1 AS rank, id, firstname, pendings
					FROM (
						
						SELECT @rownum :=0
						)r, (
						
						SELECT COUNT( * ) AS pendings, u.firstname, u.id
						FROM tb_details dt
						JOIN tb_information i ON dt.infoID = i.id
						JOIN tb_dates da ON dt.dateID = da.id
						JOIN tb_user_program up ON da.userProgramID = up.id
						JOIN tb_programs p ON up.programID = p.id
						JOIN tb_programtemplate pt ON pt.id = p.programTempID
						JOIN tb_users u ON u.id = up.userID
						WHERE dt.id IS NOT NULL 
						AND dt.latest =1
						AND opportunityType =  'Pending'
						{$condition}
						GROUP BY u.id
						ORDER BY pendings DESC
					) AS Unranked
				) AS Ranked ";
							
		if($userid != '') $sql .= " WHERE id = '{$userid}' ";
		
		return $this->db->query($sql);
	}

	function getResultSummary($limit="",$offset="",$filters, $group = TRUE)
	{
		$sql ="SELECT d.id did,eventType,time,remark,opportunityType,note,cPercent,refferal,pt.title program,batch,
					companyName,i.lastname lastname,i.firstname firstname,CONCAT(i.firstname,' ',i.lastname) name,mi,position,telephone,mobile,fax,i.email email,CONCAT(u.firstname,' ',u.lastname) user  ";

		if($group) $sql .= ",COUNT( * ) AS total ";
		
		$sql .="FROM tb_details d 
				JOIN tb_information i ON d.infoID = i.id
				JOIN tb_dates dt ON dt.id = d.dateID
				JOIN tb_user_program up ON up.id = dt.userProgramID 
				JOIN tb_users u ON up.userID = u.id
				JOIN tb_programs p ON p.id = up.programID 
				JOIN tb_programtemplate pt ON p.programTempID = pt.id
				WHERE d.id IS NOT NULL ";
		
		if($filters['user']!="")
		{
			$sql .=" AND up.userID = '{$filters['user']}' ";
		}
	
		if($filters['remark']!="")
		{
			$sql .=" AND d.remark = '{$filters['remark']}' ";
		}
		if($filters['statusR']!="")
		{
			$sql .=" AND d.opportunityType = '{$filters['statusR']}' ";
		}else $sql .=" AND d.opportunityType = 'Won' ";


		if($filters['qsearchval']!="")
		{
			if($filters['qsearchkey']=='name')
			{
				$sql .=" AND CONCAT( i.firstname,' ', i.lastname ) LIKE '%{$filters['qsearchval']}%' ";
			}
			elseif($filters['qsearchkey']=='companyName')
			{
				$str_arr = explode(' ', $filters['qsearchval']);
				$new_str = ( count($str_arr)>2 ) ? $str_arr[0].' '.$str_arr[1] : $filters['qsearchval'];
				
				if(!empty($new_str))
				{
					$sql .=" AND companyName LIKE '%{$new_str}%' ";
				}
				else{
					$sql .=" AND companyName = '{$new_str}' ";
				}
			}
			else
			{
				$sql .=" AND i.{$filters['qsearchkey']} LIKE '%{$filters['qsearchval']}%' ";
			}
		}
		
		if( !$group ){
			if($filters['qsearchkey']=='name')
			{
				$sql .=" AND CONCAT( i.firstname,' ', i.lastname ) = '{$filters['qsearchval']}' ";
			}
			elseif($filters['qsearchkey']=='companyName')
			{
				$str_arr = explode(' ', $filters['qsearchval']);
				$new_str = ( count($str_arr)>2 ) ? $str_arr[0].' '.$str_arr[1] : $filters['qsearchval'];
				
				if(!empty($new_str))
				{
					$sql .=" AND companyName LIKE '{$new_str}%' ";
				}
				else{
					$sql .=" AND companyName = '{$new_str}' ";
				}
			}
			else
			{
				$sql .=" AND i.{$filters['qsearchkey']} = '{$filters['qsearchval']}' ";
			}
		}
		
		$sql .= " AND d.latest = 1 ";

		if($group)
		{
			switch ($filters['qsearchkey']) {
				case 'companyName':
					$sql .=" GROUP BY SUBSTRING_INDEX( companyName, ' ', 2 ) ";
				break;
				case 'email':
					$sql .=" GROUP BY i.email ";
				break;			
				default:
					$sql .=" GROUP BY CONCAT(i.firstname,' ',i.lastname) ";
				break;
			}
		}

		
		if($group)
		{
			switch ($filters['orderby'])
			{
				case 'key':
						if($filters['qsearchkey']=='name'){
							 $sql.=" ORDER BY i.lastname {$filters['ordertype']},i.firstname {$filters['ordertype']}  ";
						}
						else $sql.=" ORDER BY {$filters['qsearchkey']} {$filters['ordertype']} ";
					break;
				default: if($group) $sql .=" ORDER BY total  {$filters['ordertype']}  ";
					break;
			}
		}
	
		
		if($limit!="")
		{
			$sql.="LIMIT $offset,$limit";
		}
		
		//echo $sql;
		return $this->db->query($sql);
	}
        
        function getUsersDeletedRecords()
        {
            $users = $this->getUsers('', true);
            $data = array();
            foreach($users as $user){
                $sql = "SELECT db.*,pt.name,pt.title,p.batch FROM tb_deletedbackup db
                        JOIN tb_user_program up ON up.id = db.userprogid
                        JOIN tb_programs p ON p.id = up.programID
                        JOIN tb_programtemplate pt ON pt.id = p.programTempID
                        WHERE deleted_by = {$user['id']}
                        ORDER BY db.date_deleted DESC";
		$q = $this->db->query($sql);
                $user['records'] = $q->result_array();
                $data[] = $user;
            }
            
            return $data;
        }
}
?>