<?php

class State_model extends CI_Model {
    protected $responseArr;

    function __construct()
    {
        parent::__construct();
        $this->responseArr['status'] = 'success';
        $this->responseArr['message'] = '';
        $this->responseArr['data'] = '';
    }
    /*     * *** Add/Update Country **** */
    public function addEditState($post_State) {
        try {
            if ($post_State) {
                /*                 * *** Start Check same abbreviation **** */
                if (trim($post_State['StateId']) == 0) {
                    $this->db->select('StateName');
                    $this->db->from('tblmststate');
                    $this->db->where('StateName', strtoupper(trim($post_State['StateName'])));
                    $this->db->limit(1);
                    $query = $this->db->get();
                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
                    }
                    if ($query->num_rows() == 1) {
                        $this->responseArr['status'] = 'success';
                        $this->responseArr['message'] = '';
                        $this->responseArr['data'] = 'Duplicate Entry';
                        return $this->responseArr;
                    }
                } else {
                    $this->db->select('StateName');
                    $this->db->from('tblmststate');
                    $this->db->where('StateId!=', $post_State['StateId']);
                    $this->db->where('StateName', strtoupper(trim($post_State['StateName'])));
                    $this->db->limit(1);
                    $query = $this->db->get();
                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
                    }
                    if ($query->num_rows() == 1) {
                        $this->responseArr['status'] = 'success';
                        $this->responseArr['message'] = '';
                        $this->responseArr['data'] = 'Duplicate Entry';
                        return $this->responseArr;
                    }
                }

                /*                 * *** End Check same abbreviation **** */
                $IsActive = $post_State['IsActive'] == 1 ? true : false;
                $state_data = array(
                    'StateId' => trim($post_State['StateId']),
                    'CountryId' => trim($post_State['CountryId']),
                    'StateName' => trim($post_State['StateName']),
                    'StateAbbreviation' => strtoupper(trim($post_State['StateAbbreviation'])),
                    'UserId' => trim($post_State['UserId']),
                    'IsActive' => $IsActive
                );
                $res = $this->db->query('call AddUpdateState(?,?,?,?,?,?)', $state_data);
                mysqli_more_results($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                if ($res) {
                    $this->responseArr['status'] = 'success';
                    $this->responseArr['message'] = '';
                    $this->responseArr['data'] = 'Successful';
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

    /*     * *** Get State by state_data **** */

    public function getById($StateId = Null) {
        try {
            if ($StateId) {
                $result = $this->db->query('call GetStateById(?)', $StateId);
                mysqli_next_result($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $state_data = array();
                foreach ($result->result() as $row) {
                    $state_data = $row;
                }

                $this->responseArr['status'] = 'success';
                $this->responseArr['message'] = '';
                $this->responseArr['data'] = $state_data;
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

    /*     * *** Get All States **** */

    public function getAllState($IsActive = Null) {
        try {
            if ($IsActive) {
                $Active = $IsActive == 1 ? 1 : 0;
                $result = $this->db->query('call GetStates(?)', $Active);
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

}
