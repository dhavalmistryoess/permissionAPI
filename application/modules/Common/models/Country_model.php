<?php

class Country_model extends CI_Model {
    
    protected $responseArr;

    function __construct()
    {
        parent::__construct();
        $this->responseArr['status'] = 'success';
        $this->responseArr['message'] = '';
        $this->responseArr['data'] = '';
    }
    /*     * *** Add/Update Country **** */
    public function addEditCountry($post_Country) {
        try {
            if ($post_Country) {
                /*                 * *** Start Check same abbreviation **** */
                $this->db->select('CountryAbbreviation');
                $this->db->from('tblmstcountry');
                $this->db->where('CountryAbbreviation', strtoupper(trim($post_Country['CountryAbbreviation'])));
                if($post_Country['CountryId'] != 0) {
                     $this->db->where('CountryId !=' . $post_Country['CountryId']);
                }
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
                /*                 * *** End Check same abbreviation **** */
                $IsActive = $post_Country['IsActive'] == 1 ? true : false;
                $country_data = array(
                    'CountryId' => trim($post_Country['CountryId']),
                    'CountryName' => trim($post_Country['CountryName']),
                    'CountryAbbreviation' => strtoupper(trim($post_Country['CountryAbbreviation'])),
                    'PhonePrefix' => trim($post_Country['PhonePrefix']),
                    'UserId' => trim($post_Country['UserId']),
                    'IsActive' => $IsActive
                );
                $res = $this->db->query('call AddUpdateCountry(?,?,?,?,?,?)', $country_data);
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

    /*     * *** Get Country by country_id **** */

    public function getById($countryId = Null) {
        try {
            if ($countryId) {
                $result = $this->db->query('call GetCountryById(?)', $countryId);
                mysqli_next_result($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $country_data = array();
                foreach ($result->result() as $row) {
                    $country_data = $row;
                }
                
                $this->responseArr['status'] = 'success';
                $this->responseArr['message'] = '';
                $this->responseArr['data'] = $country_data;
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

    /*     * *** Get All Countries **** */

    public function getAllCountry($IsActive = Null) {
        try {
            if ($IsActive) {
                $Active = $IsActive == 1 ? 1 : 0;
                $result = $this->db->query('call GetCountries(?)', $Active);
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
    
    public function fetch_data($limit, $offset, $searchCondition, $sortOrder, $count = 'no') {
         $offset = ($limit != "" ) ? $limit * ($offset - 1) : 0;
        
        if($searchCondition['searchQuery'] != "") {
            $this->db->or_like('CountryName', $searchCondition['searchQuery']);
            $this->db->or_like('CountryAbbreviation', $searchCondition['searchQuery']);
            $this->db->or_like('PhonePrefix', $searchCondition['searchQuery']);
        }
        if($searchCondition['status']) {
            $this->db->where('IsActive', $searchCondition['status']);
        }
        if (is_array($sortOrder)) {
            foreach ($sortOrder as $key => $val) {
                $this->db->order_by($val['field'], $val['dir']);
            }
        }
        $query = $this->db->get("tblmstcountry", $limit, $offset);
        if ($count == 'yes') {
            return $query->num_rows();
        } else {
            if ($query->num_rows() > 0) {
                return $query->result_array();
            } else {
                return false;
            }
        }
    }

}
