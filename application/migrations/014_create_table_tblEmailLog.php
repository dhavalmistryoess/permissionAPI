<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_tblEmailLog extends CI_Migration {

	/**
	 * Name of the table to be used in this migration!
	 *
	 * @var string
	 */
	protected $_table_name = "tblEmailLog";

	public function up()
	{
		$fields = array(
			"EmailLogId INT(11) AUTO_INCREMENT PRIMARY KEY",
			"ModuleId VARCHAR(100) NOT NULL",
			"EmailTemplateId INT(11) NOT NULL",
			"`From` VARCHAR(100) NOT NULL",
			"`To` VARCHAR(255) NOT NULL",
			"Cc TEXT",
			"Bcc TEXT",
			"Subject VARCHAR(255) NOT NULL",
			"EmailBody TEXT NOT NULL",
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