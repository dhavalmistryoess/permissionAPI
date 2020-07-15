<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_tblActivityLog extends CI_Migration {

	/**
	 * Name of the table to be used in this migration!
	 *
	 * @var string
	 */
	protected $_table_name = "tblActivityLog";

	public function up()
	{
		$fields = array(
			"ActivityLogId INT(11) AUTO_INCREMENT PRIMARY KEY",
			"ModuleId VARCHAR(100) NOT NULL",
			"UserId INT(11) NOT NULL",
			"Module VARCHAR(100) NOT NULL",
			"Activity VARCHAR(100) NOT NULL",
			"CreatedOn TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
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