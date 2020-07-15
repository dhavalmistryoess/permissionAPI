<?php

class Password_model extends CI_Model {

    protected $responseArr;

    function __construct() {
        parent::__construct();
        $this->responseArr['status'] = 'success';
        $this->responseArr['message'] = '';
        $this->responseArr['data'] = '';
    }

    /*     * *** Request for reset to password using forgot password page **** */

    public function forgotPassword($post_pass) {
        try {
            if ($post_pass) {
                $this->db->select('UserId,FirstName,LastName,EmailAddress,ForgotPasswordCode');
                $this->db->where('EmailAddress', trim($post_pass['EmailAddress']));
                $this->db->where('IsActive', 1);
                $this->db->where('IsBan', 0);
                $this->db->limit(1);
                $this->db->from('tblusers');
                $query = $this->db->get();

                if ($query->num_rows() == 1) {
                    $pass_data = array(
                        'ForgotPasswordCode' => trim($post_pass['ForgotPasswordCode']),
                        'UpdatedOn' => date('y-m-d H:i:s')
                    );

                    $UserId = $query->row()->UserId;
                    $c = $this->db->query("SELECT lastAttempton FROM tblusers where `UserId` = '$UserId'")->result();
                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
                    }
                    $time_lastattempt1 = $c[0]->lastAttempton; //get time of last attempt from usertable

                    $c2 = $this->db->query("SELECT Value FROM tblmstconfiguration where `Key` = 'lockPeriod'")->result();
                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
                    }
                    $configration_period = $c2[0]->Value; //get configration_period from configuration 

                    $currentDate = strtotime($time_lastattempt1);
                    $futureDate = $currentDate + (60 * $configration_period);
                    $time_lastattempt = date("Y-m-d H:i:s", $futureDate);
                    date_default_timezone_set('Asia/Kolkata');
                    $Date = date('Y-m-d H:i:s');

                    if ($Date > $time_lastattempt) {
                        $this->db->where('EmailAddress', trim($post_pass['EmailAddress']));
                        $res = $this->db->update('tblusers', $pass_data);
                        if ($res) {
                            $pass = array();
                            foreach ($query->result() as $row) {
                                $pass = $row;
                            }
                            $res = InsertActivityLog(0, $query->row()->UserId, 'Forgot Password', 'Sent Password reset Link');
                            $this->responseArr['status'] = 'success';
                            $this->responseArr['message'] = '';
                            $this->responseArr['data'] = $query;
                            return $this->responseArr;
                        }
                    } else {
                        $this->responseArr['status'] = 'success';
                        $this->responseArr['message'] = '';
                        $this->responseArr['data'] = $time_lastattempt;
                        return $this->responseArr;
                    }

                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
                    }
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

    /*     * *** verify/check password reset link is already used or not **** */

    public function checkResetLink($post_passlink) {
        try {
            if ($post_passlink) {
                $this->db->select('UserId,ForgotPasswordCode');
                $this->db->where('UserId', trim($post_passlink['UserId']));
                $this->db->where('ForgotPasswordCode', trim($post_passlink['ForgotPasswordCode']));
                $this->db->limit(1);
                $this->db->from('tblusers');
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
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

    /*     * *** Reset password using password reset link **** */

    public function resetPassword($post_pass) {
        try {
            if ($post_pass) {
                $pass_data = array(
                    'Password' => md5(trim($post_pass['Password'])),
                    'ForgotPasswordCode' => '',
                    'UpdatedOn' => date('y-m-d H:i:s')
                );
                $this->db->where('UserId', trim($post_pass['UserId']));
                $res = $this->db->update('tblusers', $pass_data);
                if ($res) {
                    $res = InsertActivityLog(0, $post_pass['UserId'], 'Reset Password', 'Reset password using reset password link');
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

    /*     * *** change password **** */

    public function changePassword($post_password) {
        try {
            if ($post_password) {
                $this->db->select('UserId,Password,EmailAddress,FirstName');
                $this->db->where('UserId', trim($post_password['UserId']));
                $this->db->where('Password', md5(trim($post_password['OldPassword'])));
                $this->db->limit(1);
                $this->db->from('tblusers');
                $query = $this->db->get();
                if ($query->num_rows() == 1) {
                    $password_data = array(
                        'Password' => md5($post_password['Password']),
                        'UpdatedBy' => trim($post_password['UserId']),
                        'UpdatedOn' => date('y-m-d H:i:s')
                    );
                    $this->db->where('UserId', trim($post_password['UserId']));
                    $res = $this->db->update('tblusers', $password_data);
                    if ($res) {
                        $res = InsertActivityLog(0, $post_password['UserId'], 'Change Password', 'Change Password after Login');
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
