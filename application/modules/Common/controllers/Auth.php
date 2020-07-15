<?php

defined('BASEPATH') or exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Auth extends MY_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Auth_model');
        $this->load->helper('common');
        $this->load->library('responsegenerator');
    }

    /***** Login function *****/
    public function login_post()
    {
        $_POST = $post_login = json_decode(trim(file_get_contents('php://input')), true);
        
        $parameters = array(
            array(
                'field' => 'EmailAddress',
                'label' => 'Email Address',
                'rules' => 'trim|required|valid_email',
                'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "email address") ,
                        'valid_email' => VALID_EMAIL_ADDRESS)
            ),
            array(
                'field' => 'Password',
                'label' => 'Password',
                'rules' => 'trim|required',
                'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "password"))
            )
        );
        $res = validatePostData($parameters, $_POST);
        if ($res !== true) {
            $this->responsegenerator->generateResponse($res['code'], $res['message']);
        }
        $EmailAddress = $post_login['EmailAddress']; //Username Posted
        $Password = MD5($post_login['Password']); //Pasword Posted
        $where_query = array('EmailAddress' => $EmailAddress, 'IsActive' => 1); //For where query condition
        $key = $this->config->item('thekey');
        $tryagain = ['status' => 'Please try again']; //Response if login invalid
        $userLocked = ['status' => 'User Locked']; //Response if login invalid
        $invalidEmail = ['status' => 'Invalid Email'];
        $bandUser = ['status' => 'User banded']; //Response if login invalid
        $inActive = ['status' => 'Not Active']; //Response if user is not active
        $inActiveByAdmin = ['status' => 'Inactive by Admin']; //Response if user is not inactive by admin
        $val = $this->Auth_model->get_user($where_query)->row(); //Model to get single data row from database base on username
        //echo $val->IsActive;
        if ($this->Auth_model->get_user($where_query)->num_rows() == 0) {
            $this->response($invalidEmail, REST_Controller::HTTP_NOT_FOUND);
        } else if ($val->IsBan == '1') {
            $this->response($bandUser, REST_Controller::HTTP_NOT_FOUND);
        } else if ($val->IsActive == '0' && $val->VerifiedOn == null) {
            $this->response($inActive, REST_Controller::HTTP_NOT_FOUND);
        }
        else if ($val->IsActive == '0' && $val->VerifiedOn != null) {
            $this->response($inActiveByAdmin, REST_Controller::HTTP_NOT_FOUND);
        }
        $match = $val->Password;   //Get password for user from database
        if ($Password == $match) {  //Condition if password matched
            $res = InsertActivityLog(0, $val->UserId, 'Assessment', 'login');
            $token['UserId'] = $val->UserId;  //From here
            $token['EmailAddress'] = $EmailAddress;
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

            $data = $this->Auth_model->chechtime($where_query);
            if (isset($data)) {
                $data = $this->Auth_model->IsUserUnlocked($where_query);
                if (isset($data)) {
                    $this->response($output, REST_Controller::HTTP_OK); //This is the respon if success
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            $data = $this->Auth_model->IsUserLocked($where_query);
            if ($data['IsLock'] == '1') {
                $userLocked['time_lastattemp'] = $data['time_lastattempt'];
                $this->response($userLocked, REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->response($tryagain, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    /***** Logout function *****/
    public function logout_post()
    {
        $post_logout = json_decode(trim(file_get_contents('php://input')), true);
        if ($post_logout['UserId']) {
            $res = InsertActivityLog(0, $post_logout['UserId'], 'Tool', 'logout');
            if ($res) {
                $this->response([
                    'message' => 'Successful logout'
                ], REST_Controller::HTTP_OK); //Respon if logout success
            } else {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG); // Respon if not logout 
            }
        } else {
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG); // Respon if not logout 
        }
    }
}
