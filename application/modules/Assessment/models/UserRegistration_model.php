<?php

class UserRegistration_model extends CI_Model {

    protected $responseArr;

    function __construct() {
        parent::__construct();
        $this->responseArr['status'] = 'success';
        $this->responseArr['message'] = '';
        $this->responseArr['data'] = '';
    }

    /*     * *** User Registration **** */

    public function CheckEmail($post_data) {
        try {
            if ($post_data) {
                $this->db->select('UserId');
                $this->db->from('tblusers');
                $this->db->where('EmailAddress', trim($post_data['EmailAddress']));
                $this->db->limit(1);
                $query = $this->db->get();
                $num = $query->num_rows();
                if ($num > 0) {
                    $this->responseArr['status'] = 'success';
                    $this->responseArr['message'] = '';
                    $this->responseArr['data'] = 'EmailAddress exists';
                    return $this->responseArr;
                } else {
                    $this->responseArr['status'] = 'success';
                    $this->responseArr['message'] = '';
                    $this->responseArr['data'] = 'EmailAddress does not exists';
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

    public function activateAccount($UserId) {
        try {
            if ($UserId) {
                $query = $this->db->query("update tblusers u set u.IsActive = 1,u.VerifiedOn = now() where u.UserId = " . $UserId);
                if ($query) {
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

    public function Registration($post_Invitation) {
        try {
            if ($post_Invitation) {
                $invitation_data = array(
                    'RoleId' => trim($post_Invitation['RoleId']),
                    'CertificateId' => $post_Invitation['CertificateId'],
                    'FirstName' => ucfirst(trim($post_Invitation['FirstName'])),
                    'MiddleName' => isset($post_Invitation['MiddleName']) ? ucfirst(trim($post_Invitation['MiddleName'])) : '',
                    'LastName' => ucfirst(trim($post_Invitation['LastName'])),
                    'EmailAddress' => trim($post_Invitation['EmailAddress']),
                    'PhoneNumber' => trim($post_Invitation['PhoneNumber']),
                    'Password' => md5(trim($post_Invitation['Password'])),
                    'IsEmployee' => 0,
                    'RegistrationType' => trim($post_Invitation['RegistrationType']),
                    'ProctorPrice' => isset($post_Invitation['ProctorPrice']) ? trim($post_Invitation['ProctorPrice']) : ''
                );
                $res = $this->db->query('call UserRegistration(?,?,?,?,?,?,?,?,?,?,?,@UId)', $invitation_data);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $out_param_query = $this->db->query('select @UId as out_param;');
                // $out_param_query1 = $this->db->query('select @UCId as out_param;');
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                // $res3 = InsertNotification(1,0,'User Registration - '.$post_Invitation["EmailAddress"],'user/list',$post_Invitation['UserId']);               
                if ($out_param_query) {
                    $UserId = $out_param_query->row()->out_param;
                    $this->responseArr['status'] = 'success';
                    $this->responseArr['message'] = '';
                    $this->responseArr['data'] = $UserId;
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

    /*     * *** Update proctor & document **** */

    public function Updateproctor($post_Invitation) {
        try {
            if ($post_Invitation) {
                $UserId = $post_Invitation['UserId'];
                $certificateList = $post_Invitation['certificateList'];
                $CertificateFor = $post_Invitation['CertificateFor'];
                if ($certificateList) {
                    foreach ($certificateList as $row) {
                        $proctor_data = array(
                            'RoleId' => trim($post_Invitation['RoleId']),
                            'CertificateId' => $row,
                            'UserId' => $UserId,
                            'CertificateFor' => $CertificateFor
                        );
                        $res = $this->db->query('call BecomeAProctor(?,?,?,?,@UCId)', $proctor_data);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                    }
                } else {
                    $proctor_data = array(
                        'RoleId' => trim($post_Invitation['RoleId']),
                        'CertificateId' => 0,
                        'CertificateFor' => 0,
                        'UserId' => $UserId
                    );
                    $res = $this->db->query('call BecomeAProctor(?,?,?,?,@UCId)', $proctor_data);
                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
                    }
                }

                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }

                if ($UserId) {
                    $this->responseArr['status'] = 'success';
                    $this->responseArr['message'] = '';
                    $this->responseArr['data'] = $UserId;
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

    /*     * *** Add/Update Department **** */

    public function getAllDefaultList() {
        try {
            $result = $this->db->query('call GetRoles()');
            mysqli_next_result($this->db->conn_id);
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res1 = array();
            foreach ($result->result() as $row) {
                if ($row->RoleId == 2 || $row->RoleId == 3) {
                    array_push($res1, $row);
                }
            }
            
            $result2 = $this->db->query('call GetCertificates(?)', 1);
            mysqli_next_result($this->db->conn_id);
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res2 = array();
            if ($result2->result()) {
                $res2 = $result2->result();
            }
            $res['Roles'] = $res1;
            $res['Certificates'] = $res2;

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

    public function getAllcertificate($UserId, $CertificateFor) {
        try {
            $data = array(
                'UserId' => $UserId,
                'CertificateFor' => $CertificateFor
            );
            $result3 = $this->db->query('call GetCertificatesForBecomeAProctor(?,?)', $data);
            mysqli_next_result($this->db->conn_id);
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $New_data = array();
            if ($result3->result()) {
                $New_data = $result3->result();
            }
            $this->responseArr['status'] = 'success';
            $this->responseArr['message'] = '';
            $this->responseArr['data'] = $New_data;
            return $this->responseArr;
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

    /*     * *** Get Department by Department_Id **** */

    public function getById($CertificateId = Null, $CertificateFor = Null) {
        try {
            if ($CertificateId && $CertificateFor) {
                $data = array(
                    'CertificateId' => trim($CertificateId),
                    'CertificateFor' => trim($CertificateFor)
                );
                $result = $this->db->query('call GetDocumentsByCertificateId(?,?)', $data);
                mysqli_next_result($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $Document_data = array();
                //$Document_data = $result->result();
                foreach ($result->result() as $row) {
                    $row->DocumentType = explode(",", $row->DocumentType);
                    array_push($Document_data, $row);
                }
                $this->responseArr['status'] = 'success';
                $this->responseArr['message'] = '';
                $this->responseArr['data'] = $Document_data;
                return $this->responseArr;
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

    /*     * *** Get All Departments **** */

    public function getAllUsers($IsActive = Null) {
        try {
            if ($IsActive) {
                $Active = $IsActive == 1 ? 1 : 0;
                $result = $this->db->query('call GetAllUsers(?)', $Active);
                mysqli_next_result($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $res = array();
                $certificate_data = array();
                foreach ($result->result() as $row) {
                   
                    $this->db->select('uc.*,c.CertificateName');
                    $this->db->join('tblmstcertificates as c', 'c.CertificateId = uc.CertificateId', 'left');
                    $this->db->from('tblusercertificates as uc');
                    $this->db->where('uc.UserId', $row->UserId);
                    $query = $this->db->get();
                    foreach ($query->result() as $row1) {
                        $certificate_param = array(
                            'UserCertificateId' => $row1->UserCertificateId,
                            'CertificateId' => $row1->CertificateId,
                            'CertificateFor' => $row1->CertificateFor
                        );
                        $data = $this->db->query('call GetDocumentsByUserCertificateId(?,?,?)', $certificate_param);
                        mysqli_next_result($this->db->conn_id);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                        $row1->documents = $data->result();
                        array_push($certificate_data, $row1);
                    }
                    $row->CertificateData = $certificate_data;
                    array_push($res, $row);
                }
                $this->responseArr['status'] = 'success';
                $this->responseArr['message'] = '';
                $this->responseArr['data'] = $res;
                return $this->responseArr;
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

    /*     * *** Get All Departments **** */

    public function getUserCertificates($UserId = Null) {
        try {
            if ($UserId) {
                $data1 = array();
                $certificate_data = array();

                $query1 = $this->db->query('select u.UserId,u.RoleId,CONCAT(u.FirstName," ",u.LastName) as Name,u.EmailAddress,u.PhoneNumber,a.City,s.StateName from tblusers u left join tbladdresses a on u.UserId = a.ReferenceId left join tblmststate s on s.StateId = a.StateId and a.AddressTypeId = 36 and a.ReferenceTable = "tblusers" where u.UserId = ' . $UserId);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $data1 = $query1->row();

                $this->db->select('uc.*,c.CertificateName,co.DisplayText');
                $this->db->join('tblmstcertificates as c', 'c.CertificateId = uc.CertificateId', 'left');
                $this->db->join('tblmstconfiguration as co', 'co.ConfigurationId = uc.DocumentVerificationStatusId', 'left');
                $this->db->from('tblusercertificates as uc');
                $this->db->where('uc.UserId', $UserId);
                $this->db->order_by("uc.DocumentVerificationStatusId = '54'", 'DESC');
                $query = $this->db->get();
                foreach ($query->result() as $row1) {
                    $certificate_param = array(
                        'UserCertificateId' => $row1->UserCertificateId,
                        'CertificateId' => $row1->CertificateId,
                        'CertificateFor' => $row1->CertificateFor
                    );
                    $data = $this->db->query('call GetDocumentsByUserCertificateId(?,?,?)', $certificate_param);
                    mysqli_next_result($this->db->conn_id);
                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
                    }
                    $OptionalDocuments = array();
                    $MandatoryDocuments = array();
                    foreach ($data->result() as $row2) {
                        if ($row2->IsMandatory == 0) {
                            array_push($OptionalDocuments, $row2);
                        } else {
                            array_push($MandatoryDocuments, $row2);
                        }
                    }
                    $row1->OptionalDocuments = $OptionalDocuments;
                    $row1->MandatoryDocuments = $MandatoryDocuments;
                    array_push($certificate_data, $row1);
                    // $row1->documents = $data->result();
                    // array_push($certificate_data,$row1);
                }
                //array_push($data1,$certificate_data);
                $data1->CertificateData = $certificate_data;

                $result = $this->db->query('select ud.*,d.DocumentName from tbluserdocuments ud left join tbldocuments d on ud.DocumentId = d.DocumentId where ud.UserId = ' . $UserId . ' and ud.IsActive = 1');
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                if ($result->result()) {
                    $data1->UserAllDocuments = $result->result();
                }
                $this->responseArr['status'] = 'success';
                $this->responseArr['message'] = '';
                $this->responseArr['data'] = $data1;
                return $this->responseArr;
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

    public function VerifyDocuments($post_data) {
        try {
            if ($post_data) {
                if ($post_data['DocumentVerificationStatus'] == "verify") {
                    $DocumentVerificationStatusId = 53;
                } else {
                    $DocumentVerificationStatusId = 52;
                }
                if (isset($post_data['DocumentVerificationComment']) && !empty($post_data['DocumentVerificationComment'])) {
                    $DocumentVerificationComment = trim($post_data['DocumentVerificationComment']);
                } else {
                    $DocumentVerificationComment = null;
                }
                $data = array(
                    'DocumentVerificationStatusId' => $DocumentVerificationStatusId,
                    'DocumentVerificationComment' => $DocumentVerificationComment,
                    'UpdatedBy' => trim($post_data['UpdatedBy'])
                );

                $this->db->where('UserCertificateId', trim($post_data['UserCertificateId']));
                $query = $this->db->update('tblusercertificates', $data);
                if ($query) {
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

    public function VerifyDocument($UserDocumentCertificateMappingId = Null, $IsVerify = Null) {
        try {
            if ($UserDocumentCertificateMappingId) {
                $query = $this->db->query("update tbluserdocumentcertificatemapping set IsVerifyByAdmin = " . $IsVerify . " where UserDocumentCertificateMappingId = " . $UserDocumentCertificateMappingId);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                if ($this->db->affected_rows() > 0) {
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

    public function getAllPendingVerificationUserList($IsActive = Null) {
        try {
            if ($IsActive) {
                $Active = $IsActive == 1 ? 1 : 0;
                $data = array(
                    'Active' => $Active,
                    'Flag' => 1
                );
                $result = $this->db->query('call getAllPendingVerificationUserList(?,?)', $data);
                mysqli_next_result($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }

                $this->responseArr['status'] = 'success';
                $this->responseArr['message'] = '';
                $this->responseArr['data'] = $result->result();
                return $this->responseArr;
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

    public function savePersonalInfo($post_data) {

        echo "<xmp>";
        print_r($post_data);
        exit;
    }

    public function AddCertificate($Certificate) {
        $insert = true;
        $UserId = $Certificate['UserId'];
        $CertificateId = $Certificate['CertificateId'];
        $CertificateFor = $Certificate['CertificateFor'];
        try {
            if ($UserId) {
                if(!empty($CertificateId)) {
                    foreach ($CertificateId as $row) {
                        $Certificate_data = array(
                            'CertificateId' => $row,
                            'UserId' => $UserId,
                            'CertificateFor' => $CertificateFor
                        );
                        //$insert = $this->db->insert('tblusercertificates',$Certificate_data);
                        $insert = $this->db->query('call AddCertificate(?,?,?)', $Certificate_data);
                        mysqli_more_results($this->db->conn_id);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                    }
                }
                if ($insert) {
                    $this->responseArr['status'] = 'success';
                    $this->responseArr['message'] = '';
                    $this->responseArr['data'] = true;
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

    public function getAllCertificateList($UserId = NULL) {
        try {
            if ($UserId) {
                $result = $this->db->query('call GetCertificatesById(?)', $UserId);
                mysqli_next_result($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $New_data = array();
                if ($result->result()) {
                    $New_data = $result->result();
                }
                $this->responseArr['status'] = 'success';
                $this->responseArr['message'] = '';
                $this->responseArr['data'] = $New_data;
                return $this->responseArr;
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

    public function isBanByadmin($user_data) {
        try {
            if ($user_data) {
                $invitation_data = array(
                    'UserId' => trim($user_data['UserId']),
                    'IsBan' => trim($user_data['IsBan']),
                    'UpdatedBy' => trim($user_data['UpdatedBy'])
                );
                $res = $this->db->query('call IsBanByadmin(?,?,?)', $invitation_data);
                mysqli_more_results($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $this->responseArr['status'] = 'success';
                $this->responseArr['message'] = '';
                $this->responseArr['data'] = true;
                return $this->responseArr;
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
