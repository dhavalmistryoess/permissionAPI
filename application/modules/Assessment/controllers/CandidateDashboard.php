<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CandidateDashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('CandidateDashboard_model');
        $this->load->helper('common');
        $this->load->library('responsegenerator');
        
    }

    /*     * *** Add/Update Department **** */

    public function ScheduleAssessment_post() {
        try {
            $this->auth();
            $_POST = $post_Schedule = json_decode(trim(file_get_contents('php://input')), true);
            $parameters = array(
                array(
                    'field' => 'AvailablePriorityDate1',
                    'label' => 'AvailablePriorityDate1',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "schedule date1"))
                ),
                array(
                    'field' => 'AvailablePriorityDate2',
                    'label' => 'AvailablePriorityDate2',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "schedule date2"))
                ),
                array(
                    'field' => 'AvailablePriorityDate3',
                    'label' => 'AvailablePriorityDate3',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "schedule date3"))
                ),
                array(
                    'field' => 'UserId',
                    'label' => 'UserId',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "user id"))
                ),
                array(
                    'field' => 'CertificateId',
                    'label' => 'CertificateId',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "certificate id"))
                ),
                array(
                    'field' => 'UserCertificateId',
                    'label' => 'UserCertificateId',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "user cerificate id"))
                )
            );
            $res = validatePostData($parameters, $_POST);
            if ($res !== true) {
                $this->responsegenerator->generateResponse($res['code'], $res['message']);
            }
          
            if ($post_Schedule) {
                $result = $this->CandidateDashboard_model->ScheduleAssessment($post_Schedule);
                if ($result['status'] == "success") {
                    if ($result['data'] == "true") {
                        //$this->response('Successful', REST_Controller::HTTP_OK);
                        $this->db->select('tu.UserId, tu.FirstName, tu.LastName, tu.EmailAddress, tu.RoleId');
                        $this->db->where('tu.RoleId', 1);
                        $data = $this->db->get('tblusers as tu');
                        $res1 = $data->result()[0];
                        $UserId = $res1->UserId;
                        $res = new stdClass();
                        $res->UserName = $post_Schedule['FirstName'] . " " . $post_Schedule['LastName'];
                        $res->CertificateName = $post_Schedule['CertificateName'];
                        $res->AvailablePriorityDate1 = $post_Schedule['AvailablePriorityDate1'];
                        $res->AvailablePriorityDate2 = $post_Schedule['AvailablePriorityDate2'];
                        $res->AvailablePriorityDate3 = $post_Schedule['AvailablePriorityDate3'];
                        //$res->CertificateName = $post_Schedule['CertificateName'];
                        $res->loginUrl = BASE_URL . '' . $post_Schedule["LoginURL"];
                        $res->RoleId = $res1->RoleId;
                        $res->CreatedBy = $post_Schedule['UserId'];
                        $res->ActionURL = "admin/assessment-list";
                        $res->NotificationText = 'Schedule assessment request for the certificate "'.$res->CertificateName.'" by '.$res->UserName;
                        $EmailToken = 'Schedule Assessment';
                        $send = sendEmailFunction($EmailToken, $UserId, $res,'',true);
                        if ($send) {
                            $this->response('Successful', REST_Controller::HTTP_OK);
                        } else {
                            $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                        }
                    } else {
                        $this->response($result['data'], REST_Controller::HTTP_OK);
                    }
                } elseif ($result['status'] == "exception") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $result['message']);
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

    /*     * *** Get Department by Department_id **** */

    public function getById_get($UserId = null) {
        try {
            //$this->auth();
            if (!empty($UserId)) {
                $data = [];
                $responce = $this->CandidateDashboard_model->getById($UserId);
                mysqli_more_results($this->db->conn_id);
                $responce1 = $this->CandidateDashboard_model->getAllDefaultList($UserId);
                mysqli_more_results($this->db->conn_id);
                $responce2 = $this->CandidateDashboard_model->getAllCertificateList($UserId);
                if ($responce['status'] == "success" && $responce1['status'] == "success" && $responce2['status'] == "success") {
                    $data['certificateList'] = $responce['data'];
                    $data['defalut'] = $responce1['data'];
                    $data['certificate'] = $responce2['data'];
                    $this->response($data, REST_Controller::HTTP_OK);
                } else if ($responce['status'] == "exception" || $responce1['status'] == "exception" || $responce2['status'] == "exception") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $responce['message'] . "" . $responce1['message'] . "" . $responce2['message']);
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

    /*     * *** Get Department by Department_id **** */

    public function getCertificateDetailsById_get($UserCertificateId = null) {
        try {
            //$this->auth();
            if (!empty($UserCertificateId)) {
                $data = [];
                $responce = $this->CandidateDashboard_model->getCertificateDetailsById($UserCertificateId);
                $responce1 = $this->CandidateDashboard_model->getAppointmentletter($UserCertificateId);
                if ($responce['status'] == "success" && $responce1['status'] == "success") {
                    $data['CertificateDetails'] = $responce['data'];
                    $data['Appointmentletter'] = $responce1['data'];
                    $this->response($data, REST_Controller::HTTP_OK);
                } else if ($responce['status'] == "exception" || $responce1['status'] == "exception") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $responce['message'] . "" . $responce1['message']);
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

    public function CancelAssessment_post() {
        try {
            //$this->auth();
            $post_Assessment = json_decode(trim(file_get_contents('php://input')), true);
            if ($post_Assessment) {
                $result = $this->CandidateDashboard_model->CancelAssessment($post_Assessment);
                if ($result['status'] == "success") {
                    $this->db->select('tu.UserId, tu.FirstName, tu.LastName, tu.EmailAddress, tu.RoleId');
                    $this->db->where('tu.RoleId', 1);
                    $data = $this->db->get('tblusers as tu');
                    $res1 = $data->result()[0];
                    $UserId = $res1->UserId;
                    $res = new stdClass();
                    $res->UserName = $post_Assessment['FirstName'] . " " . $post_Assessment['LastName'];
                    $res->CertificateName = $post_Assessment['CertificateName'];
                    $res->CancelComment = $post_Assessment['CancelComment'];
                    $res->AssignDate = $post_Assessment['AssignDate'];
                    $res->StartTime = $post_Assessment['StartTime'];
                    $res->EndTime = $post_Assessment['EndTime'];
                    $res->loginUrl = BASE_URL . '' . $post_Assessment["LoginURL"];
                    $res->RoleId = $res1->RoleId;
                    $res->CreatedBy = $post_Assessment['UserId'];
                    $res->ActionURL = "admin/assessment-list";
                    $res->NotificationText = $res->UserName.' has requested to cancel the assessment for the certificate "'.$res->CertificateName.'" assigned on '.$res->AssignDate;
                    $EmailToken = 'Admin Cancel Assessment';
                    $send = sendEmailFunction($EmailToken, $UserId, $res,'',true);
                    if ($send) {
                        $this->response('Successful', REST_Controller::HTTP_OK);
                    } else {
                        $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                    }
                } elseif ($result['status'] == "exception") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $result['message']);
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

    public function searchCertificate_post() {
        try {
            $search = json_decode(trim(file_get_contents('php://input')), true);
            $data = $this->CandidateDashboard_model->searchCertificate($search);

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

    public function DeleteCertificate_get($UserId = null, $UserCertificateId = null) {
        try {
            $this->auth();
            if (!empty($UserId)) {
                $data = $this->CandidateDashboard_model->DeleteCertificate($UserId, $UserCertificateId);
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
    public function buyNewPracticeTests_post() {
        try {
            //$this->auth();
            $post_data = json_decode(trim(file_get_contents('php://input')), true);
            if ($post_data) {
                $data = $this->CandidateDashboard_model->buyNewPracticeTests($post_data);
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
    public function AddCertificate_post() {
        try {
            $_POST = $Certificate = json_decode(trim(file_get_contents('php://input')), true);
            $parameters = array(
                array(
                    'field' =>  !empty($_POST['CertificatesNewlist']) ? 'CertificatesNewlist[]' : "CertificatesNewlist",
                    'label' => 'CertificatesNewlist',
                    'rules' => 'trim|required|numeric',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "certificate"))
                ),
                 array(
                    'field' => 'UserId',
                    'label' => 'UserId',
                    'rules' => 'trim|required|numeric',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "user id"))
                ),
            );
            
            $res = validatePostData($parameters, $_POST);
            if ($res !== true) {
                $this->responsegenerator->generateResponse($res['code'], $res['message']);
            }
           
            $data = $this->CandidateDashboard_model->AddCertificate($Certificate);

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

    public function VerificationRequest_post() {
        try {
            $RequestData = json_decode(trim(file_get_contents('php://input')), true);
            if ($RequestData) {
                $data = $this->CandidateDashboard_model->DocumentVerificationRequest($RequestData);
                if ($data['status'] == "success") {
                    $this->db->select('tu.UserId, tu.FirstName, tu.LastName, tu.EmailAddress, tu.RoleId');
                    $this->db->where('tu.RoleId', 1);
                    $data = $this->db->get('tblusers as tu');
                    $res1 = $data->result()[0];
                    $UserId = $res1->UserId;
                    $res = new stdClass();
                    $res->UserName = $RequestData['UserName'];
                    $res->CertificateName = $RequestData['CertificateName'];
                    $res->loginUrl = BASE_URL . '' . $RequestData["LoginURL"];
                    $res->RoleId = $res1->RoleId;
                    $res->CreatedBy = $RequestData['UserId'];
                    $res->ActionURL = "admin/userPendingVerification";
                    $res->NotificationText = $res->UserName.' has requested to verify the document(s) for the certificate "'.$res->CertificateName;
                    $EmailToken = 'Document Verification Request';
                    $send = sendEmailFunction($EmailToken, $UserId, $res,'',true);
                    if ($send) {
                        $this->response('Successful', REST_Controller::HTTP_OK);
                    } else {
                        $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                    }
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

}
