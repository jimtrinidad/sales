<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	function getUserData($userID)
	{
		$CI = &get_instance();
		$sql = "SELECT id,CONCAT(firstname,' ',lastname)name,photo,firstname,email
				FROM tb_users u
				WHERE id = '{$userID}' ";
		$q = $CI->db->query($sql);
		if($q->num_rows()>0)
		{
			return $q->row();
		}else $obj->name = '<span class="fadeTextSmall">not available</span>'; return $obj;	
	}
	
		function get_date_diff($start, $end=NOW)
	{
		$sdate = strtotime($start);
		$edate = strtotime($end);
		$timeshift = "";

		$time = $edate - $sdate;
		if($time>=0 && $time<=59) {
			// Seconds
			$timeshift = $time.' seconds ago';

		} elseif($time>=60 && $time<=3599) {
			// Minutes + Seconds
			$pmin = ($edate - $sdate) / 60;
			$premin = explode('.', $pmin);

			$presec = $pmin-$premin[0];
			$sec = $presec*60;

			$timeshift = $premin[0].' minutes ago';

		} elseif($time>=3600 && $time<=86399) {
			// Hours + Minutes
			$phour = ($edate - $sdate) / 3600;
			$prehour = explode('.',$phour);

			$premin = $phour-$prehour[0];
			$min = explode('.',$premin*60);

			$presec = isset($min[1]) ? '0.'. $min[1] : '0' ;
			$sec = $presec*60;

			$timeshift = $prehour[0].' hours ago';

		} elseif($time>=86400) {
			// Days + Hours + Minutes
			$pday = ($edate - $sdate) / 86400;
			$preday = explode('.',$pday);

			$phour = $pday-$preday[0];
			$prehour = explode('.',$phour*24); 

			$premin = ($phour*24)-$prehour[0];
			$min = explode('.',$premin*60);

			$presec = isset($min[1]) ? '0.'. $min[1] : '0' ;
			$sec = $presec*60;

			//$timeshift = $preday[0].' days '.$prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec '."<b>ago</b>";
			$timeshift = date("F j, Y",$sdate);


		}
		return $timeshift;
	}	
	
	function copyPhoto($id,$photostr)
	{
		$orig = $photostr;
		$ext = substr($photostr, strrpos($photostr, '.') + 1);
		$photostr = substr($photostr,strpos($photostr, 'assets/'),strlen($photostr)-strpos($photostr, 'assets/'));
		$photo = $id.'-'.str_replace(" ", "", getUserData($id)->firstname).".".$ext;
		copy($orig,'assets/images/userphoto/'.$photo);
		$files = glob("assets/images/userphoto/temp/*.".$ext); 
		foreach($files as $file){
			unlink($file); 
		}
		/*if ($handle = opendir('assets/images/userphoto/temp'))  //delete all temporary files in userphoto/temp folder
		{
			while (false !== ($file = readdir($handle))) {
				$temp = 'assets/images/userphoto/temp/' . $file;
				unlink($temp);
			}
			closedir($handle);
		}*/

		$CI = &get_instance();
		$CI->db->where('id',$id)->set('photo',$photo)->update('tb_users');
				
	}

	function critical_program($elements)
	{
		return $elements['alertLevel'] === 3 ? $elements : FALSE; 
	}

	function top_program($elements)
	{
		return $elements['alertLevel'] === 0 ? $elements : FALSE; 
	}

	function number_prefix($num)
	{
		switch ($num)
		{
			case 'Unranked':
			case 0: $prefix = 'n/a';break;
			case 1: $prefix = $num.'st';break;
			case 2: $prefix = $num.'nd';break;
			case 3: $prefix = $num.'rd';break;
			default: $prefix = $num.'th';break;
		}
		return $prefix;	
	}
	
	function week_before($week)
	{
		$str = "";
		if( $week < -1 ) $str .= abs($week) ." weeks late";
		elseif( $week == -1 ) $str .= " 1 week late";
		elseif( $week == 0 ) $str .= " ends this week";
		elseif( $week == 1 ) $str .= " 1 more week to go";
		else $str .= $week ." more weeks to go";
		
		return $str;
	}
	
	function ifEmpty($str,$message = 'not available',$html = TRUE)
	{
		return empty($str) ? $html ? "<span class='fadeTextSmall'>{$message}</span>" : $message : $str;
	}	
	
	function myDate($date,$message = 'not available',$html = TRUE)
	{
		if($date == "0000-00-00" || empty($date)) return $html ? "<span class='fadeTextSmall'>{$message}</span>" : $message;
		return date("M d, Y",strtotime($date));
	}	
	
	function get_skip_days()
	{
		$CI = &get_instance();
		$dates = $CI->db->select('date')->get('tb_non_working_days')->result_array();
		$days = array();
		foreach ($dates as $d)
		{
			array_push($days, $d['date']);
		}
		return $days;
	}

	function array_sort($array, $on, $order=SORT_ASC)
	{
		$new_array = array();
		$sortable_array = array();
		$on = explode(".", $on);
		if (count($array) > 0) {
			foreach ($array as $k => $v) { // 1st dimention
				if (is_array($v)) {
					if(array_key_exists($on[0], $v)) { //validate if first key exist in 2nd array
						foreach ($v as $k2 => $v2) { //2nd dimention
							if ($k2 == $on[0]) {
								if(isset($on[1]) && is_array($v2)) {
									if(array_key_exists($on[1], $v2)) { //validate if 2nd key exist in 3nd array
										foreach ($v2 as $k3=>$v3) { //3rd dimention
											if($k3 == $on[1]) {
												if(isset($on[2]) && is_array($v3)) {
													if(array_key_exists($on[2], $v3)) { //validate if 3nd key exist in 4nd array
														foreach ($v3 as $k4=>$v4) { //4th dimention
															if($k4 == $on[2]){
																$sortable_array[$k] = $v4;
															}
														} //end 4th foreach
													}else {
														$sortable_array[$k] = $v; //default if key does not exist in the 4th array
													}
												}else {
													$sortable_array[$k] = $v3;
												}
											}
										} //end 3rd foreach
									}else {
										$sortable_array[$k] = $v; //default if key does not exist in the 3rd array
									}
								}else {
									$sortable_array[$k] = $v2;
								}
							}
						} // end 2nd foreach
					} else {
						$sortable_array[$k] = $v; //default if key does not exist in the 2nd array
					}
				} else {
					$sortable_array[$k] = $v;
				}
			} // end 1st foreach
	
			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}
	
			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}
	
		return $new_array;
	}
	
	function countValGreater($array,$key,$condition = 1)
	{
		$array = is_array($array)?$array:array($array);
		$i = 0;
		foreach ($array as $value)
		{
			if(isset($value[$key]))
			{
				if($value[$key] > $condition)
				{
					$i++;
				}
			}
		}
		return $i;
	}

	
	//bago
	
	function getProgramDetails($program_setting_id)
	{
		$CI = &get_instance();
		return $CI->db->where('id',$program_setting_id)->get('tb_programtemplate')->row();
	}
	
	function truncateString($str, $max, $rep = '...') 
	{
		if(strlen($str) > $max) 
		{
			$leave = $max - strlen($rep);
			$str = substr_replace($str, $rep, $leave);
			return $str;
		}
		else
		{
			return $str;
		}
	}	
	
	function savephoto($name,$photostr)
	{
		$ext = substr($photostr, strrpos($photostr, '.') + 1);
		$str = substr($photostr,strpos($photostr, 'assets/'),strlen($photostr)-strpos($photostr, 'assets/'));
		
		$photo = $name."-logo.".$ext;
		copy($photostr,'assets/photos/logo/'.$photo);
		unlink($str);
		return $photo;				
	}

	function get_venue($venue_id)
	{
		$CI = &get_instance();
		return $CI->db->where('venue_id',$venue_id)->get('tb_program_venue')->row();
	}
	
	function get_speaker($speaker_id)
	{
		$CI = &get_instance();
		
		$sql = "SELECT *,CONCAT(firstname,' ',lastname) AS name
				FROM tb_program_speaker
				WHERE speaker_id = '{$speaker_id}'";
		
		return $CI->db->query($sql)->row();
	}
	
	function is_holiday($date)
	{
		// YYYY-MM-DD
		$check_date_arr = explode('-', $date);
		
		$CI = &get_instance();
		$data = array();
		$rows = $CI->db->order_by('holiday_type','desc')->get('tb_holiday')->result_array();
		foreach($rows as $row)
		{
			// DD|MM|YYYY
			$date_arr = explode('|',$row['date']);
			if($row['holiday_type'] == 'regular')
			{				
				$holiday[] = $check_date_arr[0].'-'.$date_arr[1].'-'.$date_arr[0];
			}
			else 
			{
				$holiday[] = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
			}
			
		}
				
		return in_array($date, $holiday) ? TRUE : FALSE;
	}