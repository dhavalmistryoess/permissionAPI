<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SyncDetails extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('SyncDetails_model');
        $this->load->helper('common');
        $this->load->library('mylibrary');
        $this->load->library('responsegenerator');
    }

    public function getClassName_get() {
        try {
            $this->load->helper("url");
            $foldername = "application/modules/Common/controllers/";
            $a = scandir($foldername);
            $files = array_diff(scandir($foldername), array('.', '..'));

            foreach ($files as $fileName) {
                if (!in_array($fileName, array('CronJobs.php', 'SyncDetails.php'))) {
                    $lines = file($foldername . $fileName);
                    $keyName = '';
                    foreach ($lines as $key => $line) {
                        $getDelimiter = (explode(" ", $line));
                        if (isset($getDelimiter) && $getDelimiter[0] == 'class') {
                            $demo[$getDelimiter[1]][] = $getDelimiter[1];
                            $keyName = $getDelimiter[1];
                            unset($demo[$getDelimiter[1]][0]);
                        }
                        $searchword = '_get(';
                        $getArray = array_filter($getDelimiter, function($var) use ($searchword) {
                            return (strpos($var, $searchword) !== FALSE) ? $var : false;
                        });

                        (!empty($getArray)) ? $demo[$keyName][] = explode("_", implode("", $getArray))[0] : '';
                        $searchword = '_post(';
                        $postArray = array_filter($getDelimiter, function($var) use ($searchword) {
                            return (strpos($var, $searchword) !== FALSE) ? $var : false;
                        });

                        (!empty($postArray)) ? $demo[$keyName][] = explode("_", implode("", $postArray))[0] : '';
                    }
                }
            }

            foreach ($demo as $key => $data) {
                foreach ($data as $functionName) {
                    $tempData = array(
                        'ModuleName' => 1,
                        'ClassName' => $key,
                        'FunctionName' => $functionName,
                        'IsActive' => 1,
                        'CreatedBy' => 1,
                        'UpdatedBy' => 1
                    );

                    $this->SyncDetails_model->insertRecord($tempData);
                }
            }
        } catch (Exception $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }

    public function getRolePermission_get($roleId) {
        try {
            if (!empty($roleId)) {
                $data = [];
                $data = $this->SyncDetails_model->getPermissionByRole($roleId);
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

    public function insertPermissionByRole_post() {
        try {
            $_POST = $postPermission = json_decode(trim(file_get_contents('php://input')), true);
            if (!empty($_POST['Permission'])) {
                foreach ($_POST['Permission'] as $key => $itemOptions) {
                    $parameters[] = array(
                        'field' => 'Permission[' . $key . '][RoleId]',
                        'label' => 'RoleId',
                        'rules' => 'trim|required',
                        'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "role id"))
                    );
                    $parameters[] = array(
                        'field' => 'Permission[' . $key . '][ModuleIDs]',
                        'label' => 'RoleId',
                        'rules' => 'trim|required',
                        'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "role id"))
                    );
                }
            }

            $res = validatePostData($parameters, $_POST);
            if ($res !== true) {
                $this->responsegenerator->generateResponse($res['code'], $res['message']);
            }

            if ($postPermission) {
                $result = $this->SyncDetails_model->addPermission($postPermission);
                if ($result['status'] == "success") {
                    $this->response($result['data'], REST_Controller::HTTP_OK);
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
