<?php

defined('BASEPATH') or exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Password extends MY_Controller {

    function __construct() {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Password_model');
        $this->load->library('responsegenerator');
    }

    /*     * *** Request for reset to password using forgot password page **** */

    public function forgot_post() {
        $_POST = $post_pass = json_decode(trim(file_get_contents('php://input')), true);
        
        
        $parameters = array(
            array(
                'field' => 'EmailAddress',
                'label' => 'Email Address',
                'rules' => 'trim|required|valid_email',
                'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "email address") ,
                        'valid_email' => VALID_EMAIL_ADDRESS)
            ),
        );
        $res = validatePostData($parameters, $_POST);
        if ($res !== true) {
            $this->responsegenerator->generateResponse($res['code'], $res['message']);
        }
        
       
        
        if ($post_pass) {
            $post_pass['ForgotPasswordCode'] = mt_rand(100000, 999999);
            $result = $this->Password_model->forgotPassword($post_pass);
            if ($result['status'] == "success") {
                if (is_string($result['data']) == false) {
                    $res = $result['data']->result()[0];
                    $UserId = $res->UserId;
                    $FirstName = $res->FirstName;
                    $LastName = $res->LastName;
                    $data['FirstName'] = $FirstName;
                    $data['LastName'] = $LastName;
                    $data['UserId'] = $UserId;
                    $data['ForgotPasswordCode'] = $post_pass['ForgotPasswordCode'];
                    $data['EmailAddress'] = $post_pass['EmailAddress'];

                    $res->loginUrl = BASE_URL . '/' . $post_pass["ResetPasswordURL"] . '/' . JWT::encode($data, "MyGeneratedKey", "HS256");
                    $EmailToken = 'Forgot Password';
                    $send = sendEmailFunction($EmailToken, $UserId, $res);
                    if ($send) {
                        $this->response([
                            'message' => 'Successful forgot'
                                ], REST_Controller::HTTP_OK);
                    } else {
                        $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                    }
                } else {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                }
            } else {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
            }
        } else {
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
        }
    }

    /*     * *** verify/check password reset link is already used or not **** */

    public function checkResetLink_post() {
        $post_passlink = json_decode(trim(file_get_contents('php://input')), true);
        if ($post_passlink) {
            $result = $this->Password_model->checkResetLink($post_passlink);
            if ($result['status'] == "success") {
                $this->response([
                    'message' => 'Successful'
                        ], REST_Controller::HTTP_OK);
            } elseif ($result['status'] == "exception") {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $data['message']);
            } else {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
            }
        } else {
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
        }
    }

    /*     * *** Reset password using password reset link **** */

    public function reset_post() {
        $_POST = $post_pass = json_decode(trim(file_get_contents('php://input')), true);
        
        $parameters = array(
            array(
                'field' => 'UserId',
                'label' => 'User ID',
                'rules' => 'trim|required|greater_than[0]',
                'errors' =>
                    array('greater_than' => 'Invalid UserID')
            ),
            array(
                'field' => 'Password',
                'label' => 'Password',
                'rules' => 'trim|required|regex_match[/^([a-zA-Z0-9.-_-@$*&!#]{8,})$/]',
                'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "password"),
                        'regex_match' => PASSWORD_REGEX_IS_REQUIRED)
            ),
            array(
                'field' => 'ConfirmPassword',
                'label' => 'ConfirmPassword',
                'rules' => 'trim|required|matches[Password]',
                'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "confirm password"), 'matches' => PASSWORD_DID_NOT_MATCH)
            ),
            array(
                'field' => 'LoginURL',
                'label' => 'Login URL',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'ForgotPasswordURL',
                'label' => 'Forgot Password URL',
                'rules' => 'trim|required',
            )
        );
        $res = validatePostData($parameters, $_POST);
        if ($res !== true) {
            $this->responsegenerator->generateResponse($res['code'], $res['message']);
        }
        
        if ($post_pass) {
            $result = $this->Password_model->resetPassword($post_pass);
            if ($result['status'] == "success") {
                $res = new stdClass();
                $UserId = $post_pass['UserId'];
                $res->loginUrl = BASE_URL . '/' . $post_pass["LoginURL"] . '/';
                $res->forgotUrl = BASE_URL . '/' . $post_pass["ForgotPasswordURL"] . '/';
                $EmailToken = 'Reset Password';
                $send = sendEmailFunction($EmailToken, $UserId, $res);
                if ($send) {
                    $this->response([
                        'message' => 'Successful reset'
                            ], REST_Controller::HTTP_OK);
                } else {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                }
            } elseif ($result['status'] == "exception") {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $data['message']);
            } else {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
            }
        } else {
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
        }
    }

    /*     * *** change password **** */

    public function change_post() {
        //$this->auth();
        $_POST = $post_password = json_decode(trim(file_get_contents('php://input')), true);
        $parameters = array(
                array(
                    'field' => 'UserId',
                    'label' => 'UserId',
                    'rules' => 'trim|required',
                    'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "user id"))
                ),
                array(
                    'field' => 'OldPassword',
                    'label' => 'OldPassword',
                    'rules' => 'trim|required',
                    'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "old password"))
                ),
                array(
                    'field' => 'Password',
                    'label' => 'Password',
                    'rules' => 'trim|required|regex_match[/^([a-zA-Z0-9.-_-@$*&!#]{8,})$/]',
                    'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "password"),
                              'regex_match' => PASSWORD_REGEX_IS_REQUIRED)
                ),
                array(
                    'field' => 'ConfirmPassword',
                    'label' => 'ConfirmPassword',
                    'rules' => 'trim|required|matches[Password]',
                    'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "confirm password"),
                              'regex_match' => PASSWORD_REGEX_IS_REQUIRED ,
                              'matches' => PASSWORD_DID_NOT_MATCH)
                )
            );
       
        $res = validatePostData($parameters, $_POST);
        if ($res !== true) {
            $this->responsegenerator->generateResponse($res['code'], $res['message']);
        }
        if ($post_password) {
            $result = $this->Password_model->changePassword($post_password);
            if ($result['status'] == "success") {
                $this->response([
                    'message' => 'Successful change'
                        ], REST_Controller::HTTP_OK);
            } elseif ($result['status'] == "exception") {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $data['message']);
            } else {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, 'Incorrect old password');
            }
        } else {
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
        }
    }

}
