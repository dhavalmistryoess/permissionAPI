<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_tblAddresses extends CI_Migration {

	/**
	 * Name of the table to be used in this migration!
	 *
	 * @var string
	 */
	protected $_table_name = "tblAddresses";

	public function up()
	{
		$fields = array(
			"AddressId INT(11) AUTO_INCREMENT PRIMARY KEY",
			"AddressTypeId INT(11) NOT NULL",
			"UserId INT(11) NOT NULL",
			"Address1 VARCHAR(255) NOT NULL",
			"Address2 VARCHAR(255)",
			"Address3 VARCHAR(255)",
			"City VARCHAR(100) NOT NULL",
			"StateId INT(11) NOT NULL",
			"CountryId INT(11) NOT NULL",
			"ZipCode VARCHAR(10) NOT NULL",
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