<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends MY_Controller {

    public function __construct() {
        parent::__construct();
        //$this->auth();
        $this->load->model('Common_model');
        $this->load->library('responsegenerator');
    }

    /*     * *** Common IsActive changes method for all **** */

    public function isActiveChange_post() {
        try {
            $post_data = json_decode(trim(file_get_contents('php://input')), true);
            if ($post_data) {
                $result = $this->Common_model->isActiveChange($post_data);
                if ($result['status'] == "success") {
                    $this->response([
                        'message' => 'Successful changes'
                            ], REST_Controller::HTTP_OK);
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

    /*     * *** Common Toggle Value changes method for all **** */

    public function toggleValueChange_post() {
        try {
            $post_data = json_decode(trim(file_get_contents('php://input')), true);
            if ($post_data) {
                $result = $this->Common_model->toggleValueChange($post_data);
                if ($result['status'] == "success") {
                    $this->response([
                        'message' => 'Successful changes'
                            ], REST_Controller::HTTP_OK);
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

    /*     * *** Common Delete method for all **** */

    public function delete_post() {
        try {
            $post_data = json_decode(trim(file_get_contents('php://input')), true);
            if ($post_data) {
                if ($post_data['Id'] > 0) {
                    $result = $this->Common_model->deleteItem($post_data);
                    if ($result['status'] == "success") {
                        $this->response([
                            'message' => 'Delete successfully'
                                ], REST_Controller::HTTP_OK);
                    } elseif ($result['status'] == "exception") {
                        $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $data['message']);
                    } else {
                        $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                    }
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

    public function getAllRoles_get() {
        try {
            $data = $this->Common_model->getAllRoles();
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
