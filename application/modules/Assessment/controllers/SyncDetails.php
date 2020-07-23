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
            $getSessionData = $this->session->userdata('userDetails');
            $this->load->helper("url");
            $foldername = [
                "application/modules/Assessment/controllers/",
                "application/modules/Common/controllers/"
            ];
            foreach ($foldername as $folderKey => $folder) {
                $moduleName = explode("/", $folder)[2];
               
                $getFileDetails = array_diff(scandir($folder), array('.', '..'));
                foreach ($getFileDetails as $fileName) {
                    if (!in_array($fileName, array('CronJobs.php', 'SyncDetails.php'))) {
                        $lines = file($folder . $fileName);
                        $keyName = '';
                        foreach ($lines as $key => $line) {
                            $getDelimiter = (explode(" ", $line));
                            if (isset($getDelimiter) && $getDelimiter[0] == 'class') {
                                $folderNameArr[$moduleName][$getDelimiter[1]][] = $getDelimiter[1];
                                $keyName = $getDelimiter[1];
                                unset($folderNameArr[$moduleName][$getDelimiter[1]][0]);
                            }
                            $searchword = '_get(';
                            $getArray = array_filter($getDelimiter, function($var) use ($searchword) {
                                return (strpos($var, $searchword) !== FALSE) ? $var : false;
                            });

                            (!empty($getArray)) ? $folderNameArr[$moduleName][$keyName][] = explode("_", implode("", $getArray))[0] : '';
                            $searchword = '_post(';
                            $postArray = array_filter($getDelimiter, function($var) use ($searchword) {
                                return (strpos($var, $searchword) !== FALSE) ? $var : false;
                            });

                            (!empty($postArray)) ? $folderNameArr[$moduleName][$keyName][] = explode("_", implode("", $postArray))[0] : '';
                        }
                    }
                }
            }
            if (!empty($folderNameArr)) {
                foreach ($folderNameArr as $key => $classArr) {
                    foreach ($classArr as $className => $functionArr) {
                        foreach ($functionArr as $functionName) {
                            $tempData = array(
                                'ModuleName' => ($key == "Assessment") ? 1 : 2,
                                'ClassName' => $className,
                                'FunctionName' => $functionName,
                                'IsActive' => 1,
                                'CreatedBy' => $getSessionData['UserId'],
                                'UpdatedBy' => $getSessionData['UserId']
                            );
                            $this->SyncDetails_model->insertRecord($tempData);
                        }
                    }
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
                if (!$this->cache->get(PERMISSION_ALL."_".$roleId)) {
                    $responseArr = $this->SyncDetails_model->getPermissionByRole($roleId);
                    if ($responseArr['status'] == "exception") {
                        $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $responseArr['message']);
                    } elseif ($responseArr['status'] == "fail") {
                        $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                    }
                    $permissionArray = $responseArr['data'];
                    $this->cache->save(PERMISSION_ALL."_".$roleId, json_encode($responseArr['data']), CACHE_EXPIRE_TIME);
                } else {
                     $permissionArray = (json_decode($this->cache->get(PERMISSION_ALL."_".$roleId), true));
                }
                 $this->response($permissionArray, REST_Controller::HTTP_OK);
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
            
            if (!empty($postPermission)) {
                foreach ($postPermission as $key => $itemOptions) {
                    $parameters[] = array(
                        'field' => '[' . $key . '][RoleId]',
                        'label' => 'RoleId',
                        'rules' => 'trim|required',
                        'errors' =>
                        array('required' => sprintf(IS_REQUIRED, "role id"))
                    );
                    $parameters[] = array(
                        'field' => '[' . $key . '][ModuleIDs]',
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
    
    public function getRolePermissionDetails_get($roleId) {
        try {
            if (!empty($roleId)) {
                if (!$this->cache->get(ROLE_PERMISSION."_".$roleId)) {
                    $responseArr = $this->SyncDetails_model->getPermission($roleId);
                    if ($responseArr['status'] == "exception") {
                        $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $responseArr['message']);
                    } elseif ($responseArr['status'] == "fail") {
                        $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                    }
                    $permissionArray = $responseArr['data'];
                    $this->cache->save(ROLE_PERMISSION."_".$roleId, json_encode($responseArr['data']), CACHE_EXPIRE_TIME);
                } else {
                     $permissionArray = (json_decode($this->cache->get(ROLE_PERMISSION."_".$roleId), true));
                }
                 $this->response($permissionArray, REST_Controller::HTTP_OK);
            } else {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
            }
        } catch (Exception $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }
    
    public function getAllPermission_post() {
        try {
            $_POST = $postPermission = json_decode(trim(file_get_contents('php://input')), true);
            $this->load->library('form_validation');
            $this->form_validation->set_rules('limit', 'limit', 'trim|required');
            $this->form_validation->set_rules('offset', 'offset', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $this->form_validation->error_array());
            }
          
            $checkParamater = ['limit', 'offset', 'searchData', 'sortOrder'];
          
            $searchData = $sortOrder = '';
            if ($postPermission['searchData'] != "") {
                $searchData = $postPermission['searchData'];
            }

            if ($postPermission['sortOrder'] != "") {
                $sortOrder = $postPermission['sortOrder'];
            }

            $IsActive = ($postPermission['searchData']['status'] != "") ? $postPermission['searchData']['status'] : 2;
            $offset = ($postPermission['offset'] != "") ? $postPermission['offset'] : 0;
            $limit = ($postPermission['limit'] != "") ? $postPermission['limit'] : 10;
            if (!$this->cache->get(PERMISSION_ALL)) {
                $responseArr = $this->SyncDetails_model->getPermissionByKey();
                if ($responseArr['status'] == "exception") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $responseArr['message']);
                } elseif ($responseArr['status'] == "fail") {
                    $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
                }

                $myArray = $responseArr['data'];
                $this->cache->save(PERMISSION_ALL, json_encode($responseArr['data']), CACHE_EXPIRE_TIME);
            } else {
                $myArray = (json_decode($this->cache->get(PERMISSION_ALL), true));
            }
            
            $searchColumn = ['Slug', 'DisplayName'];
            $data = generatePagination($myArray, $limit, $offset, $searchData, $sortOrder, $searchColumn);
            $this->response($data, REST_Controller::HTTP_OK);
        } catch (Exception $ex) {
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    } 
    
    public function getPermissionByKey_get($permissionKey = "") {
        try {
            $responseArr = $this->SyncDetails_model->getPermissionByKey($permissionKey);
            if ($responseArr['status'] == "success") {
                $this->response($responseArr['data'], REST_Controller::HTTP_OK);
            } elseif ($responseArr['status'] == "exception") {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $responseArr['message']);
            } else {
                $this->responsegenerator->generateResponse(REST_Controller::HTTP_NOT_FOUND, SOMETHING_WENT_WRONG);
            }
        } catch (Exception $ex) {
            trigger_error($ex->getMessage(), E_USER_ERROR);
            $this->responsegenerator->generateResponse(REST_Controller::HTTP_BAD_REQUEST, $ex->getMessage());
        }
    }
    
     public function updatePermissionName_post() {
        try {
            $_POST = $postPermission = json_decode(trim(file_get_contents('php://input')), true);
            $parameters = array(
                array(
                    'field' => 'DisplayName',
                    'label' => 'DisplayName',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "display name"))
                ),
                array(
                    'field' => 'Slug',
                    'label' => 'Slug',
                    'rules' => 'trim|required',
                    'errors' =>
                    array('required' => sprintf(IS_REQUIRED, "slug"))
                )
               
            );
          
            $res = validatePostData($parameters, $_POST);
            if ($res !== true) {
                $this->responsegenerator->generateResponse($res['code'], $res['message']);
            }

            if ($postPermission) {
                $result = $this->SyncDetails_model->updatePermission($postPermission);
                if ($result['status'] == "success" && $result['data']) {
                    $this->response($result['data'], REST_Controller::HTTP_OK);
                } elseif ($result['status'] == "success" && !$result['data']) {
                    $this->response($result['data'], REST_Controller::HTTP_FOUND);
                }elseif ($result['status'] == "exception") {
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
