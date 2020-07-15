<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class EmailTemplate extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('EmailTemplate_model');
        $this->load->helper('common');
        $this->load->library('mylibrary');
        $this->load->library('responsegenerator');
    }

    /*     * *** Add/Update EmailTemplate **** */

    public function addUpdate_post() {
        try {
            $this->auth();
            $_POST= $post_EmailTemplate = json_decode(trim(file_get_contents('php://input')), true);
            $parameters = array(
                array(
                    'field' => 'TokenId',
                    'label' => 'TokenId',
                    'rules' => 'trim|required|is_natural_no_zero',
                    'errors' =>
                    array('required' => sprintf(IS_SELECTED, "email token"),
                          'is_natural_no_zero' => sprintf(REGEXMAX_IS_REQUIRED, 'token'))
                ),
                array(
                    'field' => 'Subject',
                    'label' => 'Subject',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "Subject"))
                ),
                array(
                    'field' => 'EmailBody',
                    'label' => 'EmailBody',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "email body"))
                ),
                 array(
                    'field' => 'UserId',
                    'label' => 'UserId',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "user id"))
                ),
                array(
                    'field' => 'To',
                    'label' => 'To',
                    'rules' => 'trim|required|is_natural_no_zero',
                    'errors' =>
                        array('required' => sprintf(IS_SELECTED, "to"),
                            'is_natural_no_zero' => sprintf(REGEXMAX_IS_REQUIRED, 'to'))
                )               
            );
            
            $res = validatePostData($parameters, $_POST);
            if ($res !== true) {
                $this->responsegenerator->generateResponse($res['code'], $res['message']);
            }
          
            if ($post_EmailTemplate) {
                $result = $this->EmailTemplate_model->addEditEmailTemplate($post_EmailTemplate);
                if ($result['status'] == "success") {
                    $this->mylibrary->deleteCache(EMAILTEMPLATE_ALL);
                    $this->response('Successful', REST_Controller::HTTP_OK);
                } elseif ($result['status'] == "exception") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $data['message']);
                } else {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                }
            } else {
                 $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
            }
        } catch (Exception $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }

    /*     * *** Get State by EmailTemplate_id **** */

    public function getById_get($EmailTemplateId = null) {
        try {
//            $this->auth();
            if (!empty($EmailTemplateId)) {
                $data = [];
                $data = $this->EmailTemplate_model->getById($EmailTemplateId);
                if ($data['status'] == "success") {
                    $this->response($data['data'], REST_Controller::HTTP_OK);
                } elseif ($data['status'] == "exception") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $data['message']);
                } else {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                }
            } else {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
            }
        } catch (Exception $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }

    /*     * *** Get All EmailTemplates **** */
    public function getAll_get($IsActive = null) {
        try {
            $this->auth();
            if (!empty($IsActive)) {
                if (!$this->cache->get(EMAILTEMPLATE_ALL)) {
                    $data = $this->EmailTemplate_model->getAllEmailTemplate($IsActive);
                } else {
                    $myArray = (json_decode($this->cache->get(EMAILTEMPLATE_ALL), true));
                    $data = getCacheData($myArray, 'IsActive', $IsActive);
                }
               
                if ($data['status'] == "success") {
                    $this->response($data['data'], REST_Controller::HTTP_OK);
                } elseif ($data['status'] == "exception") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $data['message']);
                } else {
                   $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                }
            } else {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
            }
        } catch (Exception $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }

    /*     * *** Get All EmailTemplates **** */

    public function getAllDefault_get() {
        try {
            //$this->auth();
            $data = $this->EmailTemplate_model->getAllDefaultList();
            if ($data['status'] == "success") {
                $this->response($data['data'], REST_Controller::HTTP_OK);
            } elseif ($data['status'] == "exception") {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $data['message']);
            } else {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
            }
        } catch (Exception $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }
    
    public function getAll_post() {
        try {
            $this->auth();
            $_POST = $post_EmailTemplate = json_decode(trim(file_get_contents('php://input')), true);
            $this->load->library('form_validation');
            $this->form_validation->set_rules('limit', 'limit', 'trim|required');
            $this->form_validation->set_rules('offset', 'offset', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $this->form_validation->error_array());
            }
          
            $checkParamater = ['limit', 'offset', 'searchData', 'sortOrder'];
            $checkValidParameter = checkParamter($_POST, $checkParamater);

            if(!empty($checkValidParameter)) {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $checkValidParameter);
            }
        
            $searchData = $sortOrder = '';
            if ($post_EmailTemplate['searchData'] != "") {
                $searchData = $post_EmailTemplate['searchData'];
            }

            if ($post_EmailTemplate['sortOrder'] != "") {
                $sortOrder = $post_EmailTemplate['sortOrder'];
            }

            $IsActive = ($post_EmailTemplate['searchData']['status'] != "") ? $post_EmailTemplate['searchData']['status'] : 2;
            $offset = ($post_EmailTemplate['offset'] != "") ? $post_EmailTemplate['offset'] : 0;
            $limit = ($post_EmailTemplate['limit'] != "") ? $post_EmailTemplate['limit'] : 10;
            if (!$this->cache->get(EMAILTEMPLATE_ALL)) {
              $responseArr = $this->EmailTemplate_model->getAllEmailTemplate($IsActive);
                if ($responseArr['status'] == "exception") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $data['message']);
                } elseif ($responseArr['status'] == "fail") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                }

                $myArray = $responseArr['data'];
                $this->cache->save(EMAILTEMPLATE_ALL, json_encode($responseArr['data']), CACHE_EXPIRE_TIME);
            } else {
                $myArray = (json_decode($this->cache->get(EMAILTEMPLATE_ALL), true));
            }

            $searchColumn = ['Token', 'roleTo', 'Subject', 'roleCc', 'roleBcc'];
            $data = generatePagination($myArray, $limit, $offset, $searchData, $sortOrder, $searchColumn);

            $this->response($data, REST_Controller::HTTP_OK);
        } catch (Exception $ex) {
           trigger_error($ex->getMessage(), E_USER_ERROR);
           $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }

}
