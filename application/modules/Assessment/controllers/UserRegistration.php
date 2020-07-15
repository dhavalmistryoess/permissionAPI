<?php

defined('BASEPATH') or exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class UserRegistration extends MY_Controller {

    public function __construct() {
        parent::__construct();
        //$this->auth();
        $this->load->model('UserRegistration_model');
        $this->load->model('Auth_model');
        $this->load->library('responsegenerator');
    }

    public function CheckEmail_post() {
        try {
            $_POST = $post_data = json_decode(trim(file_get_contents('php://input')), true);
            $parameters = array(
                array(
                    'field' => 'EmailAddress',
                    'label' => 'Email Address',
                    'rules' => 'trim|required|valid_email',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "email address"),
                        'valid_email' => VALID_EMAIL_ADDRESS)
                )
            );
            $res = validatePostData($parameters, $_POST);
            if ($res !== true) {
                $this->responsegenerator->generateResponse($res['code'], $res['message']);
            }

            if ($post_data) {
                $result = $this->UserRegistration_model->CheckEmail($post_data);
                if ($result['status'] == "success" && $result['data'] == 'EmailAddress exists') {
                    $this->response('EmailAddress already exists', REST_Controller::HTTP_OK);
                } elseif ($result['status'] == "exception") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $result['message']);
                } else {
                    $this->response('EmailAddress does not exists', REST_Controller::HTTP_OK);
                }
            } else {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
            }
        } catch (Exception $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }

    public function savePersonalInfo_post() {
        try {
            $post_data = json_decode(trim(file_get_contents('php://input')), true);
            if ($post_data) {
                $result = $this->UserRegistration_model->savePersonalInfo($post_data);
                $this->response($result, REST_Controller::HTTP_OK);
            } else {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
            }
        } catch (Exception $ex) {
             trigger_error($ex->getMessage(), E_USER_ERROR);
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }

    public function activateAccount_get($UserId = null) {
        try {
            if ($UserId) {
                $result = $this->UserRegistration_model->activateAccount($UserId);
                $result['status'] = "success";
                if ($result['status'] == "success") {
                    $this->db->select('tu.UserId, tu.RoleId, tu.FirstName, tu.LastName, tu.EmailAddress');
                    $this->db->where('tu.UserId', $UserId);
                    $data = $this->db->get('tblusers as tu');
                    $res = $data->result()[0];
                    $res->UserName = $res->FirstName . " " . $res->LastName;
                    $res->RoleId = $res->RoleId;
                    $res->CreatedBy = $UserId;
                    if($res->RoleId == 2 || $res->RoleId == 4){
                        $res->ActionURL = "proctorDashboard";
                    }
                    else{
                        $res->ActionURL = "dashboard";
                    }
                    $res->NotificationText = "Your account is successfully activated.";
                    $res->loginUrl = BASE_URL . '/login';
                    $EmailToken = 'Activation Account';
                    $send = sendEmailFunction($EmailToken, $UserId, $res,'',true);
                    if ($send) {
                        $this->response("Successfully mail sent", REST_Controller::HTTP_OK);
                    } else {
                       $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                    }
                } elseif ($result['status'] == "exception") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $result['message']);
                } else {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                }
            } else {
                $this->response(array('message' => 'Something is wrong'), REST_Controller::HTTP_NOT_FOUND);
            }
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    /*     * *** User Registration **** */

    public function Registration_post() {
        try {
            $_POST = $post_Invitation = json_decode(trim(file_get_contents('php://input')), true);
            $parameters = array(
                array(
                    'field' => 'EmailAddress',
                    'label' => 'Email Address',
                    'rules' => 'trim|required|valid_email|is_unique[tblusers.EmailAddress]',
                    'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "email address") ,
                            'valid_email' => VALID_EMAIL_ADDRESS,
                            'is_unique' => sprintf(ALREADY_REGISTER, 'Email address')
                )),
                array(
                    'field' => 'Password',
                    'label' => 'Password',
                    'rules' => 'trim|required|regex_match[/^([a-zA-Z0-9.-_-@$*&!#]{8,})$/]',
                    'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "password"),
                              'regex_match' => PASSWORD_REGEX_IS_REQUIRED)
                ),
                array(
                    'field' => 'cPassword',
                    'label' => 'cPassword',
                    'rules' => 'trim|required|matches[Password]',
                    'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "confirm password"),
                              'regex_match' => PASSWORD_REGEX_IS_REQUIRED ,
                              'matches' => PASSWORD_DID_NOT_MATCH)
                ),
                array(
                    'field' => 'FirstName',
                    'label' => 'FirstName',
                    'rules' => 'trim|required|min_length[2]',
                    'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "first name"),
                              'min_length' => sprintf(MIN_LENGTH_ERROR, "first name"))
                ),
                 array(
                    'field' => 'LastName',
                    'label' => 'LastName',
                    'rules' => 'trim|required|min_length[2]',
                    'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "last name"),
                              'min_length' => sprintf(MIN_LENGTH_ERROR, "last name"))
                   ),
                array(
                    'field' => 'MiddleName',
                    'label' => 'MiddleName',
                    'rules' => 'trim|min_length[2]',
                    'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "middle name"),
                              'min_length' => sprintf(MIN_LENGTH_ERROR, "middle name"))
                   ),
                array(
                    'field' => 'RoleId',
                    'label' => 'RoleId',
                    'rules' => 'trim|required',
                    'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "role id"))
                   ),
                array(
                    'field' => 'PhoneNumber',
                    'label' => 'PhoneNumber',
                    'rules' => 'trim|required|regex_match[/^([a-zA-Z0-9.-_-@$*&!#]{8,})$/]',
                    'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "phone number"),
                              'regex_match' => sprintf(REGEXMAX_IS_REQUIRED, 'phone number'))
                   ),
                
            );
             if(isset($_POST['RoleId']) && ($_POST['RoleId'] == 2 || $_POST['RoleId'] == 4)) {
              $parameters[] = 
                array(
                    'field' => 'ProctorPrice',
                    'label' => 'ProctorPrice',
                    'rules' => 'trim|required|is_checknumber',
                    'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "proctor price") ,
                            'is_checknumber' => sprintf(VALID_EMAIL_ADDRESS, "proctor price" ))
                );
            }
            
            $res = validatePostData($parameters, $_POST);
            if ($res !== true) {
                $this->responsegenerator->generateResponse($res['code'], $res['message']);
            }
            if ($post_Invitation) {
                $result = $this->UserRegistration_model->Registration($post_Invitation);
                if ($result['status'] == "success") {
                    $UserId = $result['data'];
                    $this->db->select('tu.UserId, tu.RoleId,tu.FirstName, tu.LastName, tu.EmailAddress');
                    $this->db->where('tu.UserId', $UserId);
                    $data = $this->db->get('tblusers as tu');
                    $res = $data->result()[0];
                    $res->UserName = $res->FirstName . " " . $res->LastName;
                    $res->loginUrl = BASE_URL . '' . $post_Invitation["LoginURL"] . '/' . JWT::encode($UserId, "MyGeneratedKey", "HS256");
                    $res->RoleId = $res->RoleId;
                    $res->CreatedBy = $UserId;
                    if($res->RoleId == 2 || $res->RoleId == 4){
                        $res->ActionURL = "proctorDashboard";
                    }
                    else{
                        $res->ActionURL = "dashboard";
                    }
                    $res->NotificationText = "You are successfully registered.";
                    $data1 = $this->db->query('select tu.UserId, tu.FirstName, tu.LastName, tu.EmailAddress from tblusers tu where tu.RoleId = 1');
                    $res1 = $data1->result();
                    $AdminId = $res1[0]->UserId;

                    $EmailToken = 'Registration Successfully';
                    $send = sendEmailFunction($EmailToken, $UserId, $res,'',true);
                    if ($send) {
                        $res3 = InsertNotification(1, $AdminId, $res->UserName.' is successfully registered', 'manage-register-request', $UserId);
                        $this->response($result['data'], REST_Controller::HTTP_OK);
                    } else {
                        $this->response([
                            'code' => REST_Controller::HTTP_NOT_FOUND,
                            'message' => 'Something is wrong!'
                                ], REST_Controller::HTTP_NOT_FOUND);
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

    public function SendMail($Id = null, $res = null, $certificateList = null) {
        try {
            $this->db->select('tu.UserId, tu.RoleId, tu.FirstName, tu.LastName, tu.EmailAddress');
            $this->db->where('tu.UserId', $Id);
            $data = $this->db->get('tblusers as tu');
            $res1 = $data->result();
            $UserId = $res1[0]->UserId;
            $RoleId = $res1[0]->RoleId;

            $this->db->select('tu.UserId, tu.RoleId, tu.FirstName, tu.LastName, tu.EmailAddress');
            $this->db->where('tu.UserId', $res['UserId']);
            $data1 = $this->db->get('tblusers as tu');
            $res2 = $data1->result();

            $res['UserName'] = $res1[0]->FirstName . " " . $res1[0]->LastName;
            $res['CandidateName'] = $res2[0]->FirstName . " " . $res2[0]->LastName;
            $res['loginUrl'] = BASE_URL . '' . $res["LoginURL"];
            $certificateList = implode(',', $certificateList);

            $select = $this->db->query('select GROUP_CONCAT(c.CertificateName) AS certiNames from tblmstcertificates c where FIND_IN_SET(c.CertificateId, "' . $certificateList . '")');
            $certificateList = $select->result()[0]->certiNames;

            if ($RoleId != 1) {
                $EmailToken = 'Become A Proctor';
                if ($res['CertificateFor'] == 1) {
                    $res['text'] = 'You are applied as proctor for these certificates: ' . $certificateList;
                } else {
                    $res['text'] = 'You are applied as candidate for these certificates: ' . $certificateList;
                }
            } else {
                $EmailToken = 'Become A Proctor for Admin';
                $res['text'] = 'Below user applied for another Role:';
                $res['CertificateName'] = $certificateList;
                if ($res['CertificateFor'] == 1) {
                    $res['Role'] = 'Proctor';
                } else {
                    $res['Role'] = 'Candidate';
                }
            }
            $res['RoleId'] = $RoleId;
            $res['CreatedBy'] = $UserId;
            if($RoleId == 2 || $RoleId == 4){
                $res['ActionURL'] = "proctorDashboard";
                $res['NotificationText'] = $res['text'];
            }
            elseif($RoleId == 3){
                $res['ActionURL'] = "dashboard";
                $res['NotificationText'] = $res['text'];
            }
            else{
                $res['ActionURL'] = "admin/manage-register-request";
                $res['NotificationText'] = $res['CandidateName'].' is applied for '.$res['Role'].' for these certificate(s) '.$res['CertificateName'];
            }
            $send = sendEmailFunction($EmailToken, $UserId, $res);
            $res3 = InsertNotification($res['RoleId'], $UserId, $res['NotificationText'], $res['ActionURL'], $res['CreatedBy']);
            return $res;
        } catch (Exception $ex) {
             trigger_error($ex->getMessage(), E_USER_ERROR);
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }

    /*     * *** Update User proctor **** */

    public function Updateproctor_post() {
        try {
            $_POST = $post_Invitation = json_decode(trim(file_get_contents('php://input')), true);
            $parameters = array(
                array(
                    'field' => 'CertificateFor',
                    'label' => 'CertificateFor',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "certificate for"))
                ),
                array(
                    'field' => 'EmailAddress',
                    'label' => 'EmailAddress',
                    'rules' => 'trim|required|valid_email',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "email address"),
                          'valid_email' => sprintf(VALID_EMAIL_ADDRESS, "email address"))
                ),
                array(
                    'field' => 'RoleId',
                    'label' => 'RoleId',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "role id"))
                ),
                array(
                    'field' => 'UserId',
                    'label' => 'UserId',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "user id"))
                ),
                array(
                    'field' => !empty($_POST['certificateList']) ? 'certificateList[]' : "certificateList",
                    'label' => 'certificateList',
                    'rules' => 'trim|required|numeric',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "certificate"),
                          'numeric' => sprintf(IS_NUMERIC_NUMBER, "numeric"))
                )
            );
            $res = validatePostData($parameters, $_POST);
            if ($res !== true) {
                $this->responsegenerator->generateResponse($res['code'], $res['message']);
            }
            if ($post_Invitation) {
                $result = $this->UserRegistration_model->Updateproctor($post_Invitation);
                if ($result) {
                    $where_query = array('EmailAddress' => $post_Invitation['EmailAddress'], 'IsActive' => 1); //For where query condition
                    $key = $this->config->item('thekey');
                    $val = $this->Auth_model->get_user($where_query)->row();
                    $token['UserId'] = $val->UserId;  //From here
                    $token['EmailAddress'] = $post_Invitation['EmailAddress'];
                    $token['RoleId'] = $val->RoleId;
                    $token['EmployeeId'] = $val->EmployeeId;
                    $token['FirstName'] = $val->FirstName;
                    $token['LastName'] = $val->LastName;
                    $token['PhoneNumber'] = $val->PhoneNumber;
                    $token['RegistrationType'] = $val->RegistrationType;
                    $token['UserStatusId'] = $val->UserStatusId;
                    $date = new DateTime();
                    $token['iat'] = $date->getTimestamp();
                    $token['exp'] = $date->getTimestamp() + 60 * 60 * 5; //To here is to generate token
                    $output['token'] = JWT::encode($token, $key); //This is the output token
                    $output['status'] = 'Successful login'; //Respon if login invalid

                    $data = $this->db->query('select tu.UserId, tu.FirstName, tu.LastName, tu.EmailAddress from tblusers tu where tu.RoleId = 1');
                    $res1 = $data->result();
                    $AdminId = $res1[0]->UserId;
                    $send = $this->SendMail($result['data'], $post_Invitation, $post_Invitation['certificateList']);
                    $send1 = $this->SendMail($AdminId, $post_Invitation, $post_Invitation['certificateList']);
                    if ($send && $send1) {
                        $this->response($output, REST_Controller::HTTP_OK);
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

    /*     * *** Upload Adhar and PAN card **** */

    public function uploadDocuments_post($TotalCount = null, $UserId = null) {
        try {
            if ($_FILES) {
                for ($j = 0; $j < $TotalCount; $j++) {
                    if (isset($_FILES['Document' . $j]) && !empty($_FILES['Document' . $j])) {
                        $directoryname = FILE_DIR . "" . $UserId . "/assessment/Documents/";
                        if (!is_dir($directoryname)) {
                            mkdir($directoryname, 0755, true);
                        }
                        $target_dir = $directoryname;
                        $newfilename = str_replace(" ", "_", basename($_FILES["Document" . $j]["name"]));
                        $target_file = $target_dir . $newfilename;
                        move_uploaded_file($_FILES["Document" . $j]["tmp_name"], $target_file);
                    }
                }
                $this->response([
                    'message' => 'Successful upload'
                        ], REST_Controller::HTTP_OK);
            } else {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
            }
        } catch (Exception $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }

    /*     * *** Get All Departments **** */

    public function getAllDefault_get() {
        try {
            //$this->auth();
            $data = $this->UserRegistration_model->getAllDefaultList();
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

    public function getAllcertificate_get($UserId = null, $CertificateFor = null) {
        try {
            $this->auth();
            $data = $this->UserRegistration_model->getAllcertificate($UserId, $CertificateFor);
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

    /*     * *** Get Department by Department_id **** */

    public function getById_get($CertificateId = Null, $CertificateFor = Null) {
        try {
            //$this->auth();
            if (!empty($CertificateId) && !empty($CertificateFor)) {
                $data = [];
                $data = $this->UserRegistration_model->getById($CertificateId, $CertificateFor);
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

    /*     * *** Get All Departments **** */

    public function getAll_get($IsActive = null) {
        try {
            $this->auth();
            if (!empty($IsActive)) {
                $data = $this->UserRegistration_model->getAllUsers($IsActive);
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

    /*     * *** Get Department by Department_id **** */

    public function getUserCertificates_get($UserId = null) {
        try {
            $this->auth();
            if (!empty($UserId)) {
                $data = [];
                $data = $this->UserRegistration_model->getUserCertificates($UserId);
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

    public function VerifyDocuments_post() {
        try {
            $_POST = $post_data = json_decode(trim(file_get_contents('php://input')), true);
            $parameters = array(
                array(
                    'field' => 'DocumentVerificationStatus',
                    'label' => 'DocumentVerificationStatus',
                    'rules' => 'trim|required|in_list[Not Verify,verify]',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "document status"),
                          'in_list' => sprintf(INVALID_DETAILS, "document status"))
                ),
                array(
                    'field' => 'UpdatedBy',
                    'label' => 'UpdatedBy',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "updatedby"))
                ),
                array(
                    'field' => 'UserCertificateId',
                    'label' => 'UserCertificateId',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "certificate"))
                )
                
            );
            if($_POST['DocumentVerificationComment'] == 'Not Verify') {
                $parameters[] = array(
                    'field' => 'DocumentVerificationComment',
                    'label' => 'DocumentVerificationComment',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "document comment"))
                );
            }
            $res = validatePostData($parameters, $_POST);
            if ($res !== true) {
                $this->responsegenerator->generateResponse($res['code'], $res['message']);
            }
            if ($post_data) {
                $result = $this->UserRegistration_model->VerifyDocuments($post_data);
                if ($result['status'] == "success") {
                    $UserId = $post_data['UserId'];
                    $res = new stdClass();
                    $res->UserName = $post_data['UserName'];
                    
                    $res->CertificateName = $post_data['CertificateName'];
                    if ($post_data['DocumentVerificationStatus'] == "verify") {
                        $res->text = 'Your documents are verified.';
                        $res->DocumentVerificationComment = $post_data['DocumentVerificationComment'];
                    } else {
                        $res->text = 'Your documents are not verified.';
                        $res->DocumentVerificationComment = $post_data['DocumentVerificationComment'];
                    }
                    $res->loginUrl = BASE_URL . '' . $post_data["LoginURL"];

                    $res->RoleId = $post_data['RoleId'];
                    $res->CreatedBy = $post_data['UpdatedBy'];
                    $res->ActionURL = "profile";
                    $res->NotificationText = $res->text.' for this certificate "'.$res->CertificateName.'" by admin';

                    $EmailToken = 'Document Verification Status';
                    $send = sendEmailFunction($EmailToken, $UserId, $res,'',true);
                    if ($send) {
                        $this->response('Successful', REST_Controller::HTTP_OK);
                    } else {
                        $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                    }
                } elseif ($result['status'] == "exception") {
                   $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $result['message']);
                } else {
                    $this->response('EmailAddress does not exists', REST_Controller::HTTP_OK);
                }
            } else {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
            }
        } catch (Exception $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }

    /*     * *** Get User list whoes verification is pending **** */

    public function VerifyDocument_get($UserDocumentCertificateMappingId = Null, $IsVerify = Null) {
        try {
            $this->auth();
            if (isset($UserDocumentCertificateMappingId) && !empty($UserDocumentCertificateMappingId) && isset($IsVerify)) {
                $data = $this->UserRegistration_model->VerifyDocument($UserDocumentCertificateMappingId, $IsVerify);
                if ($data['status'] == "success") {
                    $this->response('Successful', REST_Controller::HTTP_OK);
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

    /*     * *** Get User list whoes verification is pending **** */

    public function getAllPendingVerificationUserList_get($IsActive = null) {
        try {
            $this->auth();
            if (!empty($IsActive)) {
                $data = $this->UserRegistration_model->getAllPendingVerificationUserList($IsActive);
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
                    'field' => 'UserId',
                    'label' => 'UserId',
                    'rules' => 'trim|required|is_natural_no_zero',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "user id"),
                          'is_natural_no_zero' => sprintf(VALID_EMAIL_ADDRESS, "user id"))
                )
            );
            $res = validatePostData($parameters, $_POST);
            if ($res !== true) {
                $this->responsegenerator->generateResponse($res['code'], $res['message']);
            }
            $data = $this->UserRegistration_model->AddCertificate($Certificate);

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

    /*     * *** User Band By Admin **** */

    public function isBanByadmin_post() {
        try {
            $this->auth();
            $_POST = $user_data = json_decode(trim(file_get_contents('php://input')), true);
            $parameters = array(
                array(
                    'field' => 'IsBan',
                    'label' => 'IsBan',
                    'rules' => 'trim|required|in_list[0,1]',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "isban"),
                          'in_list' => sprintf(VALID_EMAIL_ADDRESS, "isban"))
                ),
                array(
                    'field' => 'UpdatedBy',
                    'label' => 'UpdatedBy',
                    'rules' => 'trim|required|is_natural_no_zero',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "country"),
                          'is_natural_no_zero' => sprintf(VALID_EMAIL_ADDRESS, "updated by"))
                ),
                array(
                    'field' => 'UserId',
                    'label' => 'UserId',
                    'rules' => 'trim|required|is_natural_no_zero',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "user id"),
                          'is_natural_no_zero' => sprintf(VALID_EMAIL_ADDRESS, "user id"))
                )
            );
            $res = validatePostData($parameters, $_POST);
            if ($res !== true) {
                $this->responsegenerator->generateResponse($res['code'], $res['message']);
            }
            $data = $this->UserRegistration_model->isBanByadmin($user_data);

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
