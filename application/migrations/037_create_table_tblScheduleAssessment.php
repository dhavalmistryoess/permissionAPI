<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_tblScheduleAssessment extends CI_Migration {

	/**
	 * Name of the table to be used in this migration!
	 *
	 * @var string
	 */
	protected $_table_name = "tblScheduleAssessment";

	public function up()
	{
		$fields = array(
			"ScheduleAssessmentId INT(11) AUTO_INCREMENT PRIMARY KEY",
			"CertificateId int(11) NOT NULL",
			"CandidateId int(11) NOT NULL",
			"ProctorId int(11)",
			"AssignDate date",
			"CenterAddressId int(11)",
			"Time int(3)",
			"Comment text",
			"ScheduledStatusId tinyint(1) DEFAULT 1",
			"ScheduleAssessmentStatus tinyint(1) DEFAULT 0",
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