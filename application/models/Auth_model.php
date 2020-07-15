<?php if (!defined('BASEPATH')) exit('No direct script allowed');

class Auth_model extends CI_Model
{

	/***** get data from user table *****/
	function get_user($q)
	{
		$q['IsActive'] = 1;
		$this->db->select('*');
		$this->db->from('tblusers');
		//$this->db->where('IsActive', 1);
		$this->db->where('EmailAddress', $q['EmailAddress']);
		$this->db->where('UserStatusId IN (0,2)');
		//$this->db->limit(1);
		$query = $this->db->get();
		return $query;
	}

	public function IsUserLocked($q)
	{
		try {
			if ($q) {
				$this->db->select('UserId,Attempts');
				$this->db->from('tblusers');
				$this->db->where('EmailAddress', $q['EmailAddress']);
				$query = $this->db->get();
				$result1 = $query->result();
				$UserId = $result1[0]->UserId;
				$Attempts = $result1[0]->Attempts;

				$c1 = $this->db->query("SELECT Value FROM tblmstconfiguration where `Key` = 'lockAttempts'")->result();
				$configration_attempt = $c1[0]->Value; //get attempts from configuration 

				if (($configration_attempt - 1) > $Attempts) {
					$IsLock = '0';
				} else {
					$IsLock = '1';
				}
				$invitation_data = array(
					'UserId' => $UserId,
					'Attempts' => $Attempts + 1,
					'IsLock' => $IsLock
				);
				$res = $this->db->query('call IsUserLocked(?,?,?)', $invitation_data);

				$c = $this->db->query("SELECT lastAttempton FROM tblusers where `UserId` = '$UserId'")->result();
				$time_lastattempt1 = $c[0]->lastAttempton; //get time of last attempt from usertable

				$c2 = $this->db->query("SELECT Value FROM tblmstconfiguration where `Key` = 'lockPeriod'")->result();
				$configration_period = $c2[0]->Value; //get configration_period from configuration 

				$currentDate = strtotime($time_lastattempt1);
				$futureDate = $currentDate + (60 * $configration_period);
				$time_lastattempt = date("Y-m-d H:i:s", $futureDate);

				$db_error = $this->db->error();
				if (!empty($db_error) && !empty($db_error['code'])) {
					throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
					return false; // unreachable return statement !!!
				}
				$data['IsLock'] = $IsLock;
				$data['time_lastattempt'] = $time_lastattempt;
				return $data;
			} else {
				return false;
			}
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
			return false;
		}
	}

	public function IsUserUnlocked($q)
	{
		try {
			if ($q) {
				$this->db->select('UserId');
				$this->db->from('tblusers');
				$this->db->where('EmailAddress', $q['EmailAddress']);
				$query = $this->db->get();
				$result1 = $query->result();
				$UserId = $result1[0]->UserId;

				$invitation_data = array(
					'UserId' => $UserId,
					'Attempts' => '0',
					'IsLock' => '0'
				);
				$res = $this->db->query('call IsUserLocked(?,?,?)', $invitation_data);

				$db_error = $this->db->error();
				if (!empty($db_error) && !empty($db_error['code'])) {
					throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
					return false; // unreachable return statement !!!
				}
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
			return false;
		}
	}

	public function chechtime($q)
	{
		try {
			if ($q) {
				$this->db->select('UserId');
				$this->db->from('tblusers');
				$this->db->where('EmailAddress', $q['EmailAddress']);
				$query = $this->db->get();
				$result1 = $query->result();
				$UserId = $result1[0]->UserId;

				$c = $this->db->query("SELECT lastAttempton FROM tblusers where `UserId` = '$UserId'")->result();
				$time_lastattempt1 = $c[0]->lastAttempton; //get time of last attempt from usertable

				$c2 = $this->db->query("SELECT c.Value as config_time  FROM tblmstconfiguration c where `Key` = 'lockPeriod'")->result();
				$configration_period = $c2[0]->config_time; //get configration_period from configuration 

				$currentDate = strtotime($time_lastattempt1);
				$futureDate = $currentDate + (60 * $configration_period);
				$time_lastattempt = date("Y-m-d H:i:s", $futureDate);

				$db_error = $this->db->error();
				if (!empty($db_error) && !empty($db_error['code'])) {
					throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
					return false; // unreachable return statement !!!
				}
				$date = date('Y-m-d H:i:s');
				if ($date >= $time_lastattempt) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
			return false;
		}
	}
}
