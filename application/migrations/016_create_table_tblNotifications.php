<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_tblNotifications extends CI_Migration {

	/**
	 * Name of the table to be used in this migration!
	 *
	 * @var string
	 */
	protected $_table_name = "tblNotifications";

	public function up()
	{
		$fields = array(
			"NotificationId INT(11) AUTO_INCREMENT PRIMARY KEY",
			"RoleId INT(11) NOT NULL",
			"NotificationFor INT(11) NOT NULL",
			"NotificationText VARCHAR(500) NOT NULL",
			"ActionURL VARCHAR(100) NOT NULL",
			"IsRead BIT(1) NOT NULL DEFAULT b'0'",
			"IsActive BIT(1) NOT NULL DEFAULT b'1'",
			"CreatedBy INT(11) NOT NULL",
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