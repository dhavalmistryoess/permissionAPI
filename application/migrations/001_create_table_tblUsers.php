<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_tblUsers extends CI_Migration {

	/**
	 * Name of the table to be used in this migration!
	 *
	 * @var string
	 */
	protected $_table_name = "tblUsers";

	public function up()
	{
		$fields = array(
			"UserId INT(11) AUTO_INCREMENT PRIMARY KEY",
			"ModuleId VARCHAR(100) NOT NULL DEFAULT '0'",
			"RoleId INT(11) NOT NULL",
			"EmployeeId INT(11)",
			"FirstName VARCHAR(100) NOT NULL",
			"MiddleName VARCHAR(100)",
			"LastName VARCHAR(100) NOT NULL",
			"PhoneNumber VARCHAR(15)",
			"EmailAddress VARCHAR(100) NOT NULL",
			"Password VARCHAR(100) NOT NULL",
			"ForgotPasswordCode VARCHAR(10)",
			"ExpertInCategory VARCHAR(100)",
			"IsEmployee BIT(1) NOT NULL DEFAULT b'1'",
			"RegistrationType tinyint(1) NOT NULL",
			"UserStatusId TINYINT(1) DEFAULT '1'",
			
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

		$Password = md5('12345678');
		$query="insert into tblUsers(RoleId,FirstName,LastName,EmailAddress,Password,UserStatusId) values(1,'Super','Admin','it@gmail.com','$Password',0)";
		$this->db->query($query);
	}

	public function down()
	{
		$this->dbforge->drop_table($this->_table_name, TRUE);
	}

}

?>