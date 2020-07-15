<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_tblErrorLog extends CI_Migration {

	/**
	 * Name of the table to be used in this migration!
	 *
	 * @var string
	 */
	protected $_table_name = "tblErrorLog";

	public function up()
	{
		$fields = array(
			"id INT(11) AUTO_INCREMENT PRIMARY KEY",
			"errno INT(2) NOT NULL",
			"errtype VARCHAR(32) NOT NULL",
			"errstr TEXT NOT NULL",
			"errfile VARCHAR(255) NOT NULL",
			"errline INT(4) NOT NULL",
			"user_agent VARCHAR(120) NOT NULL",
			"ip_address VARCHAR(45) NOT NULL",
			"`time` DATETIME NOT NULL"
		);		
		$this->dbforge->add_field($fields);
		$this->dbforge->create_table($this->_table_name, TRUE);
	}

	public function down()
	{
		$this->dbforge->drop_table($this->_table_name, TRUE);
	}

}

?>