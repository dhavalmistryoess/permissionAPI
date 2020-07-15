<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_tblMSTConfiguration extends CI_Migration {

	/**
	 * Name of the table to be used in this migration!
	 *
	 * @var string
	 */
	protected $_table_name = "tblMSTConfiguration";

	public function up()
	{
		$fields = array(
			"ConfigurationId INT(11) AUTO_INCREMENT PRIMARY KEY",
			"ModuleId VARCHAR(100) NOT NULL DEFAULT '0'",
			"`Key` VARCHAR(100) NOT NULL",
			"`Value` VARCHAR(100) NOT NULL",
			"DisplayText VARCHAR(100)",
			"`Description` TEXT",
			"DisplayOrder INT(4) NOT NULL DEFAULT '0'",
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

		// $data = array(
		// 	array(
		// 	   'Key' => 'My title' ,
		// 	   'name' => 'My Name' ,
		// 	   'date' => 'My date'
		// 	),
		// 	array(
		// 	   'title' => 'Another title' ,
		// 	   'name' => 'Another Name' ,
		// 	   'date' => 'Another date'
		// 	)
		//  );
		 
		//  $this->db->insert_batch('mytable', $data); 
	}

	public function down()
	{
		$this->dbforge->drop_table($this->_table_name, TRUE);
	}

}

?>