<?php

class AuditLog_model extends CI_Model {
    protected $responseArr;

    function __construct()
    {
        parent::__construct();
        $this->responseArr['status'] = 'success';
        $this->responseArr['message'] = '';
        $this->responseArr['data'] = '';
    }
    /*     * *** Get All activity logs  **** */
    public function getActivityLogs() {
        try {
            $result = $this->db->query('call GetActivityLogs(?)', 0);
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res = array();
            if ($result->result()) {
                $res = $result->result();
            }
            $this->responseArr['status'] = 'success';
            $this->responseArr['message'] = '';
            $this->responseArr['data'] = $res;
            return $this->responseArr;
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

    /*     * *** Get All email logs  **** */

    public function getEmailLogs() {
        try {
            $result = $this->db->query('call GetEmailLogs(?)', 0);
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res = array();
            if ($result->result()) {
                $res = $result->result();
            }
            $this->responseArr['status'] = 'success';
            $this->responseArr['message'] = '';
            $this->responseArr['data'] = $res;
            return $this->responseArr;
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

    /*     * *** Get All error logs  **** */

    public function getErrorLogs() {
        try {
            $result = $this->db->query('call GetErrorLogs(?)', 0);
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res = array();
            if ($result->result()) {
                $res = $result->result();
            }
            $this->responseArr['status'] = 'success';
            $this->responseArr['message'] = '';
            $this->responseArr['data'] = $res;
            return $this->responseArr;
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

    /*     * *** Get recent 5 logs for notification in header  **** */

    public function getRecentAuditLogs() {
        try {
            $result1 = $this->db->query('call GetActivityLogs(?)', 1);
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            if ($result1->result()) {
                $res['RecentActivityLogs'] = $result1->result();
            }
            mysqli_next_result($this->db->conn_id);
            $result2 = $this->db->query('call GetEmailLogs(?)', 1);
            
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            if ($result2->result()) {
                $res['RecentEmailLogs'] = $result2->result();
            }
            mysqli_next_result($this->db->conn_id);
            $result3 = $this->db->query('call GetErrorLogs(?)', 1);
            
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            if ($result3->result()) {
                $res['RecentErrorLogs'] = $result3->result();
            }
            $this->responseArr['status'] = 'success';
            $this->responseArr['message'] = '';
            $this->responseArr['data'] = $res;
            return $this->responseArr;
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

}
