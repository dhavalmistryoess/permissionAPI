<?php

class EmailTemplate_model extends CI_Model {
    protected $responseArr;

    function __construct()
    {
        parent::__construct();
        $this->responseArr['status'] = 'success';
        $this->responseArr['message'] = '';
        $this->responseArr['data'] = '';
    }
    
    /***** Add/Update EmailTemplate **** */

    public function addEditEmailTemplate($post_EmailTemplate) {
        try {
            if ($post_EmailTemplate) {
                $IsActive = $post_EmailTemplate['IsActive'] == 1 ? true : false;
                $EmailTemplate_data = array(
                    'EmailTemplateId' => trim($post_EmailTemplate['EmailTemplateId']),
                    'ModuleId' => trim($post_EmailTemplate['ModuleId']),
                    'TokenId' => trim($post_EmailTemplate['TokenId']),
                    'Subject' => trim($post_EmailTemplate['Subject']),
                    'EmailBody' => trim($post_EmailTemplate['EmailBody']),
                    'ToId' => trim($post_EmailTemplate['To']),
                    'Cc' => trim($post_EmailTemplate['Cc']),
                    'Bcc' => trim($post_EmailTemplate['Bcc']),
                    'BccEmail' => trim($post_EmailTemplate['BccEmail']),
                    'UserId' => trim($post_EmailTemplate['UserId']),
                    'IsActive' => $IsActive
                );
                $res = $this->db->query('call AddUpdateEmailTemplate(?,?,?,?,?,?,?,?,?,?,?)', $EmailTemplate_data);
                mysqli_more_results($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                if ($res) {
                    $this->responseArr['status'] = 'success';
                    $this->responseArr['message'] = '';
                    $this->responseArr['data'] = true;
                    return $this->responseArr;
                } else {
                    $this->responseArr['status'] = 'fail';
                    return $this->responseArr;
                }
            } else {
                $this->responseArr['status'] = 'fail';
                return $this->responseArr;
            }
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

    /*     * *** Get EmailTemplate by EmailTemplate_id **** */

    public function getById($EmailTemplateId = Null) {
        try {
            if ($EmailTemplateId) {
                $result = $this->db->query('call GetEmailTemplateById(?)', $EmailTemplateId);
                mysqli_next_result($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $EmailTemplate_data = array();
                foreach ($result->result() as $row) {
                    $EmailTemplate_data = $row;
                }
                $this->responseArr['status'] = 'success';
                $this->responseArr['message'] = '';
                $this->responseArr['data'] = $EmailTemplate_data;
                return $this->responseArr;
            } else {
                $this->responseArr['status'] = 'fail';
                return $this->responseArr;
            }
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

    /*     * *** Get All EmailTemplates **** */

    public function getAllEmailTemplate($IsActive = Null) {
        try {
            if ($IsActive) {
                $Active = $IsActive == 1 ? 1 : 0;
                $result = $this->db->query('call GetEmailTemplates(?)', $Active);
                mysqli_next_result($this->db->conn_id);
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
            } else {
                $this->responseArr['status'] = 'fail';
                return $this->responseArr;
            }
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

    /*     * *** Get All EmailTemplates **** */

    public function getAllDefaultList() {
        try {

            $result1 = $this->db->query('call GetRoles()');
            mysqli_next_result($this->db->conn_id);
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res1 = array();
            if ($result1->result()) {
                $res1 = $result1->result();
            }
           

            $result2 = $this->db->query('call GetTokens()');
            mysqli_next_result($this->db->conn_id);
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res2 = array();
            if ($result2->result()) {
                $res2 = $result2->result();
            }
            
            $result3 = $this->db->query('call GetPlaceholders()');
            mysqli_next_result($this->db->conn_id);
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res3 = array();
            if ($result3->result()) {
                $res3 = $result3->result();
            }
            $res['Roles'] = $res1;
            $res['Tokens'] = $res2;
            $res['Placeholders'] = $res3;
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
