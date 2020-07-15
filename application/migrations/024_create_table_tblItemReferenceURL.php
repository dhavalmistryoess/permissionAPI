<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_tblItemReferenceURL extends CI_Migration {

	/**
	 * Name of the table to be used in this migration!
	 *
	 * @var string
	 */
	protected $_table_name = "tblItemReferenceURL";

	public function up()
	{
		$fields = array(
			"ItemReferenceURLId INT(11) AUTO_INCREMENT PRIMARY KEY",
			"ItemReviewId INT(11) NOT NULL",
			"ReferenceURL VARCHAR(255) NOT NULL",
			"ReferenceURLComment text",
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