<?php

class Common_model extends CI_Model {

    protected $responseArr;

    function __construct() {
        parent::__construct();
        $this->load->library('mylibrary');
        $this->responseArr['status'] = 'success';
        $this->responseArr['message'] = '';
        $this->responseArr['data'] = '';
    }

    /*     * *** Common IsActive changes method for all **** */

    public function isActiveChange($post_data) {
        try {
            if ($post_data) {
                if (trim($post_data['IsActive']) == 1) {
                    $IsActive = true;
                } else {
                    $IsActive = false;
                }
                $data = array(
                    'IsActive' => $IsActive,
                    'UpdatedBy' => trim($post_data['UpdatedBy']),
                    'TableName' => trim($post_data['TableName']),
                    'Id' => trim($post_data['Id']),
                    'FieldName' => trim($post_data['FieldName']),
                    'Module' => trim($post_data['Module']),
                    'ModuleId' => isset($post_data['ModuleId']) ? trim($post_data['ModuleId']) : 0
                );
                $res = $this->db->query('call ActiveChange(?,?,?,?,?,?,?)', $data);
                mysqli_more_results($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $this->removeCache(trim($post_data['Module']));
              
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

    /*     * *** Common Toggle Value changes method for all **** */

    public function toggleValueChange($post_data) {
        try {
            if ($post_data) {
                if (trim($post_data['fieldValue']) == 1) {
                    $fieldValue = true;
                } else {
                    $fieldValue = false;
                }
                $data = array(
                    'fieldValue' => $fieldValue,
                    'field' => trim($post_data['field']),
                    'UpdatedBy' => trim($post_data['UpdatedBy']),
                    'TableName' => trim($post_data['TableName']),
                    'Id' => trim($post_data['Id']),
                    'FieldName' => trim($post_data['FieldName']),
                    'Module' => trim($post_data['Module']),
                    'ModuleId' => isset($post_data['ModuleId']) ? trim($post_data['ModuleId']) : 0
                );
                $res = $this->db->query('call ToggleValueChange(?,?,?,?,?,?,?,?)', $data);
                mysqli_more_results($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                
                $this->removeCache(trim($post_data['Module']));
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

    /*     * *** Common Delete method for all **** */

    public function deleteItem($post_data) {
        try {
            if ($post_data) {
                $data = array(
                    'Id' => trim($post_data['Id']),
                    'UserId' => trim($post_data['UserId']),
                    'TableName' => trim($post_data['TableName']),
                    'FieldName' => trim($post_data['FieldName']),
                    'Module' => trim($post_data['Module']),
                    'ModuleId' => isset($post_data['ModuleId']) ? trim($post_data['ModuleId']) : 0
                );
                $res = $this->db->query('call DeleteItem(?,?,?,?,?,?)', $data);
                mysqli_more_results($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                
                $this->removeCache(trim($post_data['Module']));
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

    public function getAllRoles() {
        try {
            $result = $this->db->query('call GetRoles()');
            mysqli_more_results($this->db->conn_id);
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
    
    
    function removeCache($moduleName) {
        switch ($moduleName) {
            case "Country":
                $this->mylibrary->deleteCache(COUNTRY_ALL);
                break;
            
            case "Email Template":
                $this->mylibrary->deleteCache(EMAILTEMPLATE_ALL);
                break;
            
            case "State":
                $this->mylibrary->deleteCache(STATES_ALL);
                break;
            
            case "Item":
                $this->mylibrary->deleteCache(ITEM_ALL);
                break;
            
            case "Category":
                $this->mylibrary->deleteCache(CATEGORY_ALL);
                break;
            
            case "Discount Coupon":
                $this->mylibrary->deleteCache(DISCOUNT_ALL);
                break;
            
            case "Certificate":
                $this->mylibrary->deleteCache(CERTIFICATE_ALL);
                break;
            
            case "Document":
                $this->mylibrary->deleteCache(DOCUMENT_ALL);
                break;
        }
    }

}
