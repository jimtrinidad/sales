<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function checksession($name='session_id')
{
	$my_session = new My_Session();
	$session = $my_session->userdata($name);
	if ($session)
	{
		return TRUE;
	}
	else 
	{
		return FALSE;
	}
}

function my_session_value($name)
{
	$my_session = new My_Session();
	return $my_session->userdata($name);
}

function userPrivilege($privilege,$userID = NULL )
{
	$my_session = new My_Session();
	$CI = &get_instance();
	$userID = is_null($userID)?$my_session->userdata('uid'):$userID;
	$privID = $CI->db->where('id',$userID)
		->get('tb_users')
		->row()->p_id;
	if($CI->db->field_exists($privilege,'tb_privilege'))
	{
		return $CI->db->where('id',$privID)
				->get('tb_privilege')
				->row()
				->$privilege;
	}else return FALSE;
}

?>