<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_tblUserAssessment extends CI_Migration {

	/**
	 * Name of the table to be used in this migration!
	 *
	 * @var string
	 */
	protected $_table_name = "tblUserAssessment";

	public function up()
	{
		$fields = array(
			"UserAssessmentId INT(11) AUTO_INCREMENT PRIMARY KEY",
			"ParentAssessment INT(11) NOT NULL DEFAULT 0",
			"UserId INT(11) NOT NULL",
			"CertificateId INT(11) NOT NULL",
			"TotalScore int(3) NOT NULL DEFAULT 0",
			"TimeOfAssessment TIMESTAMP",
			"ExpirationDate TIMESTAMP",
			"TotalScheduleAssessmentAttempt int(3)",
			"AssessmentStartDate TIMESTAMP",
			"AssessmentEndDate TIMESTAMP",
			"AssessmentStatusId INT(11) DEFAULT 0",		
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