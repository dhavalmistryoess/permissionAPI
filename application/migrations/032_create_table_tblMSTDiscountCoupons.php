<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_tblMSTDiscountCoupons extends CI_Migration {

	/**
	 * Name of the table to be used in this migration!
	 *
	 * @var string
	 */
	protected $_table_name = "tblMSTDiscountCoupons";

	public function up()
	{
		$fields = array(
			"CouponId INT(11) AUTO_INCREMENT PRIMARY KEY",
			"DiscountTypeId INT(11) NOT NULL",
			"CurrencyTypeId INT(11) NOT NULL",
			"CouponCode varchar(100) NOT NULL",
			"Value decimal(12,2) NOT NULL",
			"ValidFrom TIMESTAMP NOT NULL",
			"ValidTo TIMESTAMP NOT NULL",
			
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