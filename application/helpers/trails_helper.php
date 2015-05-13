<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
//create audit trails
function trails($action,$userID = "") 
{
	$id = $userID!=""?$userID:my_session_value('uid');
	$ip = $_SERVER['REMOTE_ADDR'];

	$data = array(
		'user_id'=>$id,
		'action'=>$action,
		'ipaddress'=>$ip
	);
	
	$CI = & get_instance();
	$CI->db->set($data);
	$CI->db->set('datetime',NOW);
	$CI->db->insert('tb_trails');	
}

	function countArrayValue($ar = array())
	{
		$t = 0;
		foreach ($ar as $a)
		{
			$t += (int)$a;
		}
		return $t;
	}