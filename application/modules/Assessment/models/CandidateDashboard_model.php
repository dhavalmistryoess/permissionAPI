<?php

class CandidateDashboard_model extends CI_Model {

    protected $responseArr;

    function __construct() {
        parent::__construct();
        $this->responseArr['status'] = 'success';
        $this->responseArr['message'] = '';
        $this->responseArr['data'] = '';
    }

    /*     * *** Get All Countries **** */

    public function getAllDefaultList($UserId = Null) {
        try {
            $PaymentStatus = ['CertificateStatusId'];
            $Configuration_data = array();
            foreach ($PaymentStatus as $row) {
                $Certi_data = array(
                    'keyName' => $row,
                    'ModuleId' => 2
                );
                $result1 = $this->db->query('call GetConfigurationDataByKey(?,?)', $Certi_data);
                mysqli_next_result($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                foreach ($result1->result() as $row1) {
                    array_push($Configuration_data, $row1);
                }
            }
          
            $result = $this->db->query('SELECT cd.CertificateId,cd.CertificateName FROM tblusercertificates as ucert
					left join tblmstcertificates cd on cd.CertificateId=ucert.CertificateId where ucert.UserId = ' . $UserId . ' order BY cd.CertificateName ASC');
            $db_error = $this->db->error();
            if (!empty($db_error) && $db_error['message'] != "") {
                throw new Exception($db_error['message']);
            }
            $res1 = array();
            if ($result->result()) {
                $res1 = $result->result();
            }
            $res['Certificates'] = $res1;
            $res['Configuration'] = $Configuration_data;

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

    /*     * *** Add/Update Department **** */

    public function ScheduleAssessment($post_Schedule) {
        try {
            if ($post_Schedule) {
                if ($post_Schedule['IsDocumentVerificationRequired'] == 1) {
                    $checkQuery = $this->db->query("select DocumentVerificationStatusId from tblusercertificates where UserCertificateId = " . trim($post_Schedule['UserCertificateId']));
                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
                    }
                   
                    $checkResult = $checkQuery->row()->DocumentVerificationStatusId;
                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
                    }
                    $checkQuery2 = $this->db->query("select AddressId from tbladdresses where ReferenceId = " . trim($post_Schedule['UserId']));
                    $num = $checkQuery2->num_rows();

                    if ($num == 0) {
                        $this->responseArr['status'] = 'success';
                        $this->responseArr['message'] = '';
                        $this->responseArr['data'] = 'Address not available';
                        return $this->responseArr;
                    }
                }
                $Query = $this->db->query("Select count(UserAssessmentId) as assessmentAttempt,ResultStatus from tbluserassessment where UserCertificateId = " . $post_Schedule['UserCertificateId'] . " group by ResultStatus");
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $result = $Query->result();

                if ($result == null) {
                    $AssessmentAttempt = 1;
                } else {
                    foreach ($result as $row) {
                        $ResultStatus = $row->ResultStatus;
                        if ($ResultStatus == 17) {
                            $AssessmentAttempt = $row->assessmentAttempt + 1;
                        }
                    }
                }

                $Query2 = $this->db->query("Select count(ScheduleAttempt) as ScheduleAttempt from tblscheduleassessmenthistory where UserCertificateId = " . $post_Schedule['UserCertificateId'] . " and AssessmentAttempt = " . $AssessmentAttempt);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $result2 = $Query2->row();
                if ($result2 == null) {
                    $ScheduleAttempt = 1;
                } else {
                    $ScheduleAttempt = $result2->ScheduleAttempt + 1;
                }

                $Schedule_data = array(
                    'CertificateId' => trim($post_Schedule['CertificateId']),
                    'CandidateId' => trim($post_Schedule['UserId']),
                    'UserCertificateId' => trim($post_Schedule['UserCertificateId']),
                    'AvailablePriorityDate1' => trim($post_Schedule['AvailablePriorityDate1']),
                    'AvailablePriorityDate2' => trim($post_Schedule['AvailablePriorityDate2']),
                    'AvailablePriorityDate3' => trim($post_Schedule['AvailablePriorityDate3']),
                    'ScheduleAttempt' => $ScheduleAttempt,
                    'AssessmentAttempt' => $AssessmentAttempt
                );
                $res = $this->db->query('call ScheduleAssessment(?,?,?,?,?,?,?,?)', $Schedule_data);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                if ($res) {
                    $this->responseArr['status'] = 'success';
                    $this->responseArr['message'] = '';
                    $this->responseArr['data'] = 'true';
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

    /*     * *** Get Department by Department_Id **** */

    public function getById($UserId = NULL) {
        try {
            if ($UserId) {
                $data = array(
                    'Id' => $UserId,
                    'Flag' => 1
                );
                $result = $this->db->query('call GetUserCertificateDetails(?,?)', $data);
                 mysqli_next_result($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $UserCertificates_data = array();
                $UserCertificates_data = $result->result();
               
                $this->responseArr['status'] = 'success';
                $this->responseArr['message'] = '';
                $this->responseArr['data'] = $UserCertificates_data;
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

    /*     * *** Get certificate new list **** */

    public function getAllCertificateList($UserId = NULL) {
        try {
            if ($UserId) {
                $result = $this->db->query('call GetNewCertificatesById(?)', $UserId);
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

    /*     * *** Get Department by Department_Id **** */

    public function getCertificateDetailsById($UserCertificateId = NULL) {
        try {
            if ($UserCertificateId) {
                $Certificate_data = array();
                $data = array(
                    'Id' => $UserCertificateId,
                    'Flag' => 0
                );
                $result = $this->db->query('call GetUserCertificateDetails(?,?)', $data);
                mysqli_next_result($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                //$Certificate_data['UserCertificateDetail'] = $result->result();
               
                $confiQuery = $this->db->query("SELECT Value FROM tblmstconfiguration where `Key` = 'AppointmentCancelDays'")->row();
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $Certificate_data['AppointmentCancelDays'] = $confiQuery->Value; //get attempts from configuration 

                foreach ($result->result() as $row) {
                    $result1 = $this->db->query('Select cam.*,mc.CategoryName,mc.Description from tblmstcertificateassessmentmapping as cam left join tblmstcertificates c on c.CertificateId = cam.CertificateId left join tblusercertificates uc on uc.CertificateId = c.CertificateId and uc.UserCertificateId = '.$row->UserCertificateId.' left join tblmstcategory mc on mc.CategoryId = cam.CategoryId  where cam.CreatedOn < uc.CreatedOn and cam.IsActive=1;');
                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
                    }
                    $row->Categories = $result1->result();

                    if ($row->HasProctor == 0) {
                        $result2 = $this->db->query('call GetAssessmentDetailsById(?)', $row->UserCertificateId);
                         mysqli_next_result($this->db->conn_id);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                        $AssessmentDetails = array();
                        //$row->AssessmentDetail = $result2->result();
                        $results = array();
                        $CertificateAssessmentMappingId = array();
                        foreach ($result2->result() as $row1) {
                            $result = $this->db->query('call getAssessmentResultById(?)', $row1->UserAssessmentId);
                            mysqli_next_result($this->db->conn_id);
                            $db_error = $this->db->error();
                            if (!empty($db_error) && $db_error['message'] != "") {
                                throw new Exception($db_error['message']);
                            }
//$count = 0;
                            // foreach($result->result() as $row){
                            // 	$count = $count + $row->totalAttemptedQuestions;
                            // }
                            $results = $result->result();
                            //array_push($data,$count);
                           
                            $query2 = $this->db->query('select ua.ParentAssessment from tbluserassessment ua where ua.UserAssessmentId = ' . $row1->UserAssessmentId);
                            $db_error = $this->db->error();
                            if (!empty($db_error) && $db_error['message'] != "") {
                                throw new Exception($db_error['message']);
                            }
                            $ParentAssessment = $query2->row()->ParentAssessment;

                            if ($ParentAssessment != 0) {
                                foreach ($result->result() as $row3) {
                                    array_push($CertificateAssessmentMappingId, $row3->CertificateAssessmentMappingId);
                                }
                                $query3 = $this->db->query('call getAssessmentResultById(?)', $ParentAssessment);
                                mysqli_next_result($this->db->conn_id);
                                $db_error = $this->db->error();
                                if (!empty($db_error) && $db_error['message'] != "") {
                                    throw new Exception($db_error['message']);
                                }
                                foreach ($query3->result() as $row2) {
                                    if (!in_array($row2->CertificateAssessmentMappingId, $CertificateAssessmentMappingId)) {
                                        //$count = $count + $row1->totalAttemptedQuestions;
                                        array_push($results, $row2);
                                    }
                                }
                            }
                            //$data['data']=$x;
                            $row1->results = $results;
                            array_push($AssessmentDetails, $row1);
                            //$row->AssessmentDetail = $result2->result();
                        }
                        $row->AssessmentDetail = $AssessmentDetails;
                    } else {
                        $AssessmentDetails = array();
                        $result3 = $this->db->query('call GetScheduleDetailsById(?)', $row->UserCertificateId);
                        mysqli_next_result($this->db->conn_id);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                        //$count = 0;
                        foreach ($result3->result() as $row1) {
                            $Query1 = $this->db->query("select sah.ScheduleAssessmentHistoryId,sah.AvailablePriorityDate1,sah.AvailablePriorityDate2,sah.AvailablePriorityDate3,sah.ScheduleAttempt,sah.AssessmentAttempt,sah.Comment as HistoryComment,co.DisplayText as ScheduleStatus,sah.IsActive as HistoryIsActive,sah.CreatedOn,sa.ScheduleAssessmentId,sa.AssignDate,sa.StartTime,sa.EndTime,sa.Comment as AssignComment,co1.DisplayText as ScheduleAssessmentStatus,sa.IsActive as AssignIsActive,ua.UserAssessmentId,ua.ParentAssessment,ua.TotalScore,ua.TimeOfAssessment from tblscheduleassessmenthistory as sah left join tblscheduleassessment sa on sa.ScheduleAssessmentHistoryId = sah.ScheduleAssessmentHistoryId left join tbluserassessment ua on ua.ScheduleAssessmentId = sa.ScheduleAssessmentId left join tblmstconfiguration as co on co.ConfigurationId = sah.ScheduleStatusId left join tblmstconfiguration as co1 on co1.ConfigurationId = sa.ScheduleAssessmentStatus where sah.UserCertificateId = " . $row->UserCertificateId . " order by sah.ScheduleAssessmentHistoryId desc");
                            $db_error = $this->db->error();
                            if (!empty($db_error) && $db_error['message'] != "") {
                                throw new Exception($db_error['message']);
                            }
                            $row1->TotalScheduleAssessment = $Query1->result();
                            //array_push($AssessmentDetails,$row1);

                            if ($row1->UserAssessmentId != null) {
                                $result = $this->db->query('call getAssessmentResultById(?)', $row1->UserAssessmentId);
                                mysqli_next_result($this->db->conn_id);
                                $db_error = $this->db->error();
                                if (!empty($db_error) && $db_error['message'] != "") {
                                    throw new Exception($db_error['message']);
                                }
//$count = 0;
                                // foreach($result->result() as $row){
                                // 	$count = $count + $row->totalAttemptedQuestions;
                                // }
                                $results = $result->result();


                                //array_push($data,$count);
                                $query2 = $this->db->query('select ua.ParentAssessment from tbluserassessment ua where ua.UserAssessmentId = ' . $row1->UserAssessmentId);
                                $db_error = $this->db->error();
                                if (!empty($db_error) && $db_error['message'] != "") {
                                    throw new Exception($db_error['message']);
                                }
                                $ParentAssessment = $query2->row()->ParentAssessment;

                                if ($ParentAssessment != 0) {
                                    $CertificateAssessmentMappingId = array();
                                    foreach ($result->result() as $row3) {
                                        array_push($CertificateAssessmentMappingId, $row3->CertificateAssessmentMappingId);
                                    }
                                    $query3 = $this->db->query('call getAssessmentResultById(?)', $ParentAssessment);
                                    mysqli_next_result($this->db->conn_id);
                                    $db_error = $this->db->error();
                                    if (!empty($db_error) && $db_error['message'] != "") {
                                        throw new Exception($db_error['message']);
                                    }
                                    foreach ($query3->result() as $row2) {
                                        if (!in_array($row2->CertificateAssessmentMappingId, $CertificateAssessmentMappingId)) {
                                            //$count = $count + $row1->totalAttemptedQuestions;
                                            array_push($results, $row2);
                                        }
                                    }
                                }
                                //$data['data']=$x;
                                $row1->results = $results;
                            }
                            array_push($AssessmentDetails, $row1);
                        }
                        $row->AssessmentDetail = $AssessmentDetails;
                    }

                    $Query = $this->db->query("select upt.* from tbluserpracticetest as upt where upt.UserCertificateId = " . $row->UserCertificateId);
                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
                    }
                    $row->TotalPracticeTest = $Query->result();
                    $certificate_param = array(
                        'UserCertificateId' => $row->UserCertificateId,
                        'CertificateId' => $row->CertificateId,
                        'CertificateFor' => $row->CertificateFor
                    );
                    $result4 = $this->db->query('call GetDocumentsByUserCertificateId(?,?,?)', $certificate_param);
                     mysqli_next_result($this->db->conn_id);
                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
                    }
                    $OptionalDocuments = array();
                    $MandatoryDocuments = array();
                    foreach ($result4->result() as $row2) {
                        if ($row2->IsMandatory == 0) {
                            array_push($OptionalDocuments, $row2);
                        } else {
                            array_push($MandatoryDocuments, $row2);
                        }
                    }
                    $row->OptionalDocuments = $OptionalDocuments;
                    $row->MandatoryDocuments = $MandatoryDocuments;
                    array_push($Certificate_data, $row);

                    $checkQuery2 = $this->db->query("select ad.AddressId from tbladdresses as ad LEFT JOIN tblusercertificates as uc on uc.UserId = ad.ReferenceId where uc.UserCertificateId =" . trim($UserCertificateId) . ' AND ad.AddressTypeId = 36 AND ad.ReferenceTable = "tblusers"');
                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
                    }
                    $row->addressCount = $checkQuery2->num_rows();
                    // $row->CertificateDocuments = $result4->result();
                    // array_push($Certificate_data,$row);
                    // if($result->result()){
                }

                // 	$Schedule_data = $result->result();
                // }
                $this->responseArr['status'] = 'success';
                $this->responseArr['message'] = '';
                $this->responseArr['data'] = $Certificate_data;
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

    public function getAppointmentletter($UserCertificateId) {
        try {
            if (isset($UserCertificateId) && $UserCertificateId != '') {

                // getting company details for receipt header section from configuration table 
                $my_company_details = $this->db->where(array('ModuleId' => '1'))->get('tblmstconfiguration')->result();
                $dataset = array(
                    'CompanyName' => '',
                    'CompanyLogo' => '',
                    'CompanyAddress' => '',
                    'CompanyContactNumber' => '',
                    'CompanyEmailAddress' => '',
                    'TaxationInPercentage' => ''
                );
                foreach ($my_company_details as $rs) {

                    if ($rs->Key == 'CompanyName') {
                        $dataset['CompanyName'] = $rs->Value;
                    }
                    if ($rs->Key == 'CompanyLogo') {
                        $dataset['CompanyLogo'] = $rs->Value;
                    }
                    if ($rs->Key == 'ContactAddress') {
                        $dataset['CompanyAddress'] = $rs->Value;
                    }
                    if ($rs->Key == 'ContactPhoneNumber') {
                        $dataset['CompanyContactNumber'] = $rs->Value;
                    }
                    if ($rs->Key == 'ContactEmail') {
                        $dataset['CompanyEmailAddress'] = $rs->Value;
                    }
                    if ($rs->Key == 'TaxationInPercentage') {
                        $dataset['TaxationInPercentage'] = $rs->Value;
                    }
                }
               
                // getting order payment details
                $result = $this->db->query('call getAppointmentLetter(?)', $UserCertificateId);
                mysqli_next_result($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                $invoice_dataset = array();
                if ($result->result()) {
                    $invoice_dataset = $result->result();
                }

                if (count($invoice_dataset) > 0) {

                    $data = (object) array_merge($dataset, (array) $invoice_dataset[0]);
                    $this->responseArr['status'] = 'success';
                    $this->responseArr['message'] = '';
                    $this->responseArr['data'] = $data;
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

    public function CancelAssessment($post_Assessment) {
        try {
            if ($post_Assessment) {
                $data = array(
                    'ScheduleAssessmentId' => $post_Assessment['ScheduleAssessmentId'],
                    'ScheduleAssessmentStatus' => 56,
                    'Comment' => $post_Assessment['CancelComment'],
                    'ProctorId' => $post_Assessment['UserId'],
                    'UserId' => $post_Assessment['UserId'],
                );
                $result = $this->db->query('CALL UpdateScheduleStatus(?,?,?,?,?)', $data);
                mysqli_more_results($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                if($result){
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

    public function DocumentVerificationRequest($RequestData) {
        try {
            if ($RequestData) {
                $updateQuery = $this->db->query('update tblusercertificates set DocumentVerificationStatusId = 54 where UserCertificateId = ' . trim($RequestData['UserCertificateId']));
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                if ($updateQuery) {
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

    public function searchCertificate($orderHistory) {
        $CertificateId = !empty($orderHistory['CertificateId']) ? implode(",", $orderHistory['CertificateId']) : '';
        $OrderDateFrom = isset($orderHistory['OrderDateFrom']) ? $orderHistory['OrderDateFrom'] : '';
        $OrderDateTo = isset($orderHistory['OrderDateTo']) ? $orderHistory['OrderDateTo'] : '';
        $PaymentStatus = !empty($orderHistory['statusid']) ? implode(",", $orderHistory['statusid']) : '';
        $UserId = $orderHistory['UserId'];
        $Schedule_data = array();
        try {

            if (isset($CertificateId) && $CertificateId != '' || isset($OrderDateFrom) && $OrderDateFrom != '' ||
                    isset($OrderDateTo) && $OrderDateTo != '' || isset($PaymentStatus) && $PaymentStatus != '') {

                $OrderDateFrom1 = date("Y-m-d", strtotime($OrderDateFrom));
                $OrderDateTo1 = date("Y-m-d", strtotime($OrderDateTo));

                $query = 'select uc.*,schis.ScheduleStatusId,schis.ScheduleStatusId,schass.ScheduleAssessmentStatus,mc2.DisplayText as CertificateStatusText,mc3.DisplayText AS DocumentVerificationStatus,c.CertificateName,c.ScheduleAfterDaysForCandidate,c.USDPrice,c.INRPrice,c.EURPrice,c.CertificationDuration,c.AssessmentDuration,c.BeforeRenewButtonDisplay,c.AfterRenewButtonDisplay,c.CoolingPeriod,c.HasSubCertificate,c.PracticeExamAttempts,c.ScheduleAfterDaysForCandidate,c.HasProctor,c.HasDisplayCandidateInfo,c.HasDisplayProctorInfo,c.RescheduleAssessment,c.IsDocumentVerificationRequired,
				(select COUNT(cam.CategoryId) as categories from tblmstcertificateassessmentmapping cam where cam.CertificateId = uc.CertificateId) as categories,
				(select count(mcam.CertificateAssessmentMappingId) as activeAssessmentCategories from tblmstcertificateassessmentmapping mcam where mcam.CertificateId = uc.CertificateId and mcam.IsActive=1) as activeAssessmentCategories,
				(select count(cptm.CertificatePracticeTestMappingId) as activePracticeCategories from tblmstcertificatepracticetestmapping cptm where cptm.CertificateId = uc.CertificateId and cptm.IsActive=1) as activePracticeCategories
				from tblusercertificates uc 
				LEFT JOIN tblmstcertificates c on uc.CertificateId = c.CertificateId 
				LEFT JOIN tbluserassessment usa on usa.UserCertificateId = uc.UserCertificateId 
				left join tblmstconfiguration mc2 on mc2.ConfigurationId = uc.CertificateStatus 
				LEFT JOIN tblmstconfiguration mc3 ON mc3.ConfigurationId = uc.DocumentVerificationStatusId 
				LEFT JOIN tblscheduleassessment schass ON schass.UserCertificateId = uc.UserCertificateId 
				LEFT JOIN tbluserorder uo ON uo.UserCertificateId = uc.UserCertificateId 
				LEFT JOIN tblscheduleassessmenthistory schis ON schis.ScheduleAssessmentHistoryId = schass.ScheduleAssessmentHistoryId 
	 
				where  uc.UserId =' . $UserId;

                if (isset($CertificateId) && $CertificateId != '') {
                    $query .= ' AND uc.CertificateId IN(' . $CertificateId . ')';
                }

                if (isset($PaymentStatus) && $PaymentStatus != '') {
                    $query .= ' AND (uc.CertificateStatus IN(' . $PaymentStatus . '))';
                }

                if (isset($OrderDateFrom) && $OrderDateFrom != '' || isset($OrderDateTo) && $OrderDateTo != '') {
                    $query .= ' AND ((uo.PaymentDate BETWEEN "' . $OrderDateFrom1 . '" AND "' . $OrderDateTo1;
                    $query .= '") OR (uc.CertificationEndDate BETWEEN "' . $OrderDateFrom1 . '" AND "' . $OrderDateTo1;
                    $query .= '") OR (uc.CertificationStartDate BETWEEN "' . $OrderDateFrom1 . '" AND "' . $OrderDateTo1 . '"))';
                }
                $query .= ' Group by uc.UserCertificateId';
                $result = $this->db->query($query);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                
                
                    if ($row->HasProctor == 0) {
                        $result1 = $this->db->query('call GetAssessmentDetailsById(?)', $row->UserCertificateId);
                        mysqli_next_result($this->db->conn_id);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                        $row->AssessmentDetail = $result1->result();
                    } else {
                        $result1 = $this->db->query('call GetScheduleDetailsById(?)', $row->UserCertificateId);
                        mysqli_next_result($this->db->conn_id);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                        $row->AssessmentDetail = $result1->result();
                        $Query1 = $this->db->query("select sah.ScheduleAssessmentHistoryId,sah.AvailablePriorityDate1,sah.AvailablePriorityDate2,sah.AvailablePriorityDate3,sah.ScheduleAttempt,sah.AssessmentAttempt,sah.Comment as HistoryComment,co.DisplayText as ScheduleStatus,sah.IsActive as HistoryIsActive,sah.CreatedOn,sa.ScheduleAssessmentId,sa.AssignDate,sa.StartTime,sa.EndTime,sa.Comment as AssignComment,co1.DisplayText as ScheduleAssessmentStatus,sa.IsActive as AssignIsActive,ua.UserAssessmentId,ua.TotalScore,ua.TimeOfAssessment from tblscheduleassessmenthistory as sah left join tblscheduleassessment sa on sa.ScheduleAssessmentHistoryId = sah.ScheduleAssessmentHistoryId left join tbluserassessment ua on ua.ScheduleAssessmentId = sa.ScheduleAssessmentId left join tblmstconfiguration as co on co.ConfigurationId = sah.ScheduleStatusId left join tblmstconfiguration as co1 on co1.ConfigurationId = sa.ScheduleAssessmentStatus where sah.UserCertificateId = " . $row->UserCertificateId . " order by sah.ScheduleAssessmentHistoryId desc");
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                        $row->TotalScheduleAssessment = $Query1->result();
                    }

                    $Query = $this->db->query("select upt.* from tbluserpracticetest as upt where upt.UserCertificateId = " . $row->UserCertificateId);
                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
                    }
                    $row->TotalPracticeTest = $Query->result();
                    array_push($Schedule_data, $row);
              
                $this->responseArr['status'] = 'success';
                $this->responseArr['message'] = '';
                $this->responseArr['data'] = $Schedule_data;
                return $this->responseArr;

            } else {
                $this->responseArr['status'] = 'success';
                $this->responseArr['message'] = '';
                $this->responseArr['data'] = $Schedule_data;
                return $this->responseArr;
            }
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $this->responseArr['status'] = 'exception';
            $this->responseArr['message'] = $e->getMessage();
            return $this->responseArr;
        }
    }

    /*     * *** Delete certificate  **** */

    public function DeleteCertificate($UserId = NULL, $UserCertificateId = Null) {
        try {
            if ($UserId) {
                $data = array(
                    'UserId' => $UserId,
                    'UserCertificateId' => $UserCertificateId
                );
                $result = $this->db->query('call DeleteCertificate(?,?)', $data);
                mysqli_more_results($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                if ($result) {
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
    public function buyNewPracticeTests($post_data){
        try {
            if ($post_data) {
                $Query1 = $this->db->query("select uc.BoughtNewPracticeTests from tblusercertificates as uc where uc.UserCertificateId = " . $post_data['UserCertificateId']);
                        $db_error = $this->db->error();
                        if (!empty($db_error) && $db_error['message'] != "") {
                            throw new Exception($db_error['message']);
                        }
                        $BoughtNewPracticeTests = $Query1->row()->BoughtNewPracticeTests + $post_data['BoughtNewPracticeTests'];
                $data = array(
                    'UserId' => $post_data['UserId'],
                    'UserCertificateId' => $post_data['UserCertificateId'],
                    'BoughtNewPracticeTests' => $BoughtNewPracticeTests,
                    'CertificateName' => $post_data['CertificateName']
                );
                $result = $this->db->query('call BuyNewPracticeTests(?,?,?,?)', $data);
                mysqli_more_results($this->db->conn_id);
                $db_error = $this->db->error();
                if (!empty($db_error) && $db_error['message'] != "") {
                    throw new Exception($db_error['message']);
                }
                if ($result) {
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
    /*     * *** add certificate  **** */

    public function AddCertificate($Certificate) {
        $UserId = $Certificate['UserId'];
        $CertificatesNewlist = $Certificate['CertificatesNewlist'];

        try {
            if ($UserId) {
                foreach ($CertificatesNewlist as $row) {
                    $Certificate_data = array(
                        'CertificateId' => $row,
                        'UserId' => $UserId,
                        'CertificateFor' => 0
                    );
                    $insert = $this->db->query('call AddCertificate(?,?,?)', $Certificate_data);
                    mysqli_more_results($this->db->conn_id);
                    $db_error = $this->db->error();
                    if (!empty($db_error) && $db_error['message'] != "") {
                        throw new Exception($db_error['message']);
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

}
