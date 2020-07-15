<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Country extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Country_model');
        $this->load->helper('common');
        $this->load->library('mylibrary');
        $this->load->library('responsegenerator');
    }

    /*     * *** Add/Update Country **** */

    public function addUpdate_post() {
        try {
            $this->auth();
            $_POST = $post_Country = json_decode(trim(file_get_contents('php://input')), true);
            $is_unique = (isset($_POST['CountryId']) && $_POST['CountryId'] == 0) ? '|is_unique[tblmstcountry.CountryAbbreviation]' : '';
            $parameters = array(
                array(
                    'field' => 'CountryName',
                    'label' => 'CountryName',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "country"))
                ),
                array(
                    'field' => 'UserId',
                    'label' => 'UserId',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "user id"))
                ),
                array(
                    'field' => 'CountryAbbreviation',
                    'label' => 'CountryAbbreviation',
                    'rules' => 'trim|required'.$is_unique,
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "country abbrevation"),
                           'is_unique' =>  sprintf(ALREADY_EXIST, "Country abbrevation"))
                ),
                array(
                    'field' => 'PhonePrefix',
                    'label' => 'PhonePrefix',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "phone prefix"))
                )
            );
            $res = validatePostData($parameters, $_POST);
            if ($res !== true) {
                $this->responsegenerator->generateResponse($res['code'], $res['message']);
            }

            if ($post_Country) {
                $result = $this->Country_model->addEditCountry($post_Country);
                if ($result['status'] == "success" && $result['data'] == 'Successful') {
                    $this->mylibrary->deleteCache(COUNTRY_ALL);
                    $this->response($result['data'], REST_Controller::HTTP_OK);
                } elseif ($result['status'] == "success" && $result['data'] == 'Duplicate Entry') {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_FOUND, sprintf(ALREADY_EXIST, "Country abbreviation"));
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

    /*     * *** Get Country by country_id **** */

    public function getById_get($countryId = null) {
        try {
            //$this->auth();	
            if (!empty($countryId)) {
                $data = [];
                $data = $this->Country_model->getById($countryId);
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

    /*     * *** Get All Countries **** */

    public function getAll_get($IsActive = null) {
        try {
      
//            $this->auth();
            if (!empty($IsActive)) {
                if (!$this->cache->get(COUNTRY_ALL)) {
                    $data = $this->Country_model->getAllCountry($IsActive);
                } else {
                    $myArray = (json_decode($this->cache->get(COUNTRY_ALL), true));
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
    
    public function getCountry_post() {
        try {
            $this->auth();
            $_POST = $post_Country = json_decode(trim(file_get_contents('php://input')), true);
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
            if ($post_Country['searchData'] != "") {
                $searchData = $post_Country['searchData'];
            }

            if ($post_Country['sortOrder'] != "") {
                $sortOrder = $post_Country['sortOrder'];
            }

            $IsActive = ($post_Country['searchData']['status'] != "") ? $post_Country['searchData']['status'] : 2;
            $offset = ($post_Country['offset'] != "") ? $post_Country['offset'] : 0;
            $limit = ($post_Country['limit'] != "") ? $post_Country['limit'] : 10;
            if (!$this->cache->get(COUNTRY_ALL)) {
                $responseArr = $this->Country_model->getAllCountry($IsActive);
                if ($responseArr['status'] == "exception") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $responseArr['message']);
                } elseif ($responseArr['status'] == "fail") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                }

                $myArray = $responseArr['data'];
                $this->cache->save(COUNTRY_ALL, json_encode($responseArr['data']), CACHE_EXPIRE_TIME);
            } else {
                $myArray = (json_decode($this->cache->get(COUNTRY_ALL), true));
            }
            
            $searchColumn = ['CountryName', 'CountryAbbreviation', 'PhonePrefix'];
            $data = generatePagination($myArray, $limit, $offset, $searchData, $sortOrder, $searchColumn);
            $this->response($data, REST_Controller::HTTP_OK);
        } catch (Exception $ex) {
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }
 }
