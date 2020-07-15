<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Configuration extends MY_Controller {

    public function __construct() {
        parent::__construct();
        //$this->auth();
        $this->load->model('Configuration_model');
        $this->load->library('responsegenerator');
    }

    /*     * *** Get All Configuration data **** */

    public function getAll_get() {
        try {
            $data = $this->Configuration_model->getAllConfigurations();
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

    /*     * *** Update All Configuration data **** */

    public function Update_post() {
        try {
            $this->auth();
            $_POST = $data = json_decode(trim(file_get_contents('php://input')), true);
            if (!empty($_POST['SMTPDetails'])) {
                foreach ($_POST['SMTPDetails'] as $key => $smtpdetails) {
                    $parameters[] = array(
                        'field' => 'SMTPDetails[' . $key . '][Value]',
                        'label' => 'SMTPDetails[' . $key . '][Value]',
                        'rules' => 'trim|required',
                        'errors' =>
                        array('required' => sprintf(IS_REQUIRED, $smtpdetails['DisplayText']))
                    );
                }
            }
            if (!empty($_POST['DocumentType'])) {
                foreach ($_POST['DocumentType'] as $key => $smtpdetails) {
                    $parameters[] = array(
                        'field' => 'DocumentType[' . $key . '][DisplayText]',
                        'label' => 'DocumentType[' . $key . '][DisplayText]',
                        'rules' => 'trim|required',
                        'errors' =>
                        array('required' => sprintf(IS_REQUIRED, $smtpdetails['Key']))
                    );
                }
            }
            if (!empty($_POST['DiscountType'])) {
                foreach ($_POST['DiscountType'] as $key => $smtpdetails) {
                    $parameters[] = array(
                        'field' => 'DiscountType[' . $key . '][DisplayText]',
                        'label' => 'DiscountType[' . $key . '][DisplayText]',
                        'rules' => 'trim|required',
                        'errors' =>
                        array('required' => sprintf(IS_REQUIRED, $smtpdetails['Key']))
                    );
                }
            }
            if (!empty($_POST['CurrencyType'])) {
                foreach ($_POST['CurrencyType'] as $key => $smtpdetails) {
                    $parameters[] = array(
                        'field' => 'CurrencyType[' . $key . '][DisplayText]',
                        'label' => 'CurrencyType[' . $key . '][DisplayText]',
                        'rules' => 'trim|required',
                        'errors' =>
                        array('required' => sprintf(IS_REQUIRED, $smtpdetails['Key']))
                    );
                }
            }
            if (!empty($_POST['ScheduleStatus'])) {
                foreach ($_POST['ScheduleStatus'] as $key => $smtpdetails) {
                    $parameters[] = array(
                        'field' => 'ScheduleStatus[' . $key . '][DisplayText]',
                        'label' => 'ScheduleStatus[' . $key . '][DisplayText]',
                        'rules' => 'trim|required',
                        'errors' =>
                        array('required' => sprintf(IS_REQUIRED, $smtpdetails['Key']))
                    );
                }
            }
            if (!empty($_POST['RegistrationType'])) {
                foreach ($_POST['RegistrationType'] as $key => $smtpdetails) {
                    $parameters[] = array(
                        'field' => 'RegistrationType[' . $key . '][DisplayText]',
                        'label' => 'RegistrationType[' . $key . '][DisplayText]',
                        'rules' => 'trim|required',
                        'errors' =>
                        array('required' => sprintf(IS_REQUIRED, $smtpdetails['Key']))
                    );
                }
            }
            if (!empty($_POST['ResultStatus'])) {
                foreach ($_POST['ResultStatus'] as $key => $smtpdetails) {
                    $parameters[] = array(
                        'field' => 'ResultStatus[' . $key . '][DisplayText]',
                        'label' => 'ResultStatus[' . $key . '][DisplayText]',
                        'rules' => 'trim|required',
                        'errors' =>
                        array('required' => sprintf(IS_REQUIRED, $smtpdetails['Key']))
                    );
                }
            }
            if (!empty($_POST['UserStatus'])) {
                foreach ($_POST['UserStatus'] as $key => $smtpdetails) {
                    $parameters[] = array(
                        'field' => 'UserStatus[' . $key . '][DisplayText]',
                        'label' => 'UserStatus[' . $key . '][DisplayText]',
                        'rules' => 'trim|required',
                        'errors' =>
                        array('required' => sprintf(IS_REQUIRED, $smtpdetails['Key']))
                    );
                }
            }
            if (!empty($_POST['lockAttempts'])) {
                foreach ($_POST['lockAttempts'] as $key => $smtpdetails) {
                    $parameters[] = array(
                        'field' => 'lockAttempts[' . $key . '][Value]',
                        'label' => 'lockAttempts[' . $key . '][DisplayText]',
                        'rules' => 'trim|required',
                        'errors' =>
                        array('required' => sprintf(IS_REQUIRED, $smtpdetails['Key']))
                    );
                }
            }
            if (!empty($_POST['lockPeriod'])) {
                foreach ($_POST['lockPeriod'] as $key => $smtpdetails) {
                    $parameters[] = array(
                        'field' => 'lockPeriod[' . $key . '][Value]',
                        'label' => 'lockPeriod[' . $key . '][DisplayText]',
                        'rules' => 'trim|required',
                        'errors' =>
                        array('required' => sprintf(IS_REQUIRED, $smtpdetails['Key']))
                    );
                }
            }

            $res = validatePostData($parameters, $_POST);
            if ($res !== true) {
                $this->responsegenerator->generateResponse($res['code'], $res['message']);
            }
           
            if ($data) {
                $result = $this->Configuration_model->updateAllConfigurations($data);
                if ($result['status'] == "success") {
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

}
