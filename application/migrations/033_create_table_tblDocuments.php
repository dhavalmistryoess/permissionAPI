<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_tblDocuments extends CI_Migration {

	/**
	 * Name of the table to be used in this migration!
	 *
	 * @var string
	 */
	protected $_table_name = "tblDocuments";

	public function up()
	{
		$fields = array(
			"DocumentId INT(11) AUTO_INCREMENT PRIMARY KEY",
			"DocumentName varchar(250) NOT NULL",
			"DocumentType varchar(250) NOT NULL",
			"DocumentSize int(5) NOT NULL",
			"DocumentSizeUnit int(11) NOT NULL",
			
		);
		$common_fields = array(
			"IsActive BIT(1) NOT NULL DEFAULT b'1'",
			"CreatedBy INT(11) NOT NULL",
			"CreatedOn TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
			"UpdatedBy INT(11) NOT NULL",
			"UpdatedOn TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_field($common_fields);
		$this->dbforge->create_table($this->_table_name, TRUE);
	}

	public function down()
	{
		$this->dbforge->drop_table($this->_table_name, TRUE);
	}

}

?>