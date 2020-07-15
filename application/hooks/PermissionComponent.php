<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST_Controller.php';
require_once APPPATH . '/libraries/JWT.php';
require_once APPPATH . '/libraries/BeforeValidException.php';
require_once APPPATH . '/libraries/ExpiredException.php';
require_once APPPATH . '/libraries/SignatureInvalidException.php';
use \Firebase\JWT\JWT;

class PermissionComponent extends MX_Controller {
    
    protected $allowedWithOutLogin = [
        'Auth' => [
            'login'
        ],
        'Password' => [
            'forgot'
        ],
        'SyncDetails' => [
            'getClassName', 'getRolePermission'
        ]
      
    ];
   
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('responsegenerator');
        $this->load->library('mylibrary');
        $this->load->helper('common');
        
    }
    
    public function checkPermisssion() {
        if (!$this->checkWithoutLogin()) {
            try {
                
                // load Model
                $this->load->model('Auth_model');
                $headers = $this->input->get_request_header('Authorization');
                $key = $this->config->item('thekey');

                // get JWT Token
                $decoded = JWT::decode($headers, $key, array('HS256'));
                $where_query = array('EmailAddress' => $decoded->EmailAddress, 'UserId' => $decoded->UserId); //For where query condition
                $val = $this->Auth_model->get_user($where_query)->row(); //Model to get single data row from database base on username
                if ($this->Auth_model->get_user($where_query)->num_rows() == 0) {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND,$invalidLogin);
                }
                
                if (!$this->session->userdata('userDetails')) {
                     $this->session->set_userdata('userDetails', (array)$decoded);
                } 
                
                //get Data from the permission table
                if($val->RoleId != 1) {
                    $this->checkPermission($val->RoleId); 
                }
            } catch (Exception $e) {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $e->getMessage());
            }
        }
    }

    function checkWithoutLogin(){
        $className = $this->uri->segment(2);
        $functionName = $this->uri->segment(3);
       
        if(in_array(strtolower($className), array_map('strtolower',array_keys($this->allowedWithOutLogin)))) {
            $mergeArray = call_user_func_array('array_merge', $this->allowedWithOutLogin);
            $finalArray = array_map('strtolower',array_values($mergeArray));
            if(in_array(strtolower($functionName), $finalArray)) {
                return true;
            }
        }
        return false;
    }
    
    function checkPermission($roleID) {
        $className = $this->uri->segment(2);
        $functionName = $this->uri->segment(3);
        if (!$this->cache->get(PERMISSION_ALL)) {
            $this->db->select(['tblpermission.RoleId', 'tblpermission.HasAccess', 'tblmodules.ClassName', 'tblmodules.FunctionName']);
            $this->db->from('tblpermission');
            $this->db->join('tblmodules', 'tblpermission.ModuleID = tblmodules.ModuleID');
            $this->db->where(['tblmodules.ClassName' => strtolower($className),
                'tblmodules.FunctionName' => strtolower($functionName),
                'tblpermission.RoleId' => $roleID, 'tblpermission.HasAccess' => 1]);
            $query = $this->db->get();
            $result = $query->row();
        } else {
            $getCachePermission = (json_decode($this->cache->get(PERMISSION_ALL), true));
            $result = array_keys(array_map('nestedLowercase', $getCachePermission), ['RoleId' => $roleID, 'FunctionName' => strtolower($functionName), 'ClassName' => strtolower($className), 'HasAccess' => 1]);
        }
       if (empty($result)) {
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_FORBIDDEN, YOU_DONT_HAVE_PERMISSION);
       }
    }
   

}