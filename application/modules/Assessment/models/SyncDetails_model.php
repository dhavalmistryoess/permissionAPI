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
            $data[$i]['HasAccess'] = 1;
            $data[$i]['CreatedOn'] = 1;
            $data[$i]['UpdatedOn'] = 1;
            $i++;
        }
        
        $this->db->insert_batch('tblpermission', $data); 
        
    }


    public function getPermissionByRole($roleID) {
        try {
            $this->db->select(['tblpermission.RoleId', 'tblmodules.Slug', 'tblmodules.DisplayName', 'GROUP_CONCAT(DISTINCT tblmodules.ModuleID) As ModuleIDs' ,'tblpermission.HasAccess', 'tblmodules.ClassName']);
            $this->db->from('tblpermission');
            $this->db->join('tblmodules', 'tblpermission.ModuleID = tblmodules.ModuleID');
            $this->db->group_by(['tblmodules.Slug', 'tblmodules.ClassName', 'tblpermission.RoleId']);
            $this->db->where(['tblpermission.RoleId' => $roleID , 'tblmodules.Slug !=' => null]);

            $query = $this->db->get();
            $result = $query->result_array();
            $permissionArray = $permissionName = array();

            foreach($result as $permissionKey) {
                if(!in_array($permissionKey['ClassName'], $permissionName)) {
                    array_push($permissionName, $permissionKey['ClassName']);
                    $key = array_search($permissionKey['ClassName'], $permissionName);
                } else {
                     $key = array_search($permissionKey['ClassName'], $permissionName);
                }
                $permissionArray[$key]['key'] = $permissionKey['ClassName'];
                $permissionArray[$key][$permissionKey['ClassName']][] = $permissionKey;

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
            foreach ($permissionArr as $permission) {
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
            $roleID = array_unique(array_column($permissionArr, 'RoleId'));
            $this->db->where_in('RoleId', $roleID);
            $this->db->delete('tblpermission');
            
           
            $this->mylibrary->deleteCache(PERMISSION_ALL."_".implode(",", $roleID));
            $this->mylibrary->deleteCache(ROLE_PERMISSION."_".implode(",", $roleID));
            // insert into permission Table
            $this->db->insert_batch('tblpermission', $assignArr);
            
            //get Permission By UserName
            $this->responseArr['status'] = 'success';
            $this->responseArr['data'] = true;
            return $this->responseArr;
        } catch (Exception $ex) {
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }
    public function getPermission($roleID) {
        try {
            $this->db->select(['tblmodules.Slug','tblpermission.HasAccess']);
            $this->db->from('tblpermission');
            $this->db->join('tblmodules', 'tblpermission.ModuleID = tblmodules.ModuleID');
            $this->db->group_by(['tblmodules.Slug', 'tblmodules.ClassName', 'tblpermission.RoleId']);
            $this->db->where(['tblpermission.RoleId' => $roleID , 'tblmodules.Slug !=' => null]);

            $query = $this->db->get();
            $result = $query->result_array();
            $this->responseArr['status'] = 'success';
            $this->responseArr['data'] = $result;
            return $this->responseArr;
        } catch (Exception $ex) {
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }
    public function getPermissionByKey($permissionKey = "") {
        try {
            $this->db->select(['tblpermission.RoleId','tblmodules.DisplayName', 'tblmodules.Slug', 'GROUP_CONCAT(DISTINCT tblmodules.ModuleID) As ModuleIDs' ,'tblpermission.HasAccess', 'tblmodules.ClassName']);
            $this->db->from('tblpermission');
            $this->db->join('tblmodules', 'tblpermission.ModuleID = tblmodules.ModuleID');
            $this->db->group_by(['tblmodules.Slug', 'tblmodules.ClassName']);
            $this->db->order_by('tblmodules.Slug', 'ASC');
            if($permissionKey != "") {
                $this->db->where(['tblmodules.Slug' => $permissionKey]);
            } else {
                $this->db->where(['tblmodules.Slug !=' => null]);
            }
            $query = $this->db->get();
            $result =($permissionKey != "")  ? $query->row() : $query->result_array();
            $this->responseArr['status'] = 'success';
            $this->responseArr['data'] = $result;
            return $this->responseArr;
        } catch (Exception $ex) {
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }
    
    public function updatePermission($permissionArr) {
        try {
             $this->db->where(['DisplayName' => $permissionArr['DisplayName'], 'Slug !=' => $permissionArr['Slug']]);
            $query = $this->db->get('tblmodules');
            if ($query->num_rows() > 0){
                $status = false;
            }
            else{
                $this->db->where('Slug', $permissionArr['Slug']);
                $this->db->update('tblmodules', ['DisplayName' => $permissionArr['DisplayName']]);
                $status = true;
                $this->mylibrary->deleteCache(PERMISSION_ALL);
                $getFileDetails = array_diff(scandir('application/cache'), array('.', '..'));
                if(!empty($getFileDetails)) {
                    $permissionArray =  preg_grep("/^permission.*/", $getFileDetails);
                    foreach($permissionArray as $permission) {
                        $this->mylibrary->deleteCache($permission);
                    }
                }
              
            }
        
            //get Permission By UserName
            $this->responseArr['status'] = 'success';
            $this->responseArr['data'] = $status;
            return $this->responseArr;
        } catch (Exception $ex) {
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }
    
    
    
    

}
