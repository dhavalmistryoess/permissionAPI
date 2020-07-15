<?php

class Configuration_model extends CI_Model {
    
    protected $responseArr;

    function __construct()
    {
        parent::__construct();
        $this->responseArr['status'] = 'success';
        $this->responseArr['message'] = '';
        $this->responseArr['data'] = '';
    }
    
    /**** Get All Configuration data **** */
    public function getAllConfigurations() {
        try {
            /*             * *** Get SMTP data **** */
            $this->db->select('ConfigurationId,ModuleId,Key,Value,DisplayText, Description, DisplayOrder,IsActive');
            $this->db->where('IsActive', 1);
            $this->db->where_in('Key', ['EmailFrom', 'EmailPassword', 'SmtpHost', 'SmtpPort']);
            $result1 = $this->db->get('tblmstconfiguration');
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res['SMTPDetails'] = $result1->result();

            $this->db->select('ConfigurationId,ModuleId,Key,Value,DisplayText, Description, DisplayOrder,IsActive');
            $this->db->where('IsActive', 1);
            $this->db->where_in('Key', 'DocumentType');
            $result1 = $this->db->get('tblmstconfiguration');
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res['DocumentType'] = $result1->result();

            $this->db->select('ConfigurationId,ModuleId,Key,Value,DisplayText, Description, DisplayOrder,IsActive');
            $this->db->where('IsActive', 1);
            $this->db->where_in('Key', 'DiscountType');
            $result1 = $this->db->get('tblmstconfiguration');
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res['DiscountType'] = $result1->result();

            $this->db->select('ConfigurationId,ModuleId,Key,Value,DisplayText, Description, DisplayOrder,IsActive');
            $this->db->where('IsActive', 1);
            $this->db->where_in('Key', 'CurrencyType');
            $result1 = $this->db->get('tblmstconfiguration');
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res['CurrencyType'] = $result1->result();

            $this->db->select('ConfigurationId,ModuleId,Key,Value,DisplayText, Description, DisplayOrder,IsActive');
            $this->db->where('IsActive', 1);
            $this->db->where_in('Key', 'ScheduleStatus');
            $result1 = $this->db->get('tblmstconfiguration');
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res['ScheduleStatus'] = $result1->result();

            $this->db->select('ConfigurationId,ModuleId,Key,Value,DisplayText, Description, DisplayOrder,IsActive');
            $this->db->where('IsActive', 1);
            $this->db->where_in('Key', 'RegistrationType');
            $result1 = $this->db->get('tblmstconfiguration');
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res['RegistrationType'] = $result1->result();

            $this->db->select('ConfigurationId,ModuleId,Key,Value,DisplayText, Description, DisplayOrder,IsActive');
            $this->db->where('IsActive', 1);
            $this->db->where_in('Key', 'ResultStatus');
            $result1 = $this->db->get('tblmstconfiguration');
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res['ResultStatus'] = $result1->result();

            $this->db->select('ConfigurationId,ModuleId,Key,Value,DisplayText, Description, DisplayOrder,IsActive');
            $this->db->where('IsActive', 1);
            $this->db->where_in('Key', 'UserStatus');
            $result1 = $this->db->get('tblmstconfiguration');
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res['UserStatus'] = $result1->result();

            $this->db->select('ConfigurationId,ModuleId,Key,Value,DisplayText, Description, DisplayOrder,IsActive');
            $this->db->where('IsActive', 1);
            $this->db->where_in('Key', 'lockAttempts');
            $result1 = $this->db->get('tblmstconfiguration');
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res['lockAttempts'] = $result1->result();

            $this->db->select('ConfigurationId,ModuleId,Key,Value,DisplayText, Description, DisplayOrder,IsActive');
            $this->db->where('IsActive', 1);
            $this->db->where_in('Key', 'lockPeriod');
            $result1 = $this->db->get('tblmstconfiguration');
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res['lockPeriod'] = $result1->result();

            /*             * *** Get Document Base Path **** */
            $this->db->select('ConfigurationId,ModuleId,Key,Value,DisplayText, Description, DisplayOrder,IsActive');
            $this->db->where('IsActive', 1);
            $this->db->where_in('Key', 'DocumentBasePath');
            $result14 = $this->db->get('tblmstconfiguration');
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res['DocumentBasePath'] = $result14->result();

            $this->responseArr['status'] = 'success';
            $this->responseArr['message'] = '';
            $this->responseArr['data'] = $res;
            return $this->responseArr;
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

    /*     * *** Update All Configuration data **** */

    public function updateAllConfigurations($post_data) {
        try {
            if ($post_data) {
                if (isset($post_data['SMTPDetails']) && !empty($post_data['SMTPDetails'])) {
                    foreach ($post_data['SMTPDetails'] as $row) {
                        $update_data = array(
                            'Value' => $row['Value'],
                        );
                        $this->db->where('ConfigurationId', $row['ConfigurationId']);
                        $res = $this->db->update('tblmstconfiguration', $update_data);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                    }
                }
                if (isset($post_data['DocumentType']) && !empty($post_data['DocumentType'])) {
                    foreach ($post_data['DocumentType'] as $row) {
                        $update_data = array(
                            'DisplayText' => $row['DisplayText'],
                        );
                        $this->db->where('ConfigurationId', $row['ConfigurationId']);
                        $res = $this->db->update('tblmstconfiguration', $update_data);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                    }
                }
                if (isset($post_data['DiscountType']) && !empty($post_data['DiscountType'])) {
                    foreach ($post_data['DiscountType'] as $row) {
                        $update_data = array(
                            'DisplayText' => $row['DisplayText'],
                        );
                        $this->db->where('ConfigurationId', $row['ConfigurationId']);
                        $res = $this->db->update('tblmstconfiguration', $update_data);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                    }
                }
                if (isset($post_data['CurrencyType']) && !empty($post_data['CurrencyType'])) {
                    foreach ($post_data['CurrencyType'] as $row) {
                        $update_data = array(
                            'DisplayText' => $row['DisplayText'],
                        );
                        $this->db->where('ConfigurationId', $row['ConfigurationId']);
                        $res = $this->db->update('tblmstconfiguration', $update_data);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                    }
                }
                if (isset($post_data['ScheduleStatus']) && !empty($post_data['ScheduleStatus'])) {
                    foreach ($post_data['ScheduleStatus'] as $row) {
                        $update_data = array(
                            'DisplayText' => $row['DisplayText'],
                        );
                        $this->db->where('ConfigurationId', $row['ConfigurationId']);
                        $res = $this->db->update('tblmstconfiguration', $update_data);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                    }
                }
                if (isset($post_data['RegistrationType']) && !empty($post_data['RegistrationType'])) {
                    foreach ($post_data['RegistrationType'] as $row) {
                        $update_data = array(
                            'DisplayText' => $row['DisplayText'],
                        );
                        $this->db->where('ConfigurationId', $row['ConfigurationId']);
                        $res = $this->db->update('tblmstconfiguration', $update_data);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                    }
                }
                if (isset($post_data['ResultStatus']) && !empty($post_data['ResultStatus'])) {
                    foreach ($post_data['ResultStatus'] as $row) {
                        $update_data = array(
                            'DisplayText' => $row['DisplayText'],
                        );
                        $this->db->where('ConfigurationId', $row['ConfigurationId']);
                        $res = $this->db->update('tblmstconfiguration', $update_data);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                    }
                }
                if (isset($post_data['UserStatus']) && !empty($post_data['UserStatus'])) {
                    foreach ($post_data['UserStatus'] as $row) {
                        $update_data = array(
                            'DisplayText' => $row['DisplayText'],
                        );
                        $this->db->where('ConfigurationId', $row['ConfigurationId']);
                        $res = $this->db->update('tblmstconfiguration', $update_data);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                    }
                }

                if (isset($post_data['lockAttempts']) && !empty($post_data['lockAttempts'])) {
                    foreach ($post_data['lockAttempts'] as $row) {
                        $update_data = array(
                            'Value' => $row['Value'],
                        );
                        $this->db->where('ConfigurationId', $row['ConfigurationId']);
                        $res = $this->db->update('tblmstconfiguration', $update_data);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                    }
                }

                if (isset($post_data['lockPeriod']) && !empty($post_data['lockPeriod'])) {
                    foreach ($post_data['lockPeriod'] as $row) {
                        $update_data = array(
                            'Value' => $row['Value'],
                        );
                        $this->db->where('ConfigurationId', $row['ConfigurationId']);
                        $res = $this->db->update('tblmstconfiguration', $update_data);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                    }
                }

                if (isset($post_data['DocumentBasePath']) && !empty($post_data['DocumentBasePath'])) {
                    foreach ($post_data['DocumentBasePath'] as $row) {
                        $update_data = array(
                            'DisplayText' => $row['DisplayText'],
                        );
                        $this->db->where('ConfigurationId', $row['ConfigurationId']);
                        $res = $this->db->update('tblmstconfiguration', $update_data);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                    }
                }
                if ($res) {
                    $this->responseArr['status'] = 'success';
                    $this->responseArr['message'] = '';
                    $this->responseArr['data'] = true;
                    return $this->responseArr;
                } else {
                    $this->responseArr['status'] = 'fail';
                    return $this->responseArr;
                }
            } else {
                $this->responseArr['status'] = 'fail';
                return $this->responseArr;
            }
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

}
