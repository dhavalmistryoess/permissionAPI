<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_tblMSTCertificates extends CI_Migration {

	/**
	 * Name of the table to be used in this migration!
	 *
	 * @var string
	 */
	protected $_table_name = "tblMSTCertificates";

	public function up()
	{
		$fields = array(
			"CertificateId INT(11) AUTO_INCREMENT PRIMARY KEY",
			"CertificateName VARCHAR(100) NOT NULL",
			"VersionName VARCHAR(50) NOT NULL",
			"ReleaseDate date NOT NULL",
			"Description text NOT NULL",
			"WhoCanApply text NOT NULL",
			"Features text NOT NULL",
			"USDPrice decimal(12,2) NOT NULL",
			"INRPrice decimal(12,2)",
			"EURPrice decimal(12,2)",
			"CertificationDuration int(3)",
			"AssessmentDuration int(3) NOT NULL",
			"BeforeRenewButtonDisplay int(3)",
			"AfterRenewButtonDisplay int(3)",
			"CoolingPeriod int(3)",
			"HasSubCertificate BIT(1) NOT NULL",
			"PracticeExamAttempts int(3) NOT NULL DEFAULT 0",
			"AssessmentStartAfterDaysForProctor int(3)",
			"AssessmentStartAfterDaysForCandidate int(3) NOT NULL",
			"HasProctor BIT(1) NOT NULL DEFAULT b'1'",
			"HasDisplayCandidateInfo BIT(1) NOT NULL DEFAULT b'1'",
			"HasDisplayProctorInfo BIT(1) NOT NULL DEFAULT b'1'",
			"EligibilityCriteria int(3) NOT NULL",
			"RescheduleAssessment int(3) NOT NULL DEFAULT 0",
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