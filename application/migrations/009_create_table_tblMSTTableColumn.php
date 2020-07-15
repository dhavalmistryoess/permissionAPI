<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_tblMSTTableColumn extends CI_Migration {

	/**
	 * Name of the table to be used in this migration!
	 *
	 * @var string
	 */
	protected $_table_name = "tblMSTTableColumn";

	public function up()
	{
		$fields = array(
			"ColumnId INT(11) AUTO_INCREMENT PRIMARY KEY",
			"TableId INT(11) NOT NULL",
			"ColumnName VARCHAR(100) NOT NULL",
			"DisplayName VARCHAR(100) NOT NULL",
			"IsActive BIT(1) NOT NULL DEFAULT b'1'",
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