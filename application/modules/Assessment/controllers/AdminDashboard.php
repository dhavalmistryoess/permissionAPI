<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AdminDashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AdminDashboard_model');
        $this->load->library('responsegenerator');
    }

    /*     * *** Get Department by Department_id **** */

    public function getDetails_get() {
        //$this->auth();
        try {
            $data = [];
            $data = $this->AdminDashboard_model->getDetails();
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

    public function getProctoringDetails_get() {
        try {
            $data = [];
            $data = $this->AdminDashboard_model->getProctoringDetails();
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

    public function getFilterDefaultData_get() {
        try {
            $data = [];
            $data = $this->AdminDashboard_model->getFilterDefaultData();
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

    public function filterProctorDetails_post() {
        try {
           $post_Data = json_decode(trim(file_get_contents('php://input')), true);
            $data = $this->AdminDashboard_model->filterProctorDetails($post_Data);
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

}
