<?php 
class Mprograms extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	//get specific program of user
	function getUserProgram($id)
	{
		$sql = "";
		$sql .="SELECT * FROM tb_programs p 
				JOIN tb_programtemplate pt ON pt.id = p.programTempID
				JOIN tb_user_program up ON p.id = up.programID 
				WHERE up.userID = '$id'
				AND up.isActive = '1'
				AND p.isActive = '1' 
				ORDER BY dateStart DESC";
		return $this->db->query($sql)->result_array();
	}
	
	//get dates of program of user
	function getDates($upID,$limit="",$offset="")
	{
		$sql = "";
		$sql .="SELECT d.id did,date,up.id upid 
				FROM tb_dates d 
				JOIN tb_user_program up ON d.userProgramID = up.id
				WHERE up.id = '$upID'
				ORDER BY date DESC ";
		
		if($limit!="")
		{
			$sql .=" LIMIT $offset,$limit ";
		}

		return $this->db->query($sql);
	}
	
	//check up userprogram id exist
	function checkUP($id,$uid)
	{
		$q = "SELECT id FROM tb_user_program
				WHERE id = '$id'
				AND userID = '$uid'";
		if ($this->db->query($q)->num_rows()>0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	
	//today... get the day today or add if not yet exist...
	function today($upID,$today="")
	{
		if($today=="")
		{
			$today = date("Y-m-d",strtotime(NOW));
			$this->db->where('date',$today);
		}
		else 
		{
			$this->db->where('id',$today);
		}
		$this->db->where('userProgramID',$upID);
		$q = $this->db->get('tb_dates');
		if($q->num_rows()>0)
		{
			return $q->row()->id;
		}
		else 
		{
			$this->db->where('id',$upID);
			$q = $this->db->get('tb_user_program');
			if($q->num_rows()>0)
			{
				if(date("N",strtotime($today))!=6 && date("N",strtotime($today))!=7)
				{
					$this->db->set('date',$today);
					$this->db->set('userProgramID',$upID);
					$this->db->insert('tb_dates');
					return $this->db->insert_id();
				}
				else 
				{
					$sql = "SELECT * FROM tb_dates WHERE userProgramID = '{$upID}' ORDER BY date DESC ";
					$q = $this->db->query($sql);
					if($q->num_rows()>0)
					{
						return $q->first_row()->id;
					}
				}
			}
		}
	}
	//get all the information of the event.. contact/company/ etc...
	function details($id,$limit="",$offset="",$latest="",$filters="",$userprogID="")//id = dateid
	{
		$sql="";
		$sql .="SELECT d.id did,eventType,time,remark,opportunityType,note,old,latest,companyName,lastname,firstname,mi,position,telephone,mobile,fax,email ";
		$sql .="FROM tb_details d
				JOIN tb_information i ON d.infoID = i.id
				JOIN tb_dates dt ON d.dateID = dt.id
				WHERE ";
		if($userprogID=="")
		{
			$sql .=" d.dateID = '$id' ";
		}
		else 
		{
			$sql .=" dt.userProgramID = '$userprogID' ";
		}
		if($latest!="")
		{
			$sql .=" AND latest = '{$latest}' ";
		}
		
		if($filters!="")
		{
			if($filters['searchvalD']!="")
			{
				$sql .=" AND {$filters['searchkeyD']} LIKE '{$filters['searchvalD']}%' ";
			}
			if($filters['etypeD']!="")
			{
				$sql .=" AND d.eventType = '{$filters['etypeD']}' ";
			}		
			if($filters['remarkD']!="")
			{
				$sql .=" AND d.remark = '{$filters['remarkD']}' ";
			}
			if($filters['statusD']!="")
			{
				$sql .=" AND d.opportunityType = '{$filters['statusD']}' ";
			}
		}
		
		$sql .=" ORDER BY time desc ";
		if($limit!="")
		{
			$sql .=" LIMIT $offset,$limit ";
		}
		
		return $this->db->query($sql)->result_array();
	}
	

	//for user program summary... 
	function userProgramSummary($id,$latest="")
	{
		$sql="";
		$sql .="SELECT d.id did,eventType,time,remark,opportunityType,note,old,latest ";
		$sql .="FROM tb_details d
				JOIN tb_information i ON d.infoID = i.id
				JOIN tb_dates dt ON dt.id = d.dateID
				JOIN tb_user_program up ON up.id = dt.userProgramID
				WHERE up.id = '$id' ";
		if($latest!="")
		{
			$sql .=" AND latest = '{$latest}' ";// latest = 1
		}
		$sql .=" ORDER BY time DESC ";
		
		return $this->db->query($sql)->result_array();
	}	
	
	//return program title for info display.. dun sa today page
	function getProgramTitle($id)
	{
		$sql = "SELECT title,logo,batch,programTempID,dateStart,dateEnd FROM tb_programs p
				JOIN tb_programtemplate pt ON pt.id = p.programTempID
				JOIN tb_user_program up ON p.id = up.programID
				WHERE up.id = '$id'";
		if($this->db->query($sql)->num_rows()>0)
		{
			return $this->db->query($sql)->row();
		}		
	}
	//return date for info display.. dun sa today page
	function getDateContent($id)
	{
		$this->db->where('id',$id);
		$q = $this->db->get('tb_dates');
		if($q->num_rows()>0)
		{
			return $q->row();
		}		
	}
	
	//add information
	function addInfo($data)
	{
		$this->db->set($data);
		if($this->db->insert('tb_information'))
		{
			return $this->db->insert_id();
		}
	}
	
	//add details
	function addDetails($data,$upid,$datainfo, $time = NOW)
	{
		$sql = "SELECT * 
			FROM tb_details d
			JOIN tb_dates dt ON d.dateID = dt.id
			JOIN tb_user_program up ON up.id = dt.userProgramID
			WHERE up.id = '{$upid}' 
			AND d.infoID = '{$data['infoID']}' ";
		$q = $this->db->query($sql);
		if($q->num_rows()>0)//check first if it is old..set latest to 0 if it is old
		{
			$sql = "UPDATE tb_details d
					JOIN tb_dates dt ON d.dateID = dt.id
					JOIN tb_user_program up ON up.id = dt.userProgramID
					SET latest = 0 WHERE up.id = '{$upid}' AND infoID = '{$data['infoID']}' ";
			$this->db->query($sql);
		}
                
                if($data['opportunityType'] == 'Won'){
                    $comparedata = array(
                        'firstname' => $datainfo['firstname'],
                        'lastname'  => $datainfo['lastname'],
                        'mi'        => $datainfo['mi'],
                        'email'     => $datainfo['email'],
                        'eventType' => $data['eventType'],
                        'upid'      => $upid
                    );
                    $backupdata = $this->checkInBackup($comparedata);
                    if($backupdata){
                        $this->db->set('time',$backupdata->eventTime);
                    }else{
                        $this->db->set('time', $time);
                    }
                }else{
                    $this->db->set('time', $time);
                }                
                
		$this->db->set($data);
		if($this->db->insert('tb_details'))
		{
			return TRUE;
		}
	}
	
	//edit tb_information
	function editInfo($data,$id)
	{
		$this->db->where('id',$id);
		$this->db->update('tb_information',$data);
	}
	
	//edit details table
	function editDetails($data,$id)
	{

		$updatetime = false;
		if( strtolower($data['opportunityType']) == 'won' ) {
			if( strtolower($this->db->select('opportunityType')->where('id', $id)->get('tb_details')->row()->opportunityType) != 'won' ) {
				$updatetime = true;
			}
		}
		
		$this->db->where('id',$id);
		$this->db->set($data);

		if( $updatetime == true ) {
			$this->db->set('time', NOW);
		}

		$this->db->update('tb_details');		
	}
	
	function deleteEvent($did,$infoid)
	{
		$str = ""; //for trails
		$this->db->where('id',$did);
		$q = $this->db->get('tb_details')->row();
			if($q->latest==1)
			{
				$sql = "SELECT * FROM tb_details WHERE dateID = '{$q->dateID}' AND infoID = '{$q->infoID}' ORDER BY id DESC";
				$q = $this->db->query($sql);
				if($q->num_rows()>1)
				{
					$prev = $q->row_array(1);
					$sql = "UPDATE tb_details SET latest = 1 WHERE id = '{$prev['id']}' ";
					$this->db->query($sql);
				}	
			}
		$infoData = $this->getOldRecForEditor($infoid);
		$progData = $this->getTitleViaDid($did);
                
                //save deleted backup if won
                if($infoData->opportunityType == 'Won'){
                    $this->saveDeletedBackup(array(
                        'firstname' => $infoData->firstname,
                        'lastname'  => $infoData->lastname,
                        'mi'        => $infoData->mi,
                        'email'     => $infoData->email,
                        'event_type'    => $infoData->eventType,
                        'userprogid'    => $infoData->userprogid,
                        'eventTime'     => $infoData->time,
                        'date_deleted'  => NOW,
                        'deleted_by'    => $this->my_session->userdata('uid')
                    ));
                }
                
		$str .= "Remove  {$infoData->firstname} {$infoData->lastname} from {$progData->title} {$progData->batch}.";
		$this->db->where('id',$did);		
		$this->db->delete('tb_details');
		
		$this->db->where('infoID',$infoid);
		if($this->db->get('tb_details')->num_rows()<1)
		{
			$this->db->where('id',$infoid);
			$this->db->delete('tb_information');			
		}
		return $str;
	}
	

	//check if email exist
	function checkEmail($email,$id="")
	{
		$sql = "SELECT * FROM tb_information
				WHERE id != '$id'
				AND email LIKE '$email'
		";
		$q = $this->db->query($sql);
		if($q->num_rows()>0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	//get specific information of the event.. contact/company/ etc... 
	function getDetails($id)
	{
		$sql="";
		$sql .="SELECT d.id did,i.id infoid,eventType,time,remark,opportunityType,cPercent,note,refferal,companyName,lastname,firstname,mi,position,telephone,mobile,fax,email ";
		$sql .="FROM tb_details d
				JOIN tb_information i 
				ON d.infoID = i.id
				WHERE d.id = '$id' ";
		
		return $this->db->query($sql)->row_array();
	}
	//get history of a client via details id
	function getHistory($did,$showall)
	{
		$details = $this->getDetails($did);
		$sql = " SELECT  d.id id,infoID,eventType,time,remark,opportunityType,note,cPercent,CONCAT(pt.title,' ',p.batch)program
				FROM tb_details d
				JOIN tb_dates dt ON d.dateID = dt.id
				JOIN tb_user_program up ON up.id = dt.userProgramID
				JOIN tb_programs p ON p.id = up.programID
				JOIN tb_programtemplate pt ON p.programTempID = pt.id
				WHERE infoID = '{$details['infoid']}' ";
		if($showall!=1)
		{
			$sql .=" AND d.id != {$did} ";				
		}
		$sql.=" ORDER BY d.time DESC ";
		//echo $sql;
		return $this->db->query($sql)->result_array();
	}
	

	//get old records to copy
	function getOldRecords($userid,$programtempid,$filter,$limit="",$offset="")
	{
		$sql = "";
		$sql .="SELECT i.id infoid,CONCAT(lastname,', ',firstname)name,companyName,d.id did,
						CASE remark
						WHEN 'Rejected' THEN 'Rejected'
						ELSE opportunityType
						END as status
				FROM tb_details d 
				JOIN tb_information i ON d.infoID = i.id 
				JOIN tb_dates dt ON dt.id = d.dateID
				JOIN tb_user_program up ON up.id = dt.userProgramID 
				JOIN tb_programs p ON p.id = up.programID
				JOIN tb_user_archive ua ON up.id = ua.userProgramID
				WHERE d.latest = 1 ";
				$sql .= " AND ua.isActive = 1 ";
				$sql .=" AND ua.userID = '{$userid}' ";
				//$sql .=" AND p.programTempID = '{$programtempid}' ";
		
		if($filter['searchval']!="")
		{
			$sql .=" AND {$filter['searchkey']} LIKE '{$filter['searchval']}%' ";
		}
		if($filter['searchprog']!="")
		{
			$sql .=" AND p.id = {$filter['searchprog']} ";
		}
		
		if(isset($filter['searchstatus']) && $filter['searchstatus']!="all" && $filter['searchstatus'] !="Rejected")
		{
			$sql .=" AND opportunityType = '{$filter['searchstatus']}' ";
		}
		elseif(isset($filter['searchstatus']) && $filter['searchstatus'] =="Rejected")
		{
			$sql .=" AND remark = 'Rejected' ";
		}		 
		
		$sql .=" GROUP BY i.id ";
		
		if(isset($filter['searchstatus']) && $filter['searchstatus']=="Pending")
		{
			$sql .= " ORDER BY cPercent+0 DESC,lastname,d.id DESC ";
		}
		else 
		{
			$sql .=" ORDER BY lastname,d.id DESC ";
		}
		
		if($limit!="")
		{
			$sql .=" LIMIT $offset,$limit ";
		}
//		echo $sql;
		return $this->db->query($sql);
	}

	function getOldRecForEditor($id)
	{
		$sql = "SELECT i.id id,companyName,lastname,firstname,mi,position,telephone,mobile,fax,email,dt.userProgramID userprogid, d.eventType, d.time, d.opportunityType
				FROM tb_information i
				JOIN tb_details d ON d.infoID = i.id
				JOIN tb_dates dt ON d.dateID = dt.id
				WHERE i.id = '{$id}' 
				ORDER BY d.id DESC,lastname ";
		
		return $this->db->query($sql)->row();
	}
	
	//check if the date id is within the current program
	function checkDateID($dateID,$upID)
	{
		$sql ="SELECT d.id did,up.id upid 
				FROM tb_dates d 
				JOIN tb_user_program up ON d.userProgramID = up.id
				WHERE up.id = '$upID'
				AND d.id = '$dateID' ";
		
		$q = $this->db->query($sql);
		if($q->num_rows()>0)
		{
			return TRUE;//follow up pero bago
		}
		else
		{
			return FALSE;//(old)galing sa mga alumni
		}
	}
	
	//return program title for email fromname.. ung name na gamit pag nag email
	function getTitleViaDid($id)
	{
		$sql = "SELECT p.id,title,logo,batch,programTempID 
				FROM tb_programs p
				JOIN tb_programtemplate pt ON pt.id = p.programTempID
				JOIN tb_user_program up ON p.id = up.programID
				JOIN tb_dates dt ON up.id = dt.userProgramID
				JOIN tb_details d ON d.dateID = dt.id
				WHERE d.id = '$id'";
		if($this->db->query($sql)->num_rows()>0)
		{
			return $this->db->query($sql)->row();
		}		
	}

	//get list of user contacts
	function getUserContacts($userid,$filters = "")
	{
		$sql = "";
		$sql .="SELECT i.id infoid,CONCAT(firstname,' ',lastname)name,email
				FROM tb_details d 
				JOIN tb_information i ON d.infoID = i.id 
				JOIN tb_dates dt ON dt.id = d.dateID
				JOIN tb_user_program up ON up.id = dt.userProgramID
				JOIN tb_programs p ON p.id = up.programID
				JOIN tb_user_archive ua ON up.id = ua.userProgramID
				WHERE ua.userID	= '{$userid}' 
				AND email NOT LIKE '' 
				AND d.latest = '1' ";
		$sql .= " AND ua.isActive = 1 ";
		if($filters!="")
		{
			if($filters['searchval']!="")
			{
				if($filters['searchkey']=='name')
				$sql .=" AND (firstname LIKE '%{$filters['searchval']}%' OR lastname LIKE '%{$filters['searchval']}%') ";
				else 
				$sql .=" AND email LIKE '%{$filters['searchval']}%' ";
			}
			if($filters['conProgram']!="")
			{
				$sql .=" AND p.id = '{$filters['conProgram']}' ";
			}
			if(is_array($filters['infoids']))
			{
				$sql .=" AND i.id NOT IN ('".implode("','", $filters['infoids'])."') ";
			}
			
			if(isset($filters['searchstatus']) && $filters['searchstatus']!="all" && $filters['searchstatus'] !="Rejected")
			{
				$sql .=" AND opportunityType = '{$filters['searchstatus']}' ";
			}
			elseif(isset($filters['searchstatus']) && $filters['searchstatus'] =="Rejected")
			{
				$sql .=" AND remark = 'Rejected' ";
			}			
		}

		$sql .=" GROUP BY i.id ";
		
		if(isset($filters['searchstatus']) && $filters['searchstatus']=="Pending")
		{
			$sql .= " ORDER BY cPercent+0 DESC,firstname ";
		}
		else 
		{
			$sql .=" ORDER BY firstname ";
		}
		
		return $this->db->query($sql)->result_array();
	}
	//get  user programs, active and not
	function getUserPrograms($userid,$ptempid = "")
	{
		$sql = "";
		$sql .="SELECT p.id pid,CONCAT(pt.title,' ',p.batch)program
				FROM tb_programs p
				JOIN tb_programtemplate pt ON p.programTempID = pt.id
				JOIN tb_user_program up ON up.programID = p.id
				JOIN tb_user_archive ua ON up.id = ua.userProgramID
				WHERE ua.userID	= '{$userid}' ";
		$sql .= " AND ua.isActive = 1 ";
		if($ptempid!="")
		{
			$sql .=" AND pt.id = {$ptempid} ";
		}
		$sql .=" GROUP BY p.id ";
		$sql .= " ORDER BY pt.title,batch+0 DESC ";
		return $this->db->query($sql)->result_array();
	}
        
    function saveDeletedBackup($data)
    {
        $this->db->set($data);
        $this->db->insert('tb_deletedbackup');
    }
    
    function checkInBackup($data)
    {
        $sql = "SELECT id,eventTime
                FROM tb_deletedbackup
                WHERE firstname = '{$data['firstname']}'
                AND lastname = '{$data['lastname']}'
                AND mi = '{$data['mi']}'
                AND email = '{$data['email']}'
                AND userprogid = {$data['upid']}
                ORDER BY id DESC
                LIMIT 1";
                
        if($this->db->query($sql)->num_rows()>0)
        {
                $data = $this->db->query($sql)->row();
                //update restored data
                $this->db->where('id',$data->id);
                $this->db->set('restored_date',NOW);
                $this->db->update('tb_deletedbackup');
                
                return $data;
        }
        
        return false;
    }

    function checkLeadsInfoExists($data) {

    	$sql = "SELECT i.id AS infoID,dt.id AS detailID,lastname, firstname, POSITION, companyName
				FROM tb_information i
				JOIN tb_details dt ON i.id = dt.infoID
				JOIN tb_dates d ON d.id = dt.dateID
				WHERE d.userProgramID = ?
				AND lastname = ?
				AND firstname = ?
				AND companyName = ?";

		$q 	= $this->db->query($sql, array(
				$data['userprogid'],
				$data['lastname'],
				$data['firstname'],
				$data['companyName']
			));

		if ($q->num_rows() > 0) {
			return $q->row_array();
		}

		return false;

    }

}