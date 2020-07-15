<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class State extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('State_model');
        $this->load->helper('common');
        $this->load->library('mylibrary');
        $this->load->library('responsegenerator');
    }

    /*     * *** Add/Update State **** */

    public function addUpdate_post() {
        try {
            $this->auth();
            $_POST = $post_State = json_decode(trim(file_get_contents('php://input')), true);
            $is_unique = (isset($_POST['StateId']) && $_POST['StateId'] == 0) ? '|is_unique[tblmststate.StateAbbreviation]' : '';
            $parameters = array(
                array(
                    'field' => 'StateName',
                    'label' => 'StateName',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "state name"))
                ),
                array(
                    'field' => 'UserId',
                    'label' => 'UserId',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "user id"))
                ),
                array(
                    'field' => 'StateAbbreviation',
                    'label' => 'StateAbbreviation',
                    'rules' => 'trim|required'.$is_unique,
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "state abbrevation"),
                           'is_unique' =>  sprintf(ALREADY_EXIST, "State abbrevation"))
                ),
                array(
                    'field' => 'CountryId',
                    'label' => 'CountryId',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_SELECTED, "country"))
                )
            );
            $res = validatePostData($parameters, $_POST);
            if ($res !== true) {
                $this->responsegenerator->generateResponse($res['code'], $res['message']);
            }
            if ($post_State) {
                $result = $this->State_model->addEditState($post_State);
                if ($result['status'] == "success" && $result['data'] == 'Successful') {
                     $this->mylibrary->deleteCache(STATES_ALL);
                    $this->response($result['data'], REST_Controller::HTTP_OK);
                } elseif ($result['status'] == "success" && $result['data'] == 'Duplicate Entry') {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_FOUND, sprintf(ALREADY_EXIST, 'State Abbreviation'));
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

    /*     * *** Get State by state_id **** */

    public function getById_get($StateId = null) {
        try {
            $this->auth();
            if (!empty($StateId)) {
                $data = [];
                $data = $this->State_model->getById($StateId);
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

    /*     * *** Get All States **** */

    public function getAll_get($IsActive = null) {
        try {
            $this->auth();
            if (!empty($IsActive)) {
                if (!$this->cache->get(STATES_ALL)) {
                    $data = $this->State_model->getAllState($IsActive);
                } else {
                    $myArray = (json_decode($this->cache->get(STATES_ALL), true));
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

    
    /*     * *** Get All States **** */

    public function getAll_post() {
        try {
            $this->auth();
            $_POST = $post_State = json_decode(trim(file_get_contents('php://input')), true);
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
            if ($post_State['searchData'] != "") {
                $searchData = $post_State['searchData'];
            }

            if ($post_State['sortOrder'] != "") {
                $sortOrder = $post_State['sortOrder'];
            }

            $IsActive = ($post_State['searchData']['status'] != "") ? $post_State['searchData']['status'] : 2;
            $offset = ($post_State['offset'] != "") ? $post_State['offset'] : 0;
            $limit = ($post_State['limit'] != "") ? $post_State['limit'] : 10;
            if (!$this->cache->get(STATES_ALL)) {
              $responseArr = $this->State_model->getAllState($IsActive);
                if ($responseArr['status'] == "exception") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $responseArr['message']);
                } elseif ($responseArr['status'] == "fail") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                }

                $myArray = $responseArr['data'];
                $this->cache->save(STATES_ALL, json_encode($responseArr['data']), CACHE_EXPIRE_TIME);
            } else {
                $myArray = (json_decode($this->cache->get(STATES_ALL), true));
            }

            $searchColumn = ['CountryName', 'StateName', 'StateAbbreviation'];
            $data = generatePagination($myArray, $limit, $offset, $searchData, $sortOrder, $searchColumn);

            $this->response($data, REST_Controller::HTTP_OK);
        } catch (Exception $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }
}
