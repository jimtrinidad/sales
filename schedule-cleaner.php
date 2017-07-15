<?php

define('BASEPATH', '');

require_once 'application/config/database.php';

$keep_schedule = 1;

$SQL = new SQL($db['default']);

//max batch + 0 because batch field datatype is varchar on tb_programs
$result = $SQL->_execute("select programTempID,max(batch+0) as batch
from tb_programs o
group by programTempID");

foreach($result->fetch_all(MYSQLI_ASSOC) as $item) {
	
	print_r($item);
	$schedules_result = $SQL->_execute("select * from tb_program_schedule
where program_setting_id = " . $item['programTempID'] . "
and batch > " . ($item['batch'] + $keep_schedule));

	foreach($schedules_result->fetch_all(MYSQLI_ASSOC) as $schedule) {

		print_r($schedule);
		$session_result = $SQL->_execute("DELETE FROM tb_program_session WHERE schedule_id = " . $schedule['schedule_id']);

		$SQL->_execute("DELETE FROM tb_program_schedule WHERE schedule_id = " . $schedule['schedule_id']);

	}

	//set new next_date on program template
 	set_end_date($item['programTempID'], $SQL);

}

function set_end_date($programID, $SQL) 
{
	$sql = "SELECT * 
			FROM tb_program_schedule
			WHERE program_setting_id = '{$programID}'
			ORDER BY end_date DESC";

	$result = $SQL->_execute($sql);
	$record = $result->fetch_array();
	if (strtotime($record['end_date']) < time()) {
		$curent_end_date =  date('Y-m-d');
	} else {
		$curent_end_date = $record['end_date'];
	}

	$response = $SQL->_execute("SELECT * FROM tb_programtemplate WHERE id = {$programID}");
	$settings = $response->fetch_array();

	// echo $curent_end_date . ' - '. $settings['time_span'] . PHP_EOL;
	$new_end_date = date('Y-m-d',strtotime($curent_end_date." +".$settings['time_span']." week"));

	$SQL->_execute("UPDATE tb_programtemplate SET next_date = '{$new_end_date}' WHERE id = {$programID}");

}

class SQL {

	public $conn_id;

	public function __construct($config) {
		$this->conn_id = @mysqli_connect($config['hostname'], $config['username'], $config['password'], $config['database']);
	}

	/**
	 * Execute the query
	 *
	 * @access	private called by the base class
	 * @param	string	an SQL query
	 * @return	resource
	 */
	function _execute($sql)
	{
		$result = mysqli_query($this->conn_id, $sql);
		return $result;
	}

}