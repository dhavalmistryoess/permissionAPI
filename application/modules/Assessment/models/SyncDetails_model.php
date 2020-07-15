<?php

class SyncDetails_model extends CI_Model {

    protected $responseArr;

    function __construct() {
        parent::__construct();
        $this->load->library('mylibrary');
        $this->load->helper('common');
        $this->responseArr['status'] = 'success';
        $this->responseArr['message'] = '';
        $this->responseArr['data'] = '';
    }

    /*     * *** Add/Update Department **** */

    public function insertRecord($moduleItem) {
        try {

            $query = $this->db->get_where('tblmodules', ['ModuleName' => $moduleItem['ModuleName'], 
                                       'ClassName' => $moduleItem['ClassName'],
                                        'FunctionName' => $moduleItem['FunctionName']]);
            $result = $query->result_array();
            if (empty($result)) {
                $this->db->insert('tblmodules', $moduleItem);
                $getLastInsertID = $this->db->insert_id();
                
                $this->insertPermission($getLastInsertID);
                
            }
            else {
                $this->db->where('ModuleId', $result[0]['ModuleId']); 
                $this->db->update('tblmodules', $moduleItem); 
            }
           
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }
    
    function insertPermission($moduleID) {
        $result = $this->db->query('call GetRoles()');
        mysqli_next_result($this->db->conn_id);
        $db_error = $this->db->error();
        if (!empty($db_error) && $db_error['message'] != "") {
            throw new Exception($db_error['message']);
        }
        $res = array();
        if ($result->result()) {
            $res = $result->result();
        }
        $i = 0;
        foreach($res as $roleDetails) {
            $data[$i]['ModuleID'] = $moduleID;
            $data[$i]['RoleID'] = $roleDetails->RoleId;
            $data[$i]['HasAccess'] = ($roleDetails->RoleId == 1) ? 1 : 2;
            $data[$i]['CreatedOn'] = 1;
            $data[$i]['UpdatedOn'] = 1;
            $i++;
        }
        $this->db->insert_batch('tblpermission', $data); 
        
    }


    public function getPermissionByRole($roleID) {
        try {
            $this->db->select(['tblpermission.RoleId', 'tblmodules.DisplayName', 'GROUP_CONCAT(DISTINCT tblmodules.ModuleID) As ModuleIDs' ,'tblpermission.HasAccess', 'tblmodules.ClassName']);
            $this->db->from('tblpermission');
            $this->db->join('tblmodules', 'tblpermission.ModuleID = tblmodules.ModuleID');
            $this->db->group_by(['tblmodules.DisplayName', 'tblmodules.ClassName', 'tblpermission.RoleId']);
            $this->db->where(['tblpermission.RoleId' => $roleID , 'tblmodules.DisplayName !=' => null]);

            $query = $this->db->get();
            $result = $query->result_array();
            $permissionArray = array();

            foreach($result as $permissionKey) {
                $permissionArray[$permissionKey['ClassName']][] = $permissionKey;

            }
            $this->responseArr['status'] = 'success';
            $this->responseArr['data'] = $permissionArray;
            return $this->responseArr;
        } catch (Exception $ex) {
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }
    
    public function addPermission($permissionArr) {
        try {
            $getSessionData = $this->session->userdata('userDetails');
            $i = 0;
            foreach ($permissionArr['Permission'] as $permission) {
                $moduleKey = explode(",", $permission['ModuleIDs']);
                foreach ($moduleKey as $moduleID) {
                    $assignArr[$i]['ModuleID'] = $moduleID;
                    $assignArr[$i]['RoleID'] = $permission['RoleId'];
                    $assignArr[$i]['HasAccess'] = $permission['HasAccess'];
                    $assignArr[$i]['CreatedOn'] = $getSessionData['UserId'];
                    $assignArr[$i]['UpdatedOn'] = $getSessionData['UserId'];
                    $i++;
                }
            }
            // delete into permission Table
            $roleID = array_unique(array_column($permissionArr['Permission'], 'RoleId'));
            $this->db->where_in('RoleId', $roleID);
            $this->db->delete('tblpermission');

            // insert into permission Table
            $this->db->insert_batch('tblpermission', $assignArr);
            $this->responseArr['status'] = 'success';
            $this->responseArr['data'] = true;
            return $this->responseArr;
        } catch (Exception $ex) {
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

}
